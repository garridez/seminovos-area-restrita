/**
 * Aqui é manipulado toda a parte de upload das imagens
 */
module.exports.seletor = '.c-criar-anuncio.a-index';

module.exports.callback = ($) => {
    var HandleApiError = require('components/HandleApiError');
    var Loading = require('components/HandleApiError');
    var checkAndRedirect = function () {
        // Verifica se é necesário redirecionar
        if ($('.anuncio-steps').stepPlugin('inLastStep')) {
            Loading.open();
            window.location.href = '/meus-veiculos/' + $('#idVeiculo').val();
            return true;
        }
        return false;
    };
    $('.step-container').on('step:pre-exit:fotos', function (e) {
        var $fotosContainer = $('.fotos-container');
        var ordemCount = 0;
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

        // Se zero, não tem nenhuma foto pra subir ou excluir, então deixa passar pra próxima step
        if (!imgs.length && !imgsToDelete.length && !reordenar) {
            checkAndRedirect();
            return true;
        }

        var formData = new FormData();
        imgs.each(function () {
            formData.append('ordem[]', $(this).data('ordem')); // Ordem para o upload
            formData.append('fotos[]', $(this).data('file-data'));
        });
        imgsToDelete.each(function () {
            formData.append('fotosToDelete[]', $(this).data('idfoto'));
        });
        imgReorder.each(function () {
            var ordem = $(this).data('ordem');
            formData.append('reordem[' + ordem + ']',  $(this).data('idfoto')); // Reordena tudo
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
                $fotosContainer.data('reordanado', false)
                
                checkAndRedirect();

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