require('SnBH').autoRun.registerCallback('.c-auth.a-login', function ($) {
    require('bootstrap/js/dist/modal');
    var HandleApiError = require('components/HandleApiError');
    var Alert = require('components/Alerts');

    var ShowPassword = require('components/ShowPassword');
    ShowPassword($("input[type='password']"));

    $("body").on("click", "input.radioTipoCadastro[data-cookie]", function(e){
        let $this = $(this);
        let cookieDate = new Date;
        cookieDate.setFullYear(cookieDate.getFullYear() +1);
        document.cookie = `login-tipoCadastro=${$this.data("cookie")}; expires=${cookieDate.toGMTString()};`;
    });

    var $ctx = $('.login-area');
    var $formDivs = $('.container-form-particular, .container-form-revenda');
    $formDivs.filter('.hide').hide().removeClass('hide');
    $ctx.find('.switch-field input').change(function () {
        let seletectedForm = '.' + $(this).val();
        $formDivs
                .slideUp()
                .filter(seletectedForm)
                .slideDown();
    });

    // var match = document.cookie.match(/login-tipoCadastro=(?<tipoCadastro>[a-z]+)/)
    // var tipoCadastro = 'particular';
    // if (match && match.groups) {
    //     tipoCadastro = match.groups.tipoCadastro || tipoCadastro;
    // }
    // $(`input.radioTipoCadastro[data-cookie='${tipoCadastro}']`).click()

    var url = window.location.href;
    if ($('input[name=login-error]').val() === '1') {
        $('#modalErroSenha').modal('show');
        setTimeout(function () {
            $('#modalErroSenha').modal('hide');
        }, 8000);

    }
    if (url.search('#cuidado') > 0) {
        $('#modalCuidado').modal('show');
        setTimeout(function () {
            $('#modalCuidado').modal('hide');
        }, 8000);
    }
    $('form', '.container-form-particular, .container-form-revenda').submit(function (e) {
        $('.container-form-particular, .container-form-revenda').slideUp();
        $('.loading-container').slideDown();
    });

    $('form[name="formEmailTelFromCpfCpnj"]').submit(function (e) {
        e.preventDefault();
        $('#modalLembrarSenha').modal('hide');
        var cfpCnpj = $(this).find('input[name="cpfOuCpnj"]').val();
        $.ajax({
            type: 'POST',
            url: '/email-telefone-from-cpf-cnpj',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (data) {
                if (!HandleApiError(data)) {
                    return;
                }

                var $ctx = $('#modalRecuperarSenha');

                $ctx.find('#emailRetorno').text(data.email);
                $ctx.find('#telefoneRetorno').text(data.telefone);
                $ctx.find('#cpfCnpjRetorno').val(cfpCnpj);

                $('#modalValidaToken').find('#telefoneEnviado').text(data.telefone);

                $('#modalRecuperarSenha').modal('show');
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




    /**------------------------------------------------ */


    var AdvancedAlerts = require('components/AdvancedAlerts');
    $('#modalRecuperarSenha').on('click','.options .sms a',function(e){
      e.preventDefault();
      var cpfOuCpnj = $('#modalRecuperarSenha #cpfCnpjRetorno').val();

      $.ajax({
        type: 'POST',
        url: '/remember-pass-phone',
        data: {'cpfOuCpnj' : '131.623.356-12'},
        success: function (data) {
            if (!HandleApiError(data)) {
                return;
            }
            $('#modalValidaToken').modal('show');
        },
        error: function (e) {
            if (e.responseJSON) {
                HandleApiError(e.responseJSON);
            } else {
                HandleApiError(false);
            }
        }
      }).always(function(){
        $('#modalRecuperarSenha').modal('hide');
      });

    });

    $('#modalRecuperarSenha').modal('show');
    $('#modalValidaToken form#formValidaToken').submit(function(e){
      e.preventDefault();
      $.ajax({
        type: 'POST',
        url: '/validate-token',
        data: $(this).serialize(),
        dataType: 'json',
        success: function (data) {
            if (!HandleApiError(data)) {
                return;
            }
            if (data.token != token) {
              AdvancedAlerts.error({
                title: 'Erro',
                text: 'Token Inválido'
              });
              return;
            }
            $('#modalNovaSenha input[name="idCadastro"]').val(data.idCadastro);
            $('#modalNovaSenha').modal('show');
        },
        error: function (e) {
            if (e.responseJSON) {
                HandleApiError(e.responseJSON);
            } else {
                HandleApiError(false);
            }
        }
      }).always(function(){
        $('#modalValidaToken').modal('hide');
      });
    });

    $('#modalNovaSenha form#formNovaSenha').submit(function(e){
      e.preventDefault();

      var $this = $(this);
      var senha = $this.find('input[name="senha"]');
      var senhaConf = $this.find('input[name="senhaConf"]');

      if(senha != senhaConf){
        return;
      }

      $.ajax({
        type: 'POST',
        url: '/remember-pass-save',
        data: $(this).serialize(),
        dataType: 'json',
        success: function (data) {
            if (!HandleApiError(data)) {
                return;
            }
            $('#modalNovaSenha').modal('hide');
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

    /*AdvancedAlerts.info({
      title: 'Sucesso',
      text: 'Sua senha foi atualizada com sucesso',
      closeCallback: function(){
          window.location.href = '/';
      }
    });
    return;
    fetch('/remember-pass-phone', {
            method: 'POST',
            body: formData
        })
        .then((response) => {
            return response.json();
        })
        .then((response) => {
            if (response.status == 201) {
                mudarPasso('passo2');
            } else {
                document.getElementById('telefone_invalido').style.display = 'block';
            }
        })
        .catch(function (error) {
            console.log("Error: " + error);
        });
  }*/
});

