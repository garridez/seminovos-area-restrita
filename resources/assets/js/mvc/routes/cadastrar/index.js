
module.exports.seletor = '.c-cadastrar.a-index';

module.exports.callback = ($) => {
    require('components/EstadoCidade')();
    var HandleApiError = require('components/HandleApiError');
    var Alert = require('components/Alerts');

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
                var modal = Alert.info('Ótimo!<br>'
                        + 'Sua conta foi criada<br> Agora acesse <a href="/">"Meus Anúncios"</a> para anunciar seu veículo',
                        'Conta Criada', 20000);
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
};