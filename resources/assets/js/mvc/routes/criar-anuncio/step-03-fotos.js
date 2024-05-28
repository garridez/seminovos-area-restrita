module.exports.seletor = '.c-criar-anuncio.a-index';

module.exports.callback = ($) => {
    $('.step-container').on('steps-loaded', init);

    var DataLayerGTMPopulate = require('helpers/DataLayerGTMPopulate');
    $('.step-container').on('step:pre-exit:video', function () {
        if ($('#dados-basicos #flagCriando').val() == 1) {
            var ctx = $('.step-0, .step-1');
            DataLayerGTMPopulate(ctx, 'checkout_step_5');
        }
    });
    $('.step-container').on('step:pre-exit:plano', function () {
        if ($('#dados-basicos #flagCriando').val() == 1) {
            var ctx = $('.step-0, .step-1');
            DataLayerGTMPopulate(ctx, 'checkout_step_6');
        }
    });
};
function init() {
    var ctx = $('.step-fotos');
    if (!ctx.length) {
        return;
    }
    var sortablejs = require('sortablejs');
    var Compress = require('compress.js').default;

    var rotate = ['rotate(0deg)', 'rotate(90deg)', 'rotate(180deg)', 'rotate(270deg)'];

    var handle = '.btn-move';
    if (
        /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
    ) {
        handle = '.btn-move';
    }
    new sortablejs.Sortable($('.fotos-container > div')[0], {
        animation: 150,
        swap: true,
        handle: handle,
        onChange: function (/**Event*/ evt) {
            $('.fotos-container').data('reordanado', true);
        },
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
        //compressPhoto($(this).data('img-element'), this.files[0]);
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
                //compressPhoto(imgs.eq(i), file);
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
        var posicaoRotacao = imagem.data('posicao-rotacao');

        // Seta o numero de vezes que a imagem vai ser rotacionada em 90graus
        if (posicaoRotacao === 3) {
            posicaoRotacao = 0;
            imagem.data('posicao-rotacao', 0);
        } else {
            posicaoRotacao = posicaoRotacao + 1;
            imagem.data('posicao-rotacao', posicaoRotacao);
        }

        imagem.data('uploaded', false);
        imagem.data('force-process', true);

        imagem.css({
            transition: 'all 0.7s ease',
            '-webkit-transform': rotate[posicaoRotacao],
            '-moz-transform': rotate[posicaoRotacao],
            '-ms-transform': rotate[posicaoRotacao],
            '-o-transform': rotate[posicaoRotacao],
            transform: rotate[posicaoRotacao],
        });
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
    async function showPhoto(imgElement, file) {
        imgElement = $(imgElement);

        function setBackgroudImage(file) {
            var background = 'url("' + file + '")';

            if (imgElement.css('background-image') === background) {
                return;
            }
            imgElement.animate(
                {
                    opacity: 0,
                },
                200,
            );
            // Animação pra suavizar a transição
            setTimeout(function () {
                imgElement.css('background-image', background);
                setTimeout(function () {
                    imgElement.animate(
                        {
                            opacity: 1,
                        },
                        200,
                    );
                }, 200);
            }, 200);
        }

        if (file === undefined) {
            setBackgroudImage(imgElement.data('placeholder'));
            imgElement.data('file-data', false);
            return;
        }

        if (typeof file === 'string') {
            setBackgroudImage(file);
            return;
        }

        var fileDataUrl = await fileReaderPromise(file);
        if (typeof fileDataUrl !== 'string') {
            throw new Error('Não foi possível ler o arquivo');
            return;
        }
        //exibe os botões de ação
        imgElement.parents('.foto').find('.controls').removeClass('d-none');
        imgElement.parents('.foto').find('.btn-adicionar').addClass('d-none');

        setBackgroudImage(fileDataUrl);

        if (imgElement.data('idfoto')) {
            imgElement.data('delete', true);
        }

        imgElement.data('file-data', file).data('uploaded', false);

        var fileCompressed = await compressPhoto(file);

        if (fileCompressed) {
            imgElement.data('file-data', fileCompressed);
            var fileDataUrl = await fileReaderPromise(fileCompressed);
            setBackgroudImage(fileDataUrl);
        }

        var eventName = 'fotos:selecionada';
        var event = $.Event(eventName);
        event.target = imgElement.get(0);
        imgElement.trigger(eventName).closest('.fotos-container').trigger(event);
    }
    async function fileReaderPromise(file) {
        return new Promise(function (resolve, reject) {
            var reader = new FileReader();
            reader.onload = function (e) {
                resolve(e.target.result);
            };
            reader.readAsDataURL(file);
        });
    }
    async function compressPhoto(imageFile) {
        /**
         * Se for o Safari, não comprime
         * O Safari dá pau na hora de colocar a imagem gerada no FormData
         */
        if (navigator.userAgent.match(/Version\/[\d\.]+.*Safari/)) {
            return false;
        }

        return new Promise(async function (resolve, reject) {
            // Se o compress falhar, não tem problema. A imagem original já está settada para enviar
            try {
                if (imageFile.name.match(/.heic$/) !== null) {
                    var heic2any = require('components/heic2any');
                    var resultBlob = await heic2any({
                        blob: imageFile,
                        toType: 'image/jpg',
                    });
                    imageFile = new File([resultBlob], 'heic' + '.jpg', {
                        type: 'image/jpeg',
                        lastModified: new Date().getTime(),
                    });
                }
                var compress = new Compress();

                compress
                    .compress([imageFile], {
                        size: 4, // the max size in MB, defaults to 2MB
                        quality: 0.9, // the quality of the image, max is 1,
                        resize: true, // defaults to true, set false if you do not want to resize the image width and height
                        maxWidth: 1000,
                        maxHeight: 750,
                    })
                    .then(function (images) {
                        var img = images[0];
                        function dataURLtoFile(dataurl, filename) {
                            var arr = dataurl.split(','),
                                mime = arr[0].match(/:(.*?);/)[1],
                                bstr = atob(arr[1]),
                                n = bstr.length,
                                u8arr = new Uint8Array(n);
                            while (n--) {
                                u8arr[n] = bstr.charCodeAt(n);
                            }
                            return new File([u8arr], filename, { type: mime });
                        }

                        var file = dataURLtoFile(img.prefix + img.data, 'min_' + img.alt);
                        resolve(file);
                        var nF = new Intl.NumberFormat('pt-BR', {
                            maximumFractionDigits: 2,
                        }).format;
                        /** Debug * /
                    console.table({
                        startSize: nF(img.initialSizeInMb * 1000) + ' KB',
                        endSize: nF(img.endSizeInMb * 1000) + ' KB',
                        compressionCycles: img.iterations,
                        sizeReduced: nF(img.sizeReducedInPercent) + ' %',
                        fileName: img.alt,
                    });/**/
                    });
            } catch (e) {
                resolve(false);
                console.log('Falha ao compactar');
                console.log(e);
            }
        });
    }
}

function testImgUpload() {
    var inputFoto = $('.step-fotos').find('[name="foto"]');
    if (location.href.indexOf('insert') !== -1) {
        setTimeout(async function () {
            var list = [1, 2, 3, 4, 5];

            for (var key in list) {
                var i = (list[key] + '').padStart(2, 0);
                var url = `/sequenciais/${i}.jpg`;
                await loadURLToInputFiled(inputFoto[0], url);
                inputFoto.data('img-element', $('.display-img')[key]);
                $('[type="file"][name="foto"]').change();
            }
            setTimeout(function () {
                $('.btn-continuar').click();
            }, 100);

            return;
            console.log($('[type="file"][name="foto"]')[0].files);
            $('[type="file"][name="foto"]').change();
        }, 1000);
    }

    async function loadURLToInputFiled(input, url) {
        return new Promise(function (resolve) {
            getImgURL(url, (imgBlob) => {
                let fileName = 'hasFilename.jpg';
                let file = new File(
                    [imgBlob],
                    fileName,
                    { type: 'image/jpeg', lastModified: new Date().getTime() },
                    'utf-8',
                );
                let container = new DataTransfer();
                container.items.add(file);
                input.files = container.files;
                resolve();
            });
        });
    }
    function getImgURL(url, callback) {
        var xhr = new XMLHttpRequest();
        xhr.onload = function () {
            callback(xhr.response);
        };
        xhr.open('GET', url);
        xhr.responseType = 'blob';
        xhr.send();
    }
}
