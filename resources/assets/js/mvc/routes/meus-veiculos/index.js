module.exports.seletor = '.c-meus-veiculos.a-index';

module.exports.callback = ($) => {
    require('../../../components/JsBsModal');
    var advancedAlerts = require('../../../components/AdvancedAlerts').default;
    var Confirms = require('../../../components/Confirms').default;
    var FormAlerts = require('../../../components/FormAlerts').default;
    const jsCookie = require('js-cookie');

    modalRepasse();

    if ($('div[data-veiculo-finalizar]').length) {
        advancedAlerts.warning({
            title: 'Você possui anúncios não finalizados',
            text:
                'Você possui anúncios para concluir,<br/>' +
                ' conclua os anúncios com status <br/>CADASTRANDO ou CADASTRANDO GRÁTIS.',
            time: 12000,
        });
    }

    $('body')
        .on('click', 'a.reativar[data-confirm]', reativarDataConfirm)
        .on('click', 'a.renovar[data-confirm]', renovarDataConfirm)
        .on('click', 'a.anuncios[data-confirm]', anuncioDataConfirm)
        .on('click', 'a.vendido[data-confirm]', vendidoDataConfirm)
        // Configura os modais genericos
        .on('click', '.anuncios [data-modal]', anunciosModal)
        .on('click', '.sidebar-menu .menu-items .btn-novo-anuncio', function (e) {
            e.preventDefault();
            $('#modalTipoAnuncio').modal('show');
        })
        .on('click', '#modalTipoAnuncio .modal-body a', function (e) {
            e.preventDefault();

            var link = $(this).attr('href');
            var text = `A Seminovos <b class='text-primary'>NÃO </b>faz contato por
                  <b class='text-primary'>telefone </b> ou <b class='text-primary'>whatsapp </b>
                  solicitando código de verificação de anúncio ou similar.<br><br>
                  CUIDADO PARA NÃO CAIR EM GOLPES<br><br>
                  Estamos à disposição para esclarecer dúvidas<br>`;
            advancedAlerts
                .error({
                    text: text,
                    title: $('<span>').html(`<span class='text-primary'>Alerta </span>importante`),
                    time: false,
                    img: $('<img src="/img/svg/ico_irregularidade.svg" class="modal-img">'),
                    closeText: 'ESTOU CIENTE',
                    closeCallback: function () {
                        $('#modalTipoAnuncio').modal('hide');
                    },
                })
                .on('hide.bs.modal', function () {
                    window.location = link;
                });
        })
        .on('click', '.item-compartilhar', function () {;
            const marca = $(this).data('marca');
            const modelo = $(this).data('modelo');
            const idVeiculo = $(this).data('id-veiculo')
            if (navigator && navigator.share) {
                navigator
                    .share({
                        title: 'Veja este ' + marca + ' ' + modelo + ' que encontrei: ',
                        url: 'https://seminovos.com.br/' + idVeiculo,
                    })
                    .then(() => {
                        console.log('Thanks for sharing!');
                    })
                    .catch(console.error);
                return;
            }
            const $ctx = $(this).closest('.item-compartilhar');

            if (!$ctx.hasClass('show')) {
                $ctx.addClass('show');
                $('body').on(
                    'click.closeToolTipCompartilhar',
                    ':not(.item-compartilhar)',
                    function (e) {
                        if ($ctx.find($(e.target)).length > 0) {
                            return;
                        }
                        $ctx.removeClass('show');
                        $('body').off('click.closeToolTipCompartilhar');
                    },
                );
                return;
            }

            $('body').off('click.closeToolTipCompartilhar');
            $ctx.removeClass('show');
        });

    if (location.hash !== '' && window.URLSearchParams) {
        (function () {
            var hashParams = new URLSearchParams(location.hash.replace('#', '?'));
            if (!hashParams.has('idVeiculo')) {
                return;
            }
            var veiculoDiv = $('[data-id-veiculo="' + hashParams.get('idVeiculo') + '"].veiculo');
            var btnSeletor = false;
            switch (hashParams.get('acao')) {
                case 'vendido':
                    btnSeletor = '.btn-acao-vendido';
                    break;
                case 'reativar':
                    btnSeletor = '.btn-acao-reativar';
                    break;
            }

            if (btnSeletor !== false) {
                veiculoDiv.find(btnSeletor).click();
            }
        })();
    }
    /**
     * Filtra a listagem de anúncios quando loggado como revenda
     *
     */
    $('#plano, #staus').change(function () {
        $('.container-anuncios .anuncios .veiculo').each(function () {
            var $obj = $(this);
            let result2 = $obj.hasClass($('#status').val());
            let result = $obj.hasClass($('#plano').val());

            if (result && result2) {
                $obj.removeClass('hide').addClass('show');
            } else {
                $obj.removeClass('show').addClass('hide');
            }
        });
    });

    ///////////////// CALLBACKS /////////////////
    /**
     * Baixa o conteúdo da página atualizado
     * Baixa apenas o conteúdo dentro da div ".container-anuncios"
     */

    function reloadPageContent() {
        $.get('/', function (data) {
            $('.container-anuncios').replaceWith(data);
        });
        $.get('/meus-veiculos/qtd-anuncios-menu', function (data) {
            $('.qtd-anuncios-menu').html(data);
        });
    }

    function pesquisaSatisfacaoDataForm(veiculo) {
        var $form = $('<form>');
        var $span = $('<small class="bold text-primary">').html('Dê a sua opnião, é rapidinho!');
        var $select = $('<select name="vendaVeiculo" class="form-control" required>');
        var selectOptions = [
            { value: '', text: 'Selecione' },
            { value: '1', text: 'Vendi pela Seminovos BH' },
            { value: '2', text: 'Desisti de vender' },
            { value: '3', text: 'Vendi por outro meio' },
            { value: '4', text: 'Outro motivo' },
        ];
        selectOptions.forEach(function (e) {
            $select.append($(`<option value="${e.value}">${e.text}</option>`));
        });

        var $conjuntoSelect = $(
            '<div class="form-group d-flex align-items-center mt-4 required"></div>',
        )
            .append(
                $(
                    '<span class="no-wrap mr-3">Sobre a <b class="text-primary">venda do veículo</b>:</span>',
                ),
            )
            .append($select);

        var $rowColSelect = $(`
            <div class="row">
                <div class="col-12">
                    <div class="conjunto-select"/>
                </div>
            </div>`);

        $rowColSelect.find('.conjunto-select').replaceWith($conjuntoSelect);

        var estrelas = [
            { value: 5, text: 'Ótimo' },
            { value: 4, text: 'Bom' },
            { value: 3, text: 'Razoável' },
            { value: 2, text: 'Ruim' },
            { value: 1, text: 'Péssimo' },
        ];
        var $estrelas = $('<div class="rate">');
        estrelas.forEach(function (e) {
            $estrelas
                .append(
                    $(
                        `<input type="radio" id="star${e.value}" name="rate value="${e.value}" required>`,
                    ),
                )
                .append($(`<label for="star${e.value}" title="${e.text}"></label>`));
        });

        var $conjuntoEstrelas = $(
            '<div class="position-relative form-group d-flex align-items-start mt-2 required"></div>',
        )
            .append(
                $('<span class="no-wrap">Sobre a <b class="text-laranja">Seminovos</b>:</span>'),
            )
            .append($estrelas);

        var $rowColEstrelas = $(`
            <div class="row">
                <div class="col-12">
                    <div class="estrelas"/>
                </div>
            </div>`);
        $rowColEstrelas.find('.estrelas').replaceWith($conjuntoEstrelas);

        var $observacao = $(
            '<textarea maxlength="255" name="observacoes" class="form-control"></textarea>',
        );
        var $conjuntoObservacoes = $('<div class="form-group text-left mt-2"></div>')
            .append($('<span class="no-wrap">Observações:</span>'))
            .append($observacao);

        $form
            .append($span)
            .append($rowColSelect)
            .append($rowColEstrelas)
            .append($conjuntoObservacoes);
        FormAlerts.success({
            form: $form,
            title: 'Pesquisa de satisfação',
            submitText: 'Confirmar',
            closeCallback: function () {},
            submitCallback: function () {
                $('.modal').modal('hide');
                $.post(`/meus-veiculos/pesquisa/${veiculo.idVeiculo}`, $form.serialize()).done(
                    function (data) {
                        data = JSON.parse(data);

                        if (data.status !== 200) {
                            advancedAlerts.error({
                                text: data.detail,
                                title: 'Houve um problema...',
                                time: 10000,
                            });
                        } else {
                            advancedAlerts.success({
                                text: 'Agradecemos a sua resposta!',
                                closeCallback: function () {
                                    $('.modal').modal('hide');
                                },
                            });
                            reloadPageContent();
                        }
                    },
                );
            },
        });
    }
    function vendidoDataConfirm() {
        var $this = $(this);
        var $veiculo = $this.closest('.veiculo');
        let veiculo = {
            idVeiculo: $veiculo.data('id-veiculo'),
            placa: $veiculo.data('veiculo-placa'),
            marca: $veiculo.data('veiculo-marca'),
            modelo: $veiculo.data('veiculo-modelo'),
        };

        var displayName = $('.data-user-display-name').val();

        Confirms.info({
            title: $('<span class="text-primary">Marcar como vendido</span>'),
            text: $(`<span>
                        <b class="text-primary">${displayName}</b>, você confirma que deseja
                        <b class="text-primary">marcar como vendido</b> o anúncio
                        <b class="text-primary">${veiculo.marca} ${veiculo.modelo}</b>
                        <b> placa </b> <b class="text-primary"> ${veiculo.placa} </b>?
                    </span>`),
            confirmCallback: function () {
                $('.modal').modal('hide');
                $.getJSON(`/meus-veiculos/vendido/${veiculo.idVeiculo}`)
                    .done(function (data) {
                        if (data.status !== 200) {
                            advancedAlerts.error({
                                text: data.detail,
                                title: 'Houve um problema...',
                                time: 10000,
                            });
                        } else {
                            advancedAlerts.success({
                                text: `O veiculo ${veiculo.marca} ${veiculo.modelo} foi marcado como vendido`,
                                closeCallback: function () {
                                    $('.modal').modal('hide');
                                    pesquisaSatisfacaoDataForm(veiculo);
                                },
                            });
                            reloadPageContent();
                        }
                    })
                    .fail(function () {
                        advancedAlerts.error({
                            text: 'Não conseguir uma resposta para sua solicitação. <br> Tente novamente mais tarde.',
                            title: 'Houve um problema...',
                            time: 10000,
                        });
                    })
                    .always(function () {
                        $('.modal').modal('hide');
                    });
            },
        });
    }
    function anunciosModal() {
        var modal;
        var $this = $(this);
        var url = $this.data('url');
        var body = $this.data('modal-body');
        var successText = $this.data('modal-success-msg');
        var yesText = $this.data('modal-yes-text') || 'Sim';

        var btnSuccess = $('<button class="btn">')
            .html(yesText)
            .click(function () {
                $(this).attr('disabled', true);
                $.getJSON(url)
                    .done(function (data) {
                        if (data.status !== 200) {
                            advancedAlerts.error({
                                text: data.detail,
                                title: 'Houve um problema...',
                                time: 10000,
                            });
                        } else {
                            advancedAlerts
                                .info({ text: successText })
                                .on('hide.bs.modal', function () {
                                    if (!$this.data('modal-adicional-title')) {
                                        return;
                                    }
                                    var msg = $this.data('modal-adicional-msg');
                                    var title = $this.data('modal-adicional-title');
                                    var alertType = $this.data('modal-adicional-type') || 'info';
                                    var alertTime =
                                        parseFloat($this.data('modal-adicional-time')) || 5000;
                                    advancedAlerts[alertType]({
                                        text: msg,
                                        title: title,
                                        time: alertTime,
                                    });
                                });
                            reloadPageContent();
                        }
                    })
                    .fail(function () {
                        advancedAlerts.error({
                            text: 'Não conseguir uma resposta para sua solicitação. <br> Tente novamente mais tarde.',
                            title: 'Houve um problema...',
                            time: 10000,
                        });
                    })
                    .always(function () {
                        modal.modal('hide');
                    });
            });

        var footer = [
            '<button class="btn btn-danger" data-dismiss="modal">Cancelar</button>',
            btnSuccess,
        ];

        modal = $.jsBsModal({
            contents: {
                'modal-title': 'Atenção',
                'modal-body': body,
                'modal-footer': footer,
            },
        });
    }
    function reativarDataConfirm() {
        var $this = $(this);
        var $veiculo = $this.closest('.veiculo');

        $this.data('confirm-option-confirm', function () {
            $('.modal').modal('hide');
            var text = `A Seminovos <b class='text-primary'>NÃO </b>faz contato por
            <b class='text-primary'>telefone </b> ou <b class='text-primary'>whatsapp </b>
            solicitando código de verificação de anúncio ou similar.<br><br>
            CUIDADO PARA NÃO CAIR EM GOLPES<br><br>
            Estamos à disposição para esclarecer dúvidas<br>
            (31)3077-5888`;
            advancedAlerts.error({
                text: text,
                title: $('<span>').html('<span class="text-primary">Alerta </span>importante'),
                time: false,
                img: $('<img src="/img/svg/ico_irregularidade.svg" class="modal-img">'),
                closeText: 'ESTOU CIENTE',
                closeCallback: function () {
                    $.getJSON($this.data('confirm-url'))
                        .done(function (data) {
                            if (data.status !== 200) {
                                var url =
                                    '/carro/' +
                                    $veiculo.data('id-veiculo') +
                                    '?editar=planos#plano';
                                let aClasses =
                                    'btn btn-sm btn-info text-white d-flex ' +
                                    'align-items-center justify-content-center';
                                let btnClasses =
                                    'btn btn-sm btn-secondary d-flex align-items-center justify-content-center';
                                let divStyle = 'justify-content:center;gap:10px;margin-top:-20px;';
                                advancedAlerts.warning({
                                    title: '<span class="text-primary">Atenção!</span>',
                                    text:
                                        `<p>${data.detail}</p>` +
                                        `<div style="${divStyle}"  class="confirm-success d-flex">` +
                                        '' +
                                        `<button
                                            style="margin-top: 30px;width:20%;"
                                            class="${btnClasses}"
                                            data-dismiss="modal">OK!</button>` +
                                        '' +
                                        `<a href="${url}" style="margin-top: 30px; width:40%;" class="${aClasses}">
                                                TROCAR PLANO
                                        </a>` +
                                        '</div>',
                                    time: 0,
                                    close: '',
                                });
                            } else {
                                reloadPageContent();
                                var text = $('<span>').html(`<b class="text-primary">
                                        ${$veiculo.data('veiculo-marca')} ${$veiculo.data('veiculo-modelo')}</b>,
                                        <b class="text-primary">${$veiculo.data('veiculo-placa')}</b>
                                        reativado com <b class="text-primary">sucesso.</b>`);
                                advancedAlerts.success({
                                    text: text,
                                    title: $('<span class="text-primary">').html('Sucesso'),
                                });
                            }
                            $('.modal').modal('hide');
                        })
                        .fail(function () {
                            advancedAlerts.error({
                                title: 'ERRO',
                                text: 'Não conseguimos processar sua requisição, tente novamente mais tarde',
                            });
                        });

                    $('.modal').modal('hide');
                },
            });
        });
    }

    function renovarDataConfirm() {
        var $this = $(this);
        var $veiculo = $this.closest('.veiculo');
        $this.data('confirm-option-confirm', function () {
            $('.modal').modal('hide');
            var text = `A Seminovos <b class='text-primary'>NÃO </b>faz contato por
            <b class='text-primary'>telefone </b> ou <b class='text-primary'>whatsapp </b>
            solicitando código de verificação de anúncio ou similar.<br><br>
            CUIDADO PARA NÃO CAIR EM GOLPES<br><br>
            Estamos à disposição para esclarecer dúvidas<br>
            (31)3077-5888`;
            advancedAlerts.error({
                text: text,
                title: $('<span>').html('<span class="text-primary">Alerta </span>importante'),
                time: false,
                img: $('<img src="/img/svg/ico_irregularidade.svg" class="modal-img">'),
                closeText: 'ESTOU CIENTE',
                closeCallback: function () {
                    $.getJSON($this.data('confirm-url'))
                        .done(function (data) {
                            if (data.status !== 200) {
                                advancedAlerts.error({
                                    text: data.detail,
                                    title: 'Houve um problema...',
                                });
                            } else {
                                var text = $('<span>').html(`<b class="text-primary">
                                    ${$veiculo.data('veiculo-marca')} ${$veiculo.data('veiculo-modelo')}</b>,
                                    <b class="text-primary">${$veiculo.data('veiculo-placa')}</b>
                                    reativado com <b class="text-primary">sucesso.</b>`);
                                advancedAlerts.success({
                                    text: text,
                                    title: $('<span class="text-primary">').html('Sucesso'),
                                });
                            }
                            $('.modal').modal('hide');
                        })
                        .fail(function () {
                            advancedAlerts.error({
                                title: 'ERRO',
                                text: 'Não conseguimos processar sua requisição, tente novamente mais tarde',
                            });
                        });

                    $('.modal').modal('hide');
                },
            });
        });
    }
    function anuncioDataConfirm() {
        var $this = $(this);
        var type = $this.data('confirm-type') || 'success';
        $this.data(
            'confirm-modal',
            Confirms[type]({
                text: $this.data('confirm-body'),
                title: $this.data('confirm-title'),
                img: $this.data('confirm-img'),
                confirmText: $this.data('confirm-text'),
                negateText: $this.data('confirm-negate-text'),
                successText: $this.data('confirm-success-text'),
                confirmCallback: $this.data('confirm-option-confirm'),
                negateCallback: $this.data('confirm-option-negate'),
                reloadPage: $this.data('reload-page'),
            }),
        );
    }

    function modalRepasse() {
        if (!$('body').is('.t-revenda')) {
            return;
        }
        var modalRepasseCookie = 'modalRepasse';
        if (!jsCookie.get(modalRepasseCookie)) {
            $.jsBsModal({
                contents: {
                    close: false,
                    'modal-title': false,
                    'modal-body': `
                    <div class="text-center">
                        <img src="/img/repasse/repasse-seminovos.jpg" />
                    </div>
                    <div class="text-center mt-4">
                        Com o Repasse Seminovos, sua loja poderá publicar seu estoque de repasses em
                        uma área restrita apenas para revendedores.
                        <br><br>
                        <b>
                            Se lembra do correio de veículos?<br>
                            Funciona da mesma forma!
                        </b>
                        <br><br>
                        Você terá acesso a todo estoque das lojas cadastradas na Seminovos,
                        com informações completas em sua área restritra.
                        <br><br>
                        <b>
                        Publique agora mesmo de forma prática e rápida!
                        <br><br>
                        <a href="https://www.youtube.com/shorts/V4i_AW3OMkk" class="btn btn-laranja text-white" target="_BLANK">
                            Veja como funciona o Repasse Seminovos
                        </a>
                        <br><br>
                        <small>
                            Em caso de dúvidas,<br>
                            entre em contato conosco:<br>
                            <a href="http://wa.me/5531995502814" target="_blank" title="Atendimento por WhatsApp">
                                <i class="fa-brands fa-whatsapp" aria-hidden="true"></i> (31) 99550-2814
                            </a>
                        </small>
                    </div>
                `,
                    'modal-footer': [
                        '<a href="#" class="btn btn--laranja text--white" data-dismiss="modal">Fechar</a>',
                        '<a href="/repasse" class="btn btn-laranja text-white">Acessar Repasse</a>',
                    ],
                },
            }).on('hidden.bs.modal', function () {
                jsCookie.set(modalRepasseCookie, 1, { expires: 1 });
            });
        }
    }
};
