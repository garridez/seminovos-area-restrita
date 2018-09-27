/**
 * Aqui é manipulado toda a parte de upload das imagens
 */
module.exports.seletor = '.c-criar-anuncio.a-index';

module.exports.callback = ($) => {
    $('.step-container').on('step:pre-exit:fotos', function (e) {
        var imgs = $('.fotos-container img')
                // Filtra deixando só as tags que contém uma imagem
                .filter(function () {
                    return !!$(this).data('file-data');
                })
                // Filtras as imagens que já foram carregadas
                .filter(function () {
                    return $(this).data('uploaded') !== true;
                });
        // Se zero, não tem nenhuma foto pra subir, então deixa passar pra próxima step
        if (!imgs.length) {
            return true;
        }

        var formData = new FormData();
        imgs.each(function () {
            formData.append('fotos[]', $(this).data('file-data'));
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
            success: function (data) {
                // Marca as imagens como "já carregadas"
                imgs.data('uploaded', true);
                $('.fotos-container')
                        .closest('.step-container')
                        .stepPlugin('next');
            }
        });
        return false;
    });
};