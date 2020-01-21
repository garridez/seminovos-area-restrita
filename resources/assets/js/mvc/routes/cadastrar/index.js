
module.exports.seletor = '.c-cadastrar.a-index';

module.exports.callback = ($) => {
    require('components/EstadoCidade')();
    var HandleApiError = require('components/HandleApiError');
    var Alert = require('components/Alerts');
    var advancedAlerts = require('components/AdvancedAlerts');

    $('form#form_particularSite').submit(function (e) {
        e.preventDefault();
        var $this = $(this);
        var emailConfInput = $this.find('[name="confirmacao-email"]');
        var email = $this.find('[name="email"]').val().trim();
        var emailConf = emailConfInput.val().trim();

        if (email !== emailConf) {
            Alert.info('Os emails não são iguais!', 'Atenção')
                    .on('hidden.bs.modal', function () {
                        emailConfInput.focus();
                    });
            return;
        }

        $.ajax({
            type: 'POST',
            url: '/me-cadastrar',
            data: $this.serialize(),
            dataType: 'json',
            success: function (data) {
                if (!HandleApiError(data)) {
                    return;
                }
                var modal = Alert.info('Sua conta foi criada!<br> Você recebera um e-mail para liberação do seu cadastro',
                        'Falta pouco', 20000);
                function redirectEntrar() {
                    window.location.href = '/';
                }
                modal.on('hide.bs.modal', redirectEntrar).find('.modal-footer .btn')
                        .removeClass('btn-danger')
                        .addClass('btn-success')
                        .click(redirectEntrar)
                        .find('.text-close')
                        .html('Acessar Meus Anúncios');
            },
            error: function (e) {
                if (e.responseJSON) {
                    HandleApiError(e.responseJSON);
                } else {
                    HandleApiError(false);
                }
            }
        });
    });
    
    $("form[name='form_particularSite']").find("input[name='email']").blur(function (event) {
        var emailInput = $(this);
        var email = emailInput.val() || '';

        $("form[name='form_particularSite']").find("button")
                        .addClass('disabled')
                        .attr('disabled', true)
                        .attr('title', 'Verifique os dados antes de continuar');
        $.ajax({
            type: "GET",
            url: "/carro/email-disponivel/"+email,
            dataType: "json",
            success: function (response) {
                emailInput
                    .removeClass('is-invalid is-valid')
                    .addClass(response.emailDisponivel ? 'is-valid' : 'is-invalid');
                if (!response.emailDisponivel) {
                    $("form[name='form_particularSite']").find("button")
                        .addClass('disabled')
                        .attr('disabled', true)
                        .attr('title', 'Verifique os dados antes de continuar');

                    advancedAlerts.error({
                        title: "E-mail já cadastrado",
                        text: "E-mail já cadastrado no sistema, confira o e-mail ou entre em contato.",
                        time: 10000
                    })
                    return;
                }
                $("form[name='form_particularSite']").find("button")
                    .removeClass('disabled')
                    .attr('disabled', false)
                    .attr('title', 'Continuar');
            },
            error: function (e) {

            }
        });

    });
};