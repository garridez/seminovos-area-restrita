
module.exports.seletor = '.c-cadastrar.a-index';

module.exports.callback = ($) => {
    require('components/EstadoCidade')();

    var Verificadores = require('components/Verificadores');
    var HandleApiError = require('components/HandleApiError');
    var Alert = require('components/Alerts');
    var advancedAlerts = require('components/AdvancedAlerts');

    var ctx = $("form[name='form_particularSite']");

    var ua = navigator.userAgent.toLowerCase();
    if (ua.indexOf('safari') !== -1 && ua.indexOf('chrome') === -1) {
      console.log('Disable date input on safari');
      $inpuDataNasc = $('input[name="dataNascimento"]');
      $inpuDataNasc.attr('type', 'text');
      $inpuDataNasc.mask("00/00/0000");
    }

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

        if (ua.indexOf('safari') !== -1 && ua.indexOf('chrome') === -1) {
          var $inpuDataNasc = $('input[name="dataNascimento"]');
          var date = $inpuDataNasc.val().split('/');
          $inpuDataNasc.unmask();
          $inpuDataNasc.val(date[2] + "-" + date[1] + "-" + date[0]);
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
                var modal = Alert.info(`<h4>Sua conta foi criada!<br>Você recebera um e-mail para liberação do seu cadastro</h4>`,
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

    ctx.find("input[name='confirmacao-email']").blur(function (event) {
        var emailInput = $(this);
        var email = emailInput.val() || '';

        Verificadores.verficaEmailAction(email).then(function(response){
            validationControl(emailInput, response.emailDisponivel);
        });
    });

    ctx.find("input[name='cpfResponsavel']").blur(function (event) {
      var cpfInput = $(this);
      var cpf = cpfInput.val() || '';
      Verificadores.verficaCpfAction(cpf).then(function(response){
          validationControl(cpfInput, response.cpfDisponivel)
      });
    });

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

    $('.show-password').on('click', function () {
        let showPassword = $(this)
        let icon = showPassword.find('i');

        if (icon.hasClass('fa-eye')) {
            icon.removeClass('fa-eye').addClass('fa-eye-slash')
            showPassword.parent().find('input[type="password"]').prop('type', 'text')
        } else {
            icon.removeClass('fa-eye-slash').addClass('fa-eye')
            showPassword.parent().find('input[type="text"]').prop('type', 'password')
        }
    })

};
