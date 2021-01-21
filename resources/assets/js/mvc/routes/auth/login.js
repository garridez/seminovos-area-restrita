require('SnBH').autoRun.registerCallback('.c-auth.a-login', function ($) {
    require('bootstrap/js/dist/modal');
    var HandleApiError = require('components/HandleApiError');
    var Alert = require('components/Alerts');
    var advancedAlerts = require('components/AdvancedAlerts');

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

    /**------------------------------------------------ */
    var $formDadosBasicos = $('form#formdadosBasicos');
    var AdvancedAlerts = require('components/AdvancedAlerts');

    $('form[name="formContatosCpfCpnj"]').submit(function (e) {
      e.preventDefault();
      $('#modalLembrarSenha').modal('hide');
      $('#modalLembrarSenhaRevenda').modal('hide');

      var cfpCnpj = $(this).find('input[name="cpfOuCpnj"]').val();

      var tipoCad = $(this).find('input[name="tipoCadastro"]').val();
      $formDadosBasicos.find('input[name="tipoCadastro"]').val(tipoCad);

      $.ajax({
          type: 'POST',
          url: '/email-telefone-from-cpf-cnpj',
          data: $(this).serialize(),
          dataType: 'json',
          success: function (data) {
              if (!HandleApiError(data)) {
                  return;
              }
              
              if(!data.cpfCadastro){
                  advancedAlerts.error({
                  title: "CPF não encontrado",
                  text: `O CPF informado não existe em nossos cadastrados.<br/><br/>
                        <div><a href="/me-cadastrar" title="Criar uma conta" class="btn link-laranja">
                        Cadastre-se </a></div><br/>`,
                  time: 10000
                });
                    return;
              }
              
              $('#modalRecuperarSenha').find('.sms,.token,.email').removeClass('d-flex').removeClass('d-none');

              $('[data-retorno-telefone]').text(data.telefone);
              $('[data-retorno-email]').text(data.email);

              $('#modalRecuperarSenha').find('.sms,.token').addClass('d-none');
              if(data.telefone){
                $('#modalRecuperarSenha').find('.sms,.token').removeClass('d-none').addClass('d-flex');
              }

              $('#modalRecuperarSenha').find('.email').addClass('d-none');
              if(data.email){
                $('#modalRecuperarSenha').find('.email').removeClass('d-none').addClass('d-flex');
              }

              $formDadosBasicos.find('input[name="cpfCnpj"]').val(cfpCnpj);

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

    $('#modalRecuperarSenha').on('click','.options .sms a',function(e){
      e.preventDefault();
      var cpfOuCpnj = $formDadosBasicos.find('input[name="cpfCnpj"]').val();

      $.ajax({
        type: 'POST',
        url: '/remember-pass-phone',
        data: {'cpfOuCpnj' : cpfOuCpnj},
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

    $('#modalRecuperarSenha').on('click','.options .token a',function(e){
      e.preventDefault();
      $('#modalRecuperarSenha').modal('hide');
      $('#modalValidaToken').modal('show');
    });

    $('#modalRecuperarSenha').on('click','.options .email a',function(e){
      e.preventDefault();

      var cpfOuCpnj = $formDadosBasicos.find('input[name="cpfCnpj"]').val();
      $.ajax({
        type: 'POST',
        url: '/remember-pass',
        data: { 'cpfOuCpnj': cpfOuCpnj },
        dataType: 'json',
        success: function (data) {
            if (!HandleApiError(data)) {
                return;
            };
            AdvancedAlerts.success({
              title: 'Senha Enviada',
              text: 'Nova senha enviada para o email:</br>' + data.email
            });
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

    $('#modalValidaToken form#formValidaToken').submit(function(e){
      e.preventDefault();
      var $this = $(this);
      $.ajax({
        type: 'POST',
        url: '/validate-token',
        data: $(this).serialize(),
        dataType: 'json',
        success: function (data) {
            if (!HandleApiError(data)) {
                return;
            }
            var token = $this.find('input[name="token"]').val();
            if (data.data.token != token) {
              AdvancedAlerts.error({
                title: 'Erro',
                text: 'Token Inválido'
              });
              return;
            }
            $formDadosBasicos.find('input[name="idCadastro"]').val(data.data.idCadastro);
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
      var senha = $this.find('input[name="senha"]').val();
      var senhaConf = $this.find('input[name="senhaConf"]').val();
      var idCadastro = $formDadosBasicos.find('input[name="idCadastro"]').val();

      if(senha != senhaConf){
        AdvancedAlerts.error({
          title: 'Erro',
          text: 'As senhas não conferem'
        });
        return;
      }

      $.ajax({
        type: 'POST',
        url: '/remember-pass-save',
        data: {
          'senha' : senha,
          'idCadastro' : idCadastro
        },
        dataType: 'json',
        success: function (data) {
            if (!HandleApiError(data)) {
                return;
            }
            $('#modalNovaSenha').modal('hide');
            AdvancedAlerts.success({
              title: 'Nova senha cadastrada',
              text: 'Utilize a nova senha para entrar'
            });
            $formDadosBasicos.find('input').val('');
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

