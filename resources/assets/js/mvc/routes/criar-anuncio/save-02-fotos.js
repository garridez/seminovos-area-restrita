/**
 * Aqui é manipulado toda a parte de upload das imagens
 */
module.exports.seletor = '.c-criar-anuncio.a-index';

module.exports.callback = ($) => {
    var HandleApiError = require('components/HandleApiError');
    var BtnContinuar = require('./helpers/BtnContinuar');
    var loading = require('components/Loading');


    require('./save-02-fotos-v2')($);
    // Depois de testado, remover todo código abaixo
    return;
    $('.step-container').on('step:pre-exit:fotos', function (e) {
        var $fotosContainer = $('.fotos-container');
        var ordemCount = 0;
        BtnContinuar.disable();
        // Busca as imgs que serão feitas o upload
        var imgs = $fotosContainer.find('.display-img')
                // Seta a ordem como data
                .each(function () {
                    ordemCount++;
                    $(this).data('ordem', ordemCount);
                })
                // Filtra deixando só as tags que contém uma imagem
                .filter(function () {
                    return !!$(this).data('file-data');
                })
                // Filtras as imagens que já foram carregadas
                .filter(function () {
                    return $(this).data('uploaded') !== true;
                });
        var imgsToDelete = $fotosContainer.find('.display-img')
                // Filtra deixando só as tags que contém uma imagem
                .filter(function () {
                    return !!$(this).data('delete');
                })
                // Filtras as imagens que já foram deletadas
                .filter(function () {
                    return $(this).data('deleted') !== true;
                });


        var reordenar = $fotosContainer.data('reordanado') || false;
        var imgReorder = $fotosContainer.find('.display-img').filter(function () {
            return !!$(this).data('idfoto');
        });

        var existeImgRotacionada = false
        //controla se alguma imagem foi rotacionada
        $fotosContainer
            .find('.display-img')
            .each(function(k, v) {
                if($(v).data('posicao-rotacao') > 0){
                    existeImgRotacionada = true
                    return false
                }
            });
        // Se zero, não tem nenhuma foto pra subir ou excluir, então deixa passar pra próxima step
        if (!imgs.length && !imgsToDelete.length && !reordenar) {
            return true;
        }

        var formData = new FormData();
        imgs.each(function () {
            formData.append('ordem[]', $(this).data('ordem')); // Ordem para o upload
            formData.append('fotos[]', $(this).data('file-data'));
            // marca se imagens de upload serão rotacionadas
            formData.append('rotacionarNovasFotos[]',  $(this).data('posicao-rotacao'));
        });
        imgsToDelete.each(function () {
            formData.append('fotosToDelete[]', $(this).data('idfoto'));
        });
        imgReorder.each(function () {
            var ordem = $(this).data('ordem');
            formData.append('reordem[' + ordem + ']', $(this).data('idfoto')); // Reordena tudo
        });

        $('#dados-basicos form')
                .serializeArray()
                .forEach(function (e) {
                    formData.append(e.name, e.value);
                });
        loading.addFeedbackTexts([
            'Compactando as fotos...',
            'Guardando as fotos...',
            'Salvando...',
        ]);
        $.ajax({
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
                // Marca as imagens como "já carregadas"
                imgs.data('uploaded', true);
                imgsToDelete.data('deleted', true);
                $fotosContainer.data('reordanado', false)
                imgs.data('posicao-rotacao', 0);

                $('.fotos-container')
                        .closest('.step-container')
                        .stepPlugin('next');
            },
            error: function (e) {
                if (e.responseJSON) {
                    HandleApiError(e.responseJSON);
                } else {
                    HandleApiError(false);
                }
            }
        });
        return false;
    });
};
