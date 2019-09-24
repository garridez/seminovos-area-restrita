module.exports.seletor = ".c-meus-veiculos.a-index";

module.exports.callback = $ => {
    require("components/JsBsModal");
    var advancedAlerts = require("components/AdvancedAlerts");
    var Confirms = require('components/Confirms');
    var FormAlerts = require('components/FormAlerts');

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

    $("body").on("click", "a.vendido[data-confirm]", function (e) {
        var $this = $(this);
        var displayName = $(".data-user-display-name").val();
        var $veiculo = $this.closest(".veiculo");
        var $form = $("<form>");
        var $span = $("<span>").html(`Olá, <b class="text-primary">${displayName}</b> ! 
            Como foi sua experiência em anunciar conosco?
            Dê a sua opnião, é rapidinho!`
        );
        var $select = $("<select class='form-control'>")
            .append('<option value="1">Vendi pela Seminovos BH</option>')
            .append('<option value="2">Desisti de vender</option>')
            .append('<option value="3">Vendi por outro meio</option>')
            .append('<option value="4">Outro motivo</option>');

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
        var $observacao = $("<textarea maxlength='255' class='form-control'></textarea>");
        $form.append($span).append($select).append($estrelas).append($observacao);
        $this.data("confirm-option-confirm", function () {
            $(".modal").modal('hide');
            FormAlerts.success({
                form: $form,
                title:"Pesquisa de satisfação",
                submitText:"Confirmar",
                closeCallback:function(){},
                submitCallback: function () {
                    $(".modal").modal('hide');
                }
            })
        });
    });

    $("body").on("click", "a.reativar[data-confirm]", function () {
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
                                advancedAlerts.error({text:data.detail, title:"Houve um problema...", time:10000});
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
                            advancedAlerts.error({ title: "ERRO", text: "Não conseguimos processar sua requisição, tente novamente mais tarde" });
                        })

                    $(".modal").modal('hide');
                }
            });
        })
    });
    /**
     * @todo RENOVAR E REATIVAR ESTÃO IGUAIS
     */
    $("body").on("click", "a.renovar[data-confirm]", function () {
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
                            advancedAlerts.error({ title: "ERRO", text: "Não conseguimos processar sua requisição, tente novamente mais tarde" });
                        })

                    $(".modal").modal('hide');
                }
            });
        })
    });
    $("body").on("click", "a.anuncios[data-confirm]", function () {
        var $this = $(this);
        var type = $this.data("confirm-type") || success;
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
    });






    $("body").on("click", ".anuncios [data-modal]", function () {
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
                            advancedAlerts.error({text:data.detail, title:"Houve um problema...", time:10000});
                        } else {
                            advancedAlerts.info({text:successText})
                                .on('hide.bs.modal', function () {
                                    if (!$this.data("modal-adicional-title")) {
                                        return;
                                    }
                                    var msg = $this.data("modal-adicional-msg");
                                    var title = $this.data("modal-adicional-title");
                                    var alertType = $this.data("modal-adicional-type") || 'info';
                                    var alertTime = parseFloat($this.data("modal-adicional-time")) || 5000;
                                    advancedAlerts[alertType]({text:msg, title:title, time:alertTime});
                                });
                            reloadPageContent();
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        advancedAlerts.error({
                            text:"Não conseguir uma resposta para sua solicitação. <br> Tente novamente mais tarde.",
                            title:"Houve um problema...",
                            time:10000
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
    });
    function filtar(obj) {
        let result = $(obj).hasClass($("#plano").val());
        let result2 = $(obj).hasClass($("#status").val());
        if (result && result2) {
            $(obj)
                .removeClass("hide")
                .addClass("show");
        } else {
            $(obj)
                .removeClass("show")
                .addClass("hide");
        }
    }
    $("#status").change(function (params) {
        $(".col-12.row.mb-3.bg-white.p-0.justify-content-center").filter(function (
            index
        ) {
            filtar(this);
        });
    });
    $("#plano").change(function (params) {
        $(".col-12.row.mb-3.bg-white.p-0.justify-content-center").filter(function (
            index
        ) {
            filtar(this);
        });
    });
};
