module.exports.seletor = '.c-criar-anuncio.a-index';

module.exports.callback = ($) => {
    $('.step-container').on('steps-loaded', init);
};

function init() {
    var ctx = $('.step-fotos');
    var inputFoto = ctx.find('[name="foto"]');



    ctx.find('.fotos-container').on('click', '.display-img', function (e) {
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
                var imgs = $('.fotos-container .display-img');
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
        var img = $(this).closest('.foto').find('.display-img');
        img.data('delete', true);
        showPhoto(img);
    });
    ctx.on('click', '.btn-restaurar-img', function () {
        // Seta o placeholder e limpa os metadados
        var displayImg = $(this).closest('.foto').find('.display-img');
        displayImg.removeData('delete');
        showPhoto(displayImg, displayImg.data('original'));
    });
    /**
     * Exibe a miniatura da imagem selecionada na tag img passada
     * Também adiciona metadados ao elemento img
     * 
     * @param HTMLElement imgElement
     * @param File file Imagem que será colocada no elemento IMG.
     *      Se não for passado, então é colocado no lugar o placeholder
     * @return void
     */
    function showPhoto(imgElement, file) {
        
        imgElement = $(imgElement);
        if (file === undefined) {
            file = imgElement.data('placeholder');
        }

        if (typeof file === 'string') {
            var background = 'url("' + file + '")';
            if (imgElement.css('background-image') === background) {
                return;
            }
            imgElement.animate({
                opacity: 0
            }, 200).data('file-data', false);
            // Animação pra suavizar a transição
            setTimeout(function () {
                imgElement.css('background-image', background);
                setTimeout(function () {
                    imgElement.animate({
                        opacity: 1
                    }, 200);
                }, 200);
            }, 200);
            return;
        }

        var reader = new FileReader();
        reader.onload = function (e) {
            if (imgElement.data('idfoto')) {
                imgElement.data('delete', true);
            }
            imgElement.css('background-image', 'url("' + e.target.result + '")')
                    .data('file-data', file)
                    .data('uploaded', false);
        };
        reader.readAsDataURL(file);
    }
}