var advancedAlerts = require('components/AdvancedAlerts');
var HandleApiError = require('components/HandleApiError');

function verficaCpfAction(cpf) {
  return $.ajax({
      type: "GET",
      url: "/carro/cpf-disponivel/"+cpf,
      dataType: "json",
      success: function (response) {
          if (!response.cpfDisponivel) {

              var concat = '*******';

              var email = response.emailVinculado;
              var emailMask = response.emailVinculado.split('@');

              var emailName =  emailMask[0].slice(0,3) + concat;
              var emailDomain =  '@' + emailMask[1].slice(0,2) + concat;

              emailMask = emailMask[1].split('.').splice(1);

              var emailLocation = '.' + emailMask.join('.');
              var emailMasked = emailName + emailDomain + emailLocation;

              advancedAlerts.error({
                  title: "CPF já cadastrado",
                  text: `O CPF informado já está cadastrado com o email: ${emailMasked} <div class='esqueci-minha-senha'><a class='' href=''>Esqueci a senha</a></div>`,
                  time: 100000000
              }).on('click','.esqueci-minha-senha a', function(e){
                e.preventDefault();
                e.stopPropagation();
                $('.modal, .modal-backdrop').hide();
                esqueciMinhaSenhaAction(email,emailMasked);
              });
              return;
          }
      },
      error: function (e) {}
  });
};
function verficaEmailAction(email) {
  return $.ajax({
    type: "GET",
    url: "/carro/email-disponivel/"+email,
    dataType: "json",
    success: function (response) {
        if (!response.emailDisponivel) {
            advancedAlerts.error({
                title: "E-mail já cadastrado",
                text: "E-mail já cadastrado no sistema, confira o e-mail.<div class='esqueci-minha-senha'><a class='' href=''>Esqueci a senha</a></div>",
                time: 10000
            }).on('click','.esqueci-minha-senha a', function(e){
              e.preventDefault();
              e.stopPropagation();
              $('.modal, .modal-backdrop').hide();
              esqueciMinhaSenhaAction(email);
            });
            return;
        }
    },
    error: function (e) {}
  });
};
function esqueciMinhaSenhaAction(email, emailMasked = ''){

  var fakeForm = $(`<form method="POST" name="formLembrarSenha" id="formLembrarSenha" action="remember-pass">
    <input type="email" value="${email}" id="emailLembrarSenha" name="emailLembrarSenha" class="form-control" id="inlineFormInputGroup" placeholder="Email">
  </form>`);

  $.ajax({
      type: 'POST',
      url: '/remember-pass',
      data: $(fakeForm).serialize(),
      dataType: 'json',
      success: function (data) {
          if (!HandleApiError(data)) {
              return;
          }
          if(emailMasked){
            email = emailMasked;
          }
          advancedAlerts.info({
            text:'Confira a caixa de entrada do email <b>' + email + '</b>',
            title: 'Email enviado'
          });
      },
      error: function (e) {
          if (e.responseJSON) {
              HandleApiError(e.responseJSON);
          } else {
              HandleApiError(false);
          }
      }
  });
}
module.exports = {verficaCpfAction, verficaEmailAction};
