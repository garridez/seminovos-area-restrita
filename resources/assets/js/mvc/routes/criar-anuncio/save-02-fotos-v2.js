'use strict';

import $ from 'jquery';

import HandleApiError from '../../../components/HandleApiError';
import loading from '../../../components/Loading';
import BtnContinuar from './helpers/BtnContinuar';

function filterImgToUpload($img) {
    return (
        $img
            // Filtra deixando só as tags que contém uma imagem
            .filter(function () {
                return !!$(this).data('file-data');
            })
            // Filtras as imagens que já foram carregadas
            .filter(function () {
                return $(this).data('uploaded') !== true;
            })
    );
}

function filterImgToDelete($img) {
    return (
        $img
            // Filtra deixando só as tags que contém uma imagem
            .filter(function () {
                return !!$(this).data('delete');
            })
            // Filtras as imagens que já foram deletadas
            .filter(function () {
                return $(this).data('deleted') !== true;
            })
    );
}
function filterImgToReorder($img) {
    return $img
        .filter(function () {
            return !$(this).data('file-data');
        })
        .filter(function () {
            return !!$(this).data('idfoto');
        });
}

var ajaxAsyncCount = 0;

export default async function () {
    $('.step-container').on('steps-loaded', init);
}

async function init() {
    var $fotosContainer = $('.fotos-container');

    var countDelay = 0;
	var currentUploads = 0;

    $fotosContainer.find('.display-img').on('fotos:selecionada', function () {
        if (countDelay === 0) {
            countDelay++;
			currentUploads++;
            uploadImage(this, false, false);
            return;
        }
        countDelay++;
        currentUploads++;
        setTimeout(
            function () {
                uploadImage(this, false, false);
            }.bind(this),
            countDelay * 500,
        );
    });

    $('.step-container').on('step:pre-exit:fotos', function () {
        BtnContinuar.disable();

        var $displayImgs = $fotosContainer.find('.display-img');

        // O plugin Sortable seta como "true" o data "reordenado"
        var reordenar = $fotosContainer.data('reordanado') || false;

        var hasUpload = filterImgToUpload($displayImgs).length;
        var hasDelete = filterImgToDelete($displayImgs).length;
        var hasReorder = reordenar;

        if (!hasUpload && !hasDelete && !hasReorder) {
            return true;
        }

        var textAdicional = [];
        if (hasUpload) {
            textAdicional.push('Fazendo upload de <b>' + hasUpload + '</b> fotos');
        }
        if (hasDelete) {
            textAdicional.push('Apagando <b>' + hasDelete + '</b> fotos');
        }
        if (hasReorder) {
            textAdicional.push('Ordenando fotos');
        }

        loading.addFeedbackTexts([
            //            'Aguarde',
            'Processando fotos...',
            textAdicional.join('<br>'),
        ]);
        uploadProcessBatch();
        return false;
    });
    function setImagesOrder() {
        var $displayImgs = $fotosContainer.find('.display-img');
        $displayImgs.each(function (i) {
            $(this).data('ordem', i + 1);
        });
    }

    function awaitAjaxAsyncCount() {
        return new Promise(function (resolve) {
            var interval = setInterval(function () {
                if (ajaxAsyncCount === 0) {
                    clearInterval(interval);
                    resolve();
                }
            }, 100);
        });
    }

    async function uploadProcessBatch() {
        $.active = $.active || 1;
        loading.open(true);

        await awaitAjaxAsyncCount();

        var $fotosContainer = $('.fotos-container');
        var reordenar = $fotosContainer.data('reordanado') || false;
        var $displayImgs = $fotosContainer.find('.display-img');

        for (var img of $displayImgs) {
            await uploadImage(img, reordenar, false);
        }

        try {
            var idVeiculo = $('#dados-basicos form').find('#idVeiculo').val();
            if (idVeiculo) {
                $.get('/clear-cache/' + idVeiculo);
            }
        } catch (_e) {
            console.log('Erro no clear cache');
        }
        $.active = 1;
        $(document).triggerHandler('ajaxComplete', [{ status: 200 }]);

        //loading.close(true);

        console.log('subiu tudo!');

        $fotosContainer.data('reordanado', false);

        $('.fotos-container').closest('.step-container').stepPlugin('next');
    }

    async function uploadImage(img, reordenar = false, showLoading = true) {
        setImagesOrder();
        ajaxAsyncCount++;
		
		$('.btn-continuar').prop('disabled', true).html('Aguarde...') ;
		loading.open(true);
		
        var ajaxLoaddingBackup = window.setAjaxLoadding;
        window.setAjaxLoadding = showLoading;		

		let timeout = null;
		let watch = setInterval(function () {
			console.log('Count:', currentUploads);
			if (currentUploads <= 0) {
				if (!timeout) {
					timeout = setTimeout(function () {
						if (currentUploads <= 0) {
							console.log('Uploads finalizados. Count:', currentUploads);
							$('.btn-continuar').prop('disabled', false).html('Continue');
							loading.close(true);
							clearInterval(watch);
						} else {
							// Reinicia o timeout se algo novo começou a enviar
							clearTimeout(timeout);
							timeout = null;
						}
					}, 1000); // espera 1 segundo estável
				}
			} else {
				// Se algum upload começar, cancela a espera
				clearTimeout(timeout);
				timeout = null;
				
				$('.btn-continuar').prop('disabled', true).html('Aguarde...') ;
				loading.open(true);	
			}
		}, 200);

        var $img = $(img);
        var $containerFoto = $img.closest('.foto');

        $containerFoto.addClass('uploading');
        function removeLoading() {
            $containerFoto.removeClass('uploading');
            window.setAjaxLoadding = ajaxLoaddingBackup;
            ajaxAsyncCount--;
        }
        if ($img.data('force-process') === true) {
            $img.data('uploaded', false);
            $img.data('deleted', false);
            if ($img.data('idfoto')) {
                $img.data('delete', true);
            }
        }

        var $imgToUpload = filterImgToUpload($img);
        var $imgToDelete = filterImgToDelete($img);
        var $imgToReorder = filterImgToReorder($img);

        if (!$imgToUpload.length && !$imgToDelete.length && !reordenar) {
            removeLoading();
            return;
        }
        if (reordenar && !$imgToReorder.length && !$imgToUpload.length) {
            removeLoading();
            return;
        }

        var formData = new FormData();
        $('#dados-basicos form')
            .serializeArray()
            .forEach(function (e) {
                formData.append(e.name, e.value);
            });

        $imgToUpload.each(function () {
            formData.append('ordem[]', $(this).data('ordem')); // Ordem para o upload
            formData.append('fotos[]', $(this).data('file-data'));
            // marca se imagens de upload serão rotacionadas
            formData.append('rotacionarNovasFotos[]', $(this).data('posicao-rotacao'));
        });
        $imgToDelete.each(function () {
            formData.append('fotosToDelete[]', $(this).data('idfoto'));
        });
        $imgToReorder.each(function () {
            var ordem = $(this).data('ordem');
            formData.append('reordem[' + ordem + ']', $(this).data('idfoto')); // Reordena tudo
        });

        var dataNames = [];
        for (var i of formData.entries()) {
            dataNames.push(i);
        }

        try {
            await $.ajax({
                url: '/carro/fotos',
                data: formData,
                type: 'POST',
                contentType: false,
                processData: false,
                dataType: 'json',
                context: document,
                success: function (data) {
					currentUploads--;
					
                    if (!HandleApiError(data)) {
                        return;
                    }
                    console.log(data);
                    console.log('upload ok ' + currentUploads);
                    // Marca as imagens como "já carregadas"
                    $img.data('uploaded', true);
                    $imgToDelete.data('deleted', true);
                    if (
                        data.resUpload &&
                        data.resUpload.data &&
                        data.resUpload.data.fotosInseridas
                    ) {
                        $img.data('idfoto', data.resUpload.data.fotosInseridas[0].idFoto);
                    }
                },
                error: function (e) {
					currentUploads--;
					
                    if (e.responseJSON) {
                        HandleApiError(e.responseJSON);
                    } else {
                        HandleApiError(false);
                    }
                    console.log('upload com erro');
                },
            });
        } catch (_e) {
			currentUploads--;
            console.log('Deu erro');
        }
        removeLoading();
    }
}
