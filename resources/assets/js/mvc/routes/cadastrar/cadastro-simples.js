
module.exports.seletor = '.c-cadastrar.a-cadastro-simples';

module.exports.callback = ($) => {
    var Loading = require('components/Loading');
    var Alert = require('components/Alerts');

    var Verificadores = require('components/Verificadores');

    var ctx = $('form[name="form_cadastroSimples"]');

    function validationControl(input, validated) {
      $btnSubmit = ctx.find('button[type="submit"]');
      $input = ctx.find(input);

      $btnSubmit
        .addClass('disabled')
        .attr('disabled', true)
        .attr('title', 'Verifique os dados antes de continuar');

      $input
        .removeClass('is-invalid is-valid')
        .addClass(validated ? 'is-valid' : 'is-invalid');

      if(validated){
        $btnSubmit.removeClass('disabled')
          .attr('disabled', false)
          .attr('title', 'Continuar');
      }
    }

    ctx.find("input[name='confirmacaoEmail']").blur(function (event) {
      var emailInput = $(this);
      var email = emailInput.val() || '';

      Verificadores.verficaEmailAction(email).then(function(response){
          validationControl(emailInput, response.emailDisponivel);
      });
    });

    ctx.find('input[name="cpfResponsavel"]').blur(function (event) {
        var cpfInput = $(this);
        var cpf = cpfInput.val() || '';

        Verificadores.verficaCpfAction(cpf).then(function(response){
          validationControl(cpfInput, response.cpfDisponivel)
        });
    });

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
