module.exports.seletor = '.c-criar-anuncio.a-index';

module.exports.callback = ($) => {
    $('.step-container').on('steps-loaded', init);
};

function init() {
    var ctx = $('.step-fotos');
    if (!ctx.length) {
        return;
    }
    var sortablejs = require('sortablejs');
    var Compress = require('compress.js');


    new sortablejs.Sortable($('.fotos-container > div')[0], {
        animation: 150,
        swap: true,
        handle: '.btn-move',
        onChange: function (/**Event*/evt) {
            $('.fotos-container').data('reordanado', true);
        }
    });

    var inputFoto = ctx.find('[name="foto"]');



    ctx.find('.fotos-container').on('click', '.display-img', function (e) {
        e.preventDefault();
        inputFoto.data('img-element', this);
        inputFoto[0].click();
        return false;
    });
    inputFoto.change(function () {
        showPhoto($(this).data('img-element'), this.files[0]);
        compressPhoto($(this).data('img-element'), this.files[0]);
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
                    compressPhoto(imgs.eq(i), file);
                });
            });


    ctx.on('click', '.btn-upload-img', function (e) {
        e.preventDefault();
        // Trigga a imagem para abrir o upload
        $(this).closest('.foto').find('.display-img').click();
    });
    ctx.on('click', '.btn-remove-img', function (e) {
        e.preventDefault();
        // Seta o placeholder e limpa os metadados
        var img = $(this).closest('.foto').find('.display-img');
        img.data('delete', true);
        showPhoto(img);
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
            showPhoto(imgElement, e.target.result);
            if (imgElement.data('idfoto')) {
                imgElement.data('delete', true);
            }
            imgElement
                    .data('file-data', file)
                    .data('uploaded', false);
        };
        reader.readAsDataURL(file);
    }
    function compressPhoto(imgElement, imageFile) {

        // Se o compress falhar, não tem problema. A imagem original já está settada para enviar
        try {
            var compress = new Compress();

            compress.compress([imageFile], {
                size: 4, // the max size in MB, defaults to 2MB
                quality: 0.8, // the quality of the image, max is 1,
                maxWidth: 600, // the max width of the output image, defaults to 1920px
                maxHeight: 600, // the max height of the output image, defaults to 1920px
                resize: true // defaults to true, set false if you do not want to resize the image width and height
            }).then(function (images) {
                var img = images[0];
                function dataURLtoFile(dataurl, filename) {
                    var arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
                            bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
                    while (n--) {
                        u8arr[n] = bstr.charCodeAt(n);
                    }
                    return new File([u8arr], filename, {type: mime});
                }

                var file = dataURLtoFile(img.prefix + img.data, 'min.jpg');
                showPhoto(imgElement, file);
                /** Debug
                 console.log(`<b>Start Size:</b> ${img.initialSizeInMb} MB <br/>`
                 + `<b>End Size:</b> ${img.endSizeInMb} MB <br/>`
                 + `<b>Compression Cycles:</b> ${img.iterations} <br/>`
                 + `<b>Size Reduced:</b> ${img.sizeReducedInPercent} % <br/>`
                 + `<b>File Name:</b> ${img.alt}`);*/
            });
        } catch (e) {

        }
    }
}