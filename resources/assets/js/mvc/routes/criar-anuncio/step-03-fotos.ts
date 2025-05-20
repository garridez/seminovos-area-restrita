import Compress from 'compress.js';
import heic2any from 'heic2any';
import sortablejs from 'sortablejs';

import DataLayerGTMPopulate from '../../../helpers/DataLayerGTMPopulate';

export const seletor = '.c-criar-anuncio.a-index';

export const callback = ($: JQueryStatic) => {
    $('.step-container').on('steps-loaded', init);

    $('.step-container').on('step:pre-exit:video', function () {
        if ($('#dados-basicos #flagCriando').val() == 1) {
            const ctx = $('.step-0, .step-1');
            DataLayerGTMPopulate(ctx, 'checkout_step_5');
        }
    });
    $('.step-container').on('step:pre-exit:plano', function () {
        if ($('#dados-basicos #flagCriando').val() == 1) {
            const ctx = $('.step-0, .step-1');
            DataLayerGTMPopulate(ctx, 'checkout_step_6');
        }
    });
};
function init() {
    const ctx = $('.step-fotos');
    if (!ctx.length) {
        return;
    }

    const rotate = ['rotate(0deg)', 'rotate(90deg)', 'rotate(180deg)', 'rotate(270deg)'];

    let handle = '.btn-move';
    if (
        /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
    ) {
        handle = '.btn-move';
    }
    new sortablejs($('.fotos-container > div')[0], {
        animation: 150,
        swap: true,
        handle: handle,
        onChange: function () {
            $('.fotos-container').data('reordanado', true);
        },
    });

    const inputFoto = ctx.find<HTMLInputElement>('[name="foto"]');
    const inputFotosMulti = ctx.find<HTMLInputElement>('[name="fotos"]');

    ctx.find('.btn-excluir-todas-fotos').on('click', function () {
        if (!confirm('Deseja realmente excluir todas fotos?')) {
            return;
        }
        ctx.find('.btn-remove-img').trigger('click');
    });

    ctx.find('.fotos-container').on('click', '.display-img', function (e) {
        e.preventDefault();

        const totalImgs = $('.display-img').length;
        const totalImgsEmpty = $('.display-img').filter((i, e) => {
            return !$(e).data('file-data') && !$(e).data('idfoto');
        }).length;

        if (totalImgs === totalImgsEmpty) {
            inputFotosMulti[0].click();
            return false;
        }

        inputFoto.data('img-element', this);
        inputFoto[0].click();
        return false;
    });
    inputFoto.on('change', function () {
        if (this.files) {
            showPhoto($(this).data('img-element'), this.files[0]);
        }
        //compressPhoto($(this).data('img-element'), this.files[0]);
    });
    // Input de multiplos arquivos
    inputFotosMulti.on('change', function () {
        const imgs = $('.fotos-container .display-img');
        // Reseta as imagens
        imgs.each(function () {
            $(this).attr('src', $(this).data('placeholder'));
        });

        const files = this.files;
        if (files) {
            const arr = Array.from(files);
            arr.forEach(function (file, i) {
                showPhoto(imgs.eq(i), file);
                //compressPhoto(imgs.eq(i), file);
            });
        }
    });

    ctx.on('click', '.btn-upload-img', function (e) {
        e.preventDefault();
        // Trigga a imagem para abrir o upload
        $(this).closest('.foto').find('.display-img').trigger('click');
    });
    ctx.on('click', '.btn-remove-img', function (e) {
        e.preventDefault();
        // Seta o placeholder e limpa os metadados
        const img = $(this).closest('.foto').find('.display-img');

        //oculta os botões de ação
        $(this).closest('.foto').find('.controls').addClass('d-none');
        $(this).closest('.foto').find('.btn-adicionar').removeClass('d-none');

        img.data('delete', true);
        showPhoto(img);
    });
    ctx.on('click', '.btn-to-rotate', function (e) {
        e.preventDefault();
        const imagem = $(this).closest('.foto').find('.display-img');
        let posicaoRotacao = imagem.data('posicao-rotacao');

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
    async function showPhoto(imgElement: JQuery<HTMLElement>, file?: string | File) {
        imgElement = $(imgElement);

        function setBackgroudImage(file: string) {
            const background = 'url("' + file + '")';

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

        const fileDataUrl = await fileReaderPromise(file);
        if (typeof fileDataUrl !== 'string') {
            throw new Error('Não foi possível ler o arquivo');
        }
        //exibe os botões de ação
        imgElement.parents('.foto').find('.controls').removeClass('d-none');
        imgElement.parents('.foto').find('.btn-adicionar').addClass('d-none');

        setBackgroudImage(fileDataUrl);

        if (imgElement.data('idfoto')) {
            imgElement.data('delete', true);
        }

        imgElement.data('file-data', file).data('uploaded', false);

        const fileCompressed = await compressPhoto(file);

        if (fileCompressed) {
            imgElement.data('file-data', fileCompressed);
            const fileDataUrl = await fileReaderPromise(fileCompressed);
            if (fileDataUrl) {
                setBackgroudImage(fileDataUrl);
            }
        }

        const eventName = 'fotos:selecionada';
        const event = $.Event(eventName);
        //event.target = imgElement.get(0);
        imgElement.trigger(eventName).closest('.fotos-container').trigger(event);
    }
    async function fileReaderPromise(file: File) {
        return new Promise<string | null>(function (resolve) {
            const reader = new FileReader();
            reader.onload = function (e: ProgressEvent<FileReader>) {
                if (e.target) {
                    resolve(e.target.result as string | null);
                }
            };
            reader.readAsDataURL(file);
        });
    }
    async function compressPhoto(imageFile: File) {
        /**
         * Se for o Safari, não comprime
         * O Safari dá pau na hora de colocar a imagem gerada no FormData
         */
        if (navigator.userAgent.match(/Version\/[\d.]+.*Safari/)) {
            return false;
        }

        return new Promise<File | false>(function (resolve) {
            (async () => {
                // Se o compress falhar, não tem problema. A imagem original já está settada para enviar
                try {
                    if (imageFile.name.match(/.heic$/) !== null) {
                        const resultBlob = await heic2any({
                            blob: imageFile,
                            toType: 'image/jpg',
                        });
                        imageFile = new File(
                            Array.isArray(resultBlob) ? resultBlob : [resultBlob],
                            'heic' + '.jpg',
                            {
                                type: 'image/jpeg',
                                lastModified: new Date().getTime(),
                            },
                        );
                    }
                    const compress = new Compress();

                    compress
                        .compress([imageFile], {
                            size: 4, // the max size in MB, defaults to 2MB
                            quality: 0.9, // the quality of the image, max is 1,
                            resize: true,
                            maxWidth: 1000,
                            maxHeight: 750,
                        })
                        .then(function (images) {
                            const img = images[0];
                            function dataURLtoFile(dataurl: string, filename: string) {
                                const arr = dataurl.split(',');
                                const mime = (arr[0].match(/:(.*?);/) || [])[1];
                                const bstr = atob(arr[1]);
                                let n = bstr.length;
                                const u8arr = new Uint8Array(n);
                                while (n--) {
                                    u8arr[n] = bstr.charCodeAt(n);
                                }
                                return new File([u8arr], filename, { type: mime });
                            }

                            const file = dataURLtoFile(img.prefix + img.data, 'min_' + img.alt);
                            resolve(file);
                            const nF = new Intl.NumberFormat('pt-BR', {
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
            })();
        });
    }
}
/* eslint @typescript-eslint/no-unused-vars: 0 */
function testImgUpload() {
    const inputFoto = $('.step-fotos').find<HTMLInputElement>('[name="foto"]');
    if (location.href.indexOf('insert') !== -1) {
        setTimeout(async function () {
            const list = [1, 2, 3, 4, 5];

            for (const key in list) {
                const i = (list[key] + '').padStart(2, '0');
                const url = `/sequenciais/${i}.jpg`;
                await loadURLToInputFiled(inputFoto[0], url);
                inputFoto.data('img-element', $('.display-img')[key]);
                $('[type="file"][name="foto"]').trigger('change');
            }
            setTimeout(function () {
                $('.btn-continuar').trigger('click');
            }, 100);

            return;
            //console.log($('[type="file"][name="foto"]')[0].files);
            //$('[type="file"][name="foto"]').change();
        }, 1000);
    }

    async function loadURLToInputFiled(input: HTMLInputElement, url: string) {
        return new Promise(function (resolve) {
            getImgURL(url, (imgBlob: BlobPart) => {
                const fileName = 'hasFilename.jpg';
                const file = new File([imgBlob], fileName, {
                    type: 'image/jpeg',
                    lastModified: new Date().getTime(),
                });
                const container = new DataTransfer();
                container.items.add(file);
                input.files = container.files;
                resolve(true);
            });
        });
    }
    function getImgURL(url: string, callback: (e: BlobPart) => void) {
        const xhr = new XMLHttpRequest();
        xhr.onload = function () {
            callback(xhr.response);
        };
        xhr.open('GET', url);
        xhr.responseType = 'blob';
        xhr.send();
    }
}
