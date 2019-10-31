module.exports.seletor = ".c-meus-veiculos.a-index";

module.exports.callback = $ => {
    require("components/JsBsModal");
    var advancedAlerts = require("components/AdvancedAlerts");
    var Confirms = require('components/Confirms');
    var FormAlerts = require('components/FormAlerts');
    
    if ($("div[data-veiculo-finalizar]").length) {
        advancedAlerts.warning({
            title: "Você possuí anúncios não finalizados",
            text: "Você possui anúncios para concluir,<br/> conclua os anúncios com status <br/>CADASTRANDO ou CADASTRANDO GRÁTIS.",
            time: 12000
        });
    }

    $("body")
            .on("click", "a.reativar[data-confirm]", reativarDataConfirm)
            .on("click", "a.renovar[data-confirm]", renovarDataConfirm)
            .on("click", "a.anuncios[data-confirm]", anuncioDataConfirm)
            .on("click", "a.vendido[data-confirm]", vendidoDataConfirm)
            // Configura os modais genericos
            .on("click", ".anuncios [data-modal]", anunciosModal);

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
        }());
    }
    /**
     * Filtra a listagem de anúncios quando loggado como revenda
     * 
     */
    $("#plano, #staus").change(function () {
        $('.container-anuncios .anuncios .veiculo')
                .each(function () {
                    var $obj = $(this);
                    let result2 = $obj.hasClass($("#status").val());
                    let result = $obj.hasClass($("#plano").val());

                    if (result && result2) {
                        $obj
                                .removeClass("hide")
                                .addClass("show");
                    } else {
                        $obj
                                .removeClass("show")
                                .addClass("hide");
                    }
                });
    });

    ///////////////// CALLBACKS /////////////////
    /**
    * Baixa o conteúdo da página atualizado
    * Baixa apenas o conteúdo dentro da div ".container-anuncios"
    */

    function reloadPageContent() {
        $.get("/", function (data) {
            $(".container-anuncios").replaceWith(data);
        });
        $.get("/meus-veiculos/qtd-anuncios-menu", function (data) {
            $(".qtd-anuncios-menu").html(data);
        });
    }

    function pesquisaSatisfacaoDataForm (veiculo) {
        var $form = $("<form>");
        var $span = $("<small class='bold text-primary'>").html(`Dê a sua opnião, é rapidinho!`);
        var $select = $("<select class='form-control'>")
            .append('<option value="1">Vendi pela Seminovos BH</option>')
            .append('<option value="2">Desisti de vender</option>')
            .append('<option value="3">Vendi por outro meio</option>')
            .append('<option value="4">Outro motivo</option>');
        var $conjuntoSlect = $("<div class='d-flex align-items-center mt-4'></div>")
            .append($("<span class='no-wrap mr-3'>Sobre a <b class='text-primary'>venda do veículo</b>:</span>"))
            .append($select);

        var $estrelas = $("<div class='rate'>")
            .append(`<input type="radio" id="star5" name="rate" value="5" />`)
            .append(`<label for="star5" title="text">5 stars</label>`)
            .append(`<input type="radio" id="star4" name="rate" value="4" />`)
            .append(`<label for="star4" title="text">4 stars</label>`)
            .append(`<input type="radio" id="star3" name="rate" value="3" />`)
            .append(`<label for="star3" title="text">3 stars</label>`)
            .append(`<input type="radio" id="star2" name="rate" value="2" />`)
            .append(`<label for="star2" title="text">2 stars</label>`)
            .append(`<input type="radio" id="star1" name="rate" value="1" />`)
            .append(`<label for="star1" title="text">1 star</label>`);
        var $conjuntoEstrelas = $("<div class='d-flex align-items-start mt-2'></div>")
            .append($("<span class='no-wrap'>Sobre a <b class='text-laranja'>Seminovos</b>:</span>"))
            .append($estrelas);
        
        var $observacao = $("<textarea maxlength='255' class='form-control'></textarea>");
        var $conjuntoObservacoes = $("<div class='text-left mt-2'></div>")
            .append($("<span class='no-wrap'>Observações:</span>"))
            .append($observacao);

        $form.append($span).append($conjuntoSlect).append($conjuntoEstrelas).append($conjuntoObservacoes);
        FormAlerts.success({
            form: $form,
            title:"Pesquisa de satisfação",
            submitText:"Confirmar",
            closeCallback:function(){

            },
            submitCallback: function () {
                /**
                 * POST PARA ONDE A PESQUISA DE SATISFAÇÃO VAI
                 */
                $.post(`/meus-veiculos/vendido/${veiculo.idVeiculo}`, { name: "John", time: "2pm" })
                    .done(function( data ) {
                        alert( "Data Loaded: " + data );
                    });
            }
        });
    }
    function vendidoDataConfirm(){
        var $this = $(this);
        var $veiculo = $this.closest(".veiculo");
        let veiculo = {
            idVeiculo : $veiculo.data("id-veiculo"),
            placa : $veiculo.data("veiculo-placa"),
            marca : $veiculo.data("veiculo-marca"),
            modelo : $veiculo.data("veiculo-modelo"),
        }

        var displayName = $(".data-user-display-name").val();


        Confirms.info({
            title:$("<span class='text-primary'>Marcar como vendido</span>"),
            text: $(`<span>
                        <b class="text-primary">${displayName}</b>, você confirma que deseja
                        <b class="text-primary">marcar como vendido</b> o anúncio
                        <b class="text-primary">${veiculo.marca} ${veiculo.modelo}</b>
                        <b> placa </b> <b class="text-primary"> ${veiculo.placa} </b>?
                    </span>`),
            confirmText: "Sim, eu vendi",
            confirmCallback: function(){
                $(".modal").modal('hide');
                $.getJSON(`/meus-veiculos/vendido/${veiculo.idVeiculo}`)
                .done(function (data, jqXHR, type) {
                    if (data.status !== 200) {
                        advancedAlerts.error({text: data.detail, title: "Houve um problema...", time: 10000});
                    } else {
                        advancedAlerts.success({
                            text:`O veiculo ${veiculo.marca} ${veiculo.modelo} foi marcado como vendido`,
                            closeCallback:function(){
                                $(".modal").modal('hide');
                                pesquisaSatisfacaoDataForm(veiculo) 
                            }
                    })
                        reloadPageContent();
                    }
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    advancedAlerts.error({
                        text: "Não conseguir uma resposta para sua solicitação. <br> Tente novamente mais tarde.",
                        title: "Houve um problema...",
                        time: 10000
                    });
                })
                .always(function () {
                    modal.modal("hide");
                });
            }
     });
    }
    function anunciosModal() {
        var modal;
        var $this = $(this);
        var url = $this.data("url");
        var body = $this.data("modal-body");
        var successText = $this.data("modal-success-msg");
        var yesText = $this.data("modal-yes-text") || "Sim";

        var btnSuccess = $('<button class="btn">')
                .html(yesText)
                .click(function () {
                    $(this).attr("disabled", true);
                    $.getJSON(url)
                            .done(function (data, jqXHR, type) {
                                if (data.status !== 200) {
                                    advancedAlerts.error({text: data.detail, title: "Houve um problema...", time: 10000});
                                } else {
                                    advancedAlerts.info({text: successText})
                                            .on('hide.bs.modal', function () {
                                                if (!$this.data("modal-adicional-title")) {
                                                    return;
                                                }
                                                var msg = $this.data("modal-adicional-msg");
                                                var title = $this.data("modal-adicional-title");
                                                var alertType = $this.data("modal-adicional-type") || 'info';
                                                var alertTime = parseFloat($this.data("modal-adicional-time")) || 5000;
                                                advancedAlerts[alertType]({text: msg, title: title, time: alertTime});
                                            });
                                    reloadPageContent();
                                }
                            })
                            .fail(function (jqXHR, textStatus, errorThrown) {
                                advancedAlerts.error({
                                    text: "Não conseguir uma resposta para sua solicitação. <br> Tente novamente mais tarde.",
                                    title: "Houve um problema...",
                                    time: 10000
                                });
                            })
                            .always(function () {
                                modal.modal("hide");
                            });
                });

        var footer = [
            '<button class="btn btn-danger" data-dismiss="modal">Cancelar</button>',
            btnSuccess
        ];

        modal = $.jsBsModal({
            contents: {
                "modal-title": "Atenção",
                "modal-body": body,
                "modal-footer": footer
            }
        });
    }
    function reativarDataConfirm() {
        var $this = $(this);
        var $veiculo = $this.closest(".veiculo");
        $this.data("confirm-option-confirm", function () {
            $(".modal").modal('hide');
            var text = `A Seminovos <b class='text-primary'>NÃO </b>faz contato por
            <b class='text-primary'>telefone </b> ou <b class='text-primary'>whatsapp </b>
            solicitando código de verificação de anúncio ou similar.<br><br>
            CUIDADO PARA NÃO CAIR EM GOLPES<br><br>
            Estamos à disposição para esclarecer dúvidas<br>
            (31)3077-5888`;
            advancedAlerts.error({
                text: text,
                title: $("<span>").html(`<span class='text-primary'>Alerta </span>importante`),
                time: false,
                img: $('<img src="/img/svg/ico_irregularidade.svg" class="modal-img">'),
                closeText: "ESTOU CIENTE",
                closeCallback: function () {
                    $.getJSON($this.data("confirm-url"))
                            .done(function (data, jqXHR, type) {
                                if (data.status !== 200) {
                                    advancedAlerts.error({text: data.detail, title: "Houve um problema...", time: 10000});
                                } else {
                                    reloadPageContent();
                                    var text = $("<span>").html(`<b class="text-primary">${$veiculo.data("veiculo-marca")} ${$veiculo.data("veiculo-modelo")}</b>, 
                                                            <b class="text-primary">${$veiculo.data("veiculo-placa")}</b> 
                                                            reativado com <b class="text-primary">sucesso.</b>`);
                                    advancedAlerts.success({
                                        text: text,
                                        title: $("<span class='text-primary'>").html("Sucesso"),
                                    });
                                }
                                $(".modal").modal('hide');
                            }).fail(function () {
                        advancedAlerts.error({title: "ERRO", text: "Não conseguimos processar sua requisição, tente novamente mais tarde"});
                    });

                    $(".modal").modal('hide');
                }
            });
        });
    }

    function renovarDataConfirm() {
        var $this = $(this);
        var $veiculo = $this.closest(".veiculo");
        $this.data("confirm-option-confirm", function () {
            $(".modal").modal('hide');
            var text = `A Seminovos <b class='text-primary'>NÃO </b>faz contato por
            <b class='text-primary'>telefone </b> ou <b class='text-primary'>whatsapp </b>
            solicitando código de verificação de anúncio ou similar.<br><br>
            CUIDADO PARA NÃO CAIR EM GOLPES<br><br>
            Estamos à disposição para esclarecer dúvidas<br>
            (31)3077-5888`;
            advancedAlerts.error({
                text: text,
                title: $("<span>").html(`<span class='text-primary'>Alerta </span>importante`),
                time: false,
                img: $('<img src="/img/svg/ico_irregularidade.svg" class="modal-img">'),
                closeText: "ESTOU CIENTE",
                closeCallback: function () {
                    $.getJSON($this.data("confirm-url"))
                            .done(function (data, jqXHR, type) {
                                if (data.status !== 200) {
                                    advancedAlerts.error({
                                        text: data.detail,
                                        title: "Houve um problema...",
                                    })
                                } else {
                                    var text = $("<span>").html(`<b class="text-primary">${$veiculo.data("veiculo-marca")} ${$veiculo.data("veiculo-modelo")}</b>, 
                                                            <b class="text-primary">${$veiculo.data("veiculo-placa")}</b> 
                                                            reativado com <b class="text-primary">sucesso.</b>`);
                                    advancedAlerts.success({
                                        text: text,
                                        title: $("<span class='text-primary'>").html("Sucesso")
                                    });
                                }
                                $(".modal").modal('hide');
                            }).fail(function () {
                        advancedAlerts.error({title: "ERRO", text: "Não conseguimos processar sua requisição, tente novamente mais tarde"});
                    });

                    $(".modal").modal('hide');
                }
            });
        });
    }
    function anuncioDataConfirm() {
        var $this = $(this);
        var type = $this.data("confirm-type") || 'success';
        $this.data("confirm-modal", Confirms[type]({
            text: $this.data("confirm-body"),
            title: $this.data("confirm-title"),
            img: $this.data("confirm-img"),
            confirmText: $this.data("confirm-text"),
            negateText: $this.data("confirm-negate-text"),
            successText: $this.data("confirm-success-text"),
            confirmCallback: $this.data("confirm-option-confirm"),
            negateCallback: $this.data("confirm-option-negate")
        }));
    }
};
