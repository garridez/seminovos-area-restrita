module.exports.seletor = '.c-criar-anuncio.a-index';

module.exports.callback = ($) => {
    $('.step-container').on('steps-loaded', init);

    var DataLayerGTMPopulate = require('helpers/DataLayerGTMPopulate');
    $('.step-container').on('step:pre-exit:video', function(){
      if($('#dados-basicos #flagCriando').val() == 1){
        var ctx = $('.step-0, .step-1');
        DataLayerGTMPopulate(ctx,'checkout_step_5');
      }
    });
    $('.step-container').on('step:pre-exit:plano', function(){
      if($('#dados-basicos #flagCriando').val() == 1){
        var ctx = $('.step-0, .step-1');
        DataLayerGTMPopulate(ctx,'checkout_step_6');
      }
    });
};
function init() {
    var ctx = $('.step-fotos');
    if (!ctx.length) {
        return;
    }
    var sortablejs = require('sortablejs');
    var Compress = require('compress.js');
    var rotate = [
        'rotate(0deg)',
        'rotate(90deg)',
        'rotate(180deg)',
        'rotate(270deg)',
    ];

    var handle = '.btn-move, .foto-container, .controls';
    if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
      handle = '.btn-move';
    }
    new sortablejs.Sortable($('.fotos-container > div')[0], {
        animation: 150,
        swap: true,
        handle: handle,
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

        //oculta os botões de ação
        $(this).closest('.foto').find('.controls').addClass('d-none');
        $(this).closest('.foto').find('.btn-adicionar').removeClass('d-none');

        img.data('delete', true);
        showPhoto(img);
    });
    ctx.on('click', '.btn-to-rotate', function (e) {
        e.preventDefault();
        var imagem = $(this).closest('.foto').find('.display-img');
        var posicaoRotacao = imagem.data('posicaoRotacao')

        // Seta o numero de vezes que a imagem vai ser rotacionada em 90graus
        if(posicaoRotacao === 3){
            posicaoRotacao = 0
            imagem.data('posicaoRotacao', 0);

        }else {
            posicaoRotacao = posicaoRotacao + 1;
            imagem.data('posicaoRotacao', posicaoRotacao);
        }

        imagem.css({
            'transition': 'all 0.7s ease',
            '-webkit-transform': rotate[posicaoRotacao],
            '-moz-transform': rotate[posicaoRotacao],
            '-ms-transform': rotate[posicaoRotacao],
            '-o-transform': rotate[posicaoRotacao],
            'transform': rotate[posicaoRotacao],
        })

    })
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

            //exibe os botões de ação
            imgElement.parents('.foto').find('.controls').removeClass('d-none');
            imgElement.parents('.foto').find('.btn-adicionar').addClass('d-none');

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
        /**
         * Se for o Safari, não comprime
         * O Safari dá pau na hora de colocar a imagem gerada no FormData
         */
        if (!!navigator.userAgent.match(/Version\/[\d\.]+.*Safari/)) {
            return;
        }

        // Se o compress falhar, não tem problema. A imagem original já está settada para enviar
        try {
            var compress = new Compress();

            compress.compress([imageFile], {
                size: 4, // the max size in MB, defaults to 2MB
                quality: 0.9, // the quality of the image, max is 1,
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

                var file = dataURLtoFile(img.prefix + img.data, 'min_' + img.alt);
                showPhoto(imgElement, file);
                /** Debug * /
                 console.log(`<b>Start Size:</b> ${img.initialSizeInMb} MB <br/>`
                 + `<b>End Size:</b> ${img.endSizeInMb} MB <br/>`
                 + `<b>Compression Cycles:</b> ${img.iterations} <br/>`
                 + `<b>Size Reduced:</b> ${img.sizeReducedInPercent} % <br/>`
                 + `<b>File Name:</b> ${img.alt}`);/**/
            });
        } catch (e) {

        }
    }
}
