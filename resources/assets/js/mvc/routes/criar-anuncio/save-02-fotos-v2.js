function filterImgToUpload($img) {
    return $img
            // Filtra deixando só as tags que contém uma imagem
            .filter(function () {
                return !!$(this).data('file-data');
            })
            // Filtras as imagens que já foram carregadas
            .filter(function () {
                return $(this).data('uploaded') !== true;
            });
}

function filterImgToDelete($img) {
    return $img
            // Filtra deixando só as tags que contém uma imagem
            .filter(function () {
                return !!$(this).data('delete');
            })
            // Filtras as imagens que já foram deletadas
            .filter(function () {
                return $(this).data('deleted') !== true;
            });
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
module.exports = async function ($) {
    var HandleApiError = require('components/HandleApiError');
    var loading = require('components/Loading');
    var BtnContinuar = require('./helpers/BtnContinuar');


    $('.step-container').on('step:pre-exit:fotos', function (e) {
        var $fotosContainer = $('.fotos-container');
        BtnContinuar.disable();

        var $displayImgs = $fotosContainer.find('.display-img');

        // O plugin Sortable seta como "true" o data "reordenado"
        var reordenar = $fotosContainer.data('reordanado') || false;

        var hasUpload = filterImgToUpload($displayImgs).length;
        var hasDelete = filterImgToDelete($displayImgs).length;
        var hasReorder = reordenar;
        console.log({
            hasUpload,
            hasDelete,
            hasReorder
        });

        if (!hasUpload && !hasDelete && !hasReorder) {
            console.log('Nada pra fazer');
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
            textAdicional.join('<br>')
        ]);
        uploadProcess();
        return false;
    });

    async function uploadProcess() {
        console.log('Start upload')
        loading._persistent = true;
        var ordemCount = 0;
        var $fotosContainer = $('.fotos-container');
        var reordenar = $fotosContainer.data('reordanado') || false;
        var $displayImgs = $fotosContainer.find('.display-img');
        $('.fotos-container').find('.display-img').animate({opacity: .1});

//        var feedbackText = [];
        console.log($displayImgs);

        for (var img of $displayImgs) {
            var $img = $(img);
            ordemCount++;
            console.log('\n');
            console.log('ordemCount:', ordemCount);
            // Seta a ordem como data
            $img.data('ordem', ordemCount);

            var $imgToUpload = filterImgToUpload($img);
            var $imgToDelete = filterImgToDelete($img);
            var $imgToReorder = filterImgToReorder($img);

            if (!$imgToUpload.length && !$imgToDelete.length && !reordenar) {
                //console.log('nada pra fazer');
                $img.animate({opacity: 1});
                console.log('Sem upload, delete e reordenar');
                continue;
            }
            if (reordenar && !$imgToReorder.length && !$imgToUpload.length) {
                console.log('Mandou reordenar mas não tem nada');
                $img.animate({opacity: 1});
                continue;

            }
            if (reordenar && $imgToReorder.length) {
                console.log('reordenar');
            }
            ///console.log($imgToUpload, $imgToDelete, $imgToReorder, ordemCount);



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

            console.log(formData);


            var dataNames = [];
            for (var i of formData.entries()) {
                //console.log(i);
                dataNames.push(i);
            }
            console.log(dataNames);

            try {
                await $.ajax({
                    url: '/carro/fotos',
                    data: formData,
                    type: 'POST',
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function (data) {
                        if (!HandleApiError(data)) {
                            return;
                        }
                        console.log('upload ok');
                        // Marca as imagens como "já carregadas"
                        $img.data('uploaded', true);
                        $imgToDelete.data('deleted', true);

                        $img.data('posicao-rotacao', 0);

//                $('.fotos-container')
//                        .closest('.step-container')
//                        .stepPlugin('next');
                    },
                    error: function (e) {
                        if (e.responseJSON) {
                            HandleApiError(e.responseJSON);
                        } else {
                            HandleApiError(false);
                        }
                        console.log('upload com erro');
                    }
                });
            } catch (e) {
                console.log('Deu erro');
            }
            $img.animate({opacity: 1});
        }

        console.log('Terminou o loop');

        loading.close(true);



        console.log('subiu tudo!');

        $fotosContainer.data('reordanado', false);

        $('.fotos-container')
                .closest('.step-container')
                .stepPlugin('next');
    }



};

