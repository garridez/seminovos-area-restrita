
module.exports.seletor = '.c-cadastrar.a-cadastro-simples';

module.exports.callback = ($) => {
    var Loading = require('components/Loading');
    var Alert = require('components/Alerts');

    $('form[name="form_cadastroSimples"]').submit(function (e) {
        var $this = $(this);
        var emailConfInput = $this.find('[name="confirmacaoEmail"]');
        var email = $this.find('[name="email"]').val().trim();
        var emailConf = emailConfInput.val().trim();

        if (email !== emailConf) {
            Alert.info('Os emails não são iguais!', 'Atenção')
                    .on('hidden.bs.modal', function () {
                        emailConfInput.focus();
                    });
            e.preventDefault();
            return false;
        }

        var senhaConfirmInput = $this.find('[name="confirmacaoSenha"]');
        var senha = $this.find('[name="senha"]').val().trim();
        var senhaConf = senhaConfirmInput.val().trim();

        if (senha !== senhaConf) {
            Alert.info('As senhas não são iguais!', 'Atenção')
                    .on('hidden.bs.modal', function () {
                        senhaConfirmInput.focus();
                    });
            e.preventDefault();
            return false;
        }
        Loading.addFeedbackTexts([
                    'Validando informações...',
                    'Salvando dados...',
                    'Fazendo login...',
                    'Redirecionando...'
                ], false);
        Loading.open();
    });
};