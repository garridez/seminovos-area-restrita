/**
 * Aqui é manipulado toda a parte de upload das imagens
 */
module.exports.seletor = '.c-criar-anuncio.a-index';

module.exports.callback = ($) => {
    var HandleApiError = require('components/HandleApiError');
    $('.step-container').on('step:pre-exit:fotos', function (e) {
        var ordemCount = 0;
        // Busca as imgs que serão feitas o upload
        var imgs = $('.fotos-container .display-img')
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
        var imgsToDelete = $('.fotos-container .display-img')
                // Filtra deixando só as tags que contém uma imagem
                .filter(function () {
                    return !!$(this).data('delete');
                })
                // Filtras as imagens que já foram deletadas
                .filter(function () {
                    return $(this).data('deleted') !== true;
                });

        // Se zero, não tem nenhuma foto pra subir ou excluir, então deixa passar pra próxima step
        if (!imgs.length && !imgsToDelete.length) {
            return true;
        }

        var formData = new FormData();
        imgs.each(function () {
            formData.append('ordem[]', $(this).data('ordem'));
            formData.append('fotos[]', $(this).data('file-data'));
        });
        imgsToDelete.each(function () {
            formData.append('fotosToDelete[]', $(this).data('idfoto'));
        });
        $('#dados-basicos form')
                .serializeArray()
                .forEach(function (e) {
                    formData.append(e.name, e.value);
                });

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