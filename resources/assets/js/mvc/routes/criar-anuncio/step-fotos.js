module.exports.seletor = '.c-criar-anuncio.a-index';

module.exports.callback = ($) => {
    $('.step-container').on('steps-loaded', init);
};

function init() {
    var ctx = $('.step-fotos');
    var inputFoto = ctx.find('[name="foto"]');



    ctx.find('.fotos-container').on('click', 'img', function (e) {
        e.preventDefault();
        inputFoto.data('img-element', this);
        inputFoto[0].click();
        return false;
    });
    inputFoto.change(function () {
        showPhoto($(this).data('img-element'), this.files[0]);
    });
    ctx.find('[name="fotos"]') // Input de multiplos arquivos
            .change(function () {
                var imgs = $('.fotos-container img');
                // Reseta as imagens
                imgs.each(function () {
                    $(this).attr('src', $(this).data('placeholder'));
                });
                $(this.files).each(function (i, file) {
                    showPhoto(imgs.eq(i), file);
                });
            });


    ctx.on('click', '.btn-remove-img', function () {
        // Seta o placeholder e limpa os metadados
        showPhoto($(this).closest('.foto').find('img'));
    });
    /**
     * Exibe a miniatura da imagem selecionada na tag img passada
     * Também adiciona metadados ao elemento img
     * 
     * @param HTMLImageElement imgElement
     * @param File file Imagem que será colocada no elemento IMG.
     *      Se não for passado, então é colocado no lugar o placeholder
     * @return void
     */
    function showPhoto(imgElement, file) {
        imgElement = $(imgElement);
        if (file === undefined) {
            imgElement.attr('src', imgElement.data('placeholder'))
                    .data('file-data', false);
            return;
        }
        var reader = new FileReader();
        reader.onload = function (e) {
            imgElement.attr('src', e.target.result)
                    .data('file-data', file)
                    .data('uploaded', false);
        };
        reader.readAsDataURL(file);
    }
}