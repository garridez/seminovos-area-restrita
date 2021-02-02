
module.exports.seletor = '.c-meus-dados.a-index';

module.exports.callback = ($) => {
    var advancedAlerts = require('components/AdvancedAlerts');
    var $ctxForm = $('form[name="form_particularSite"]');
    var emailInput = $ctxForm.find("input[name='email']");

    var $btnSubmit = $ctxForm.find('button[type="submit"]');
    var originalEmail = emailInput.val() || '';
    $(emailInput).keypress(function(){
      var email = emailInput.val() || '';
      if(originalEmail == email) return;

      $btnSubmit.addClass('to-validade');
    });


    $ctxForm.on('click','button.to-validade',function (e) {
      e.preventDefault();
      e.stopPropagation();

      $('.loading-container').removeClass('hide');

      var email = emailInput.val() || '';
      $.ajax({
          type: "GET",
          url: "/carro/email-disponivel/"+email,
          dataType: "json",
          success: function (response) {
              emailInput
                  .removeClass('is-invalid is-valid')
                  .addClass(response.emailDisponivel ? 'is-valid' : 'is-invalid');
              if (!response.emailDisponivel) {

                  $btnSubmit
                      .attr('title', 'Verifique os dados antes de continuar');

                  advancedAlerts.error({
                      title: "E-mail já cadastrado",
                      text: "E-mail já cadastrado no sistema, confira o e-mail ou entre em contato.",
                      time: 10000
                  });
                  $('.loading-container').addClass('hide');
                  return;
              }

              $('.loading-container').addClass('hide');

              $btnSubmit
                  .removeClass('to-validade');

              $btnSubmit.click();
          },
          error: function (e) {}
      });
    });
    
    $ctxForm.find("input[name='cpfResponsavel']").blur(function (event) {
      var cpfInput = $(this);
      var cpf = cpfInput.val() || '';
      $.ajax({
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
                  text: `O CPF informado já está cadastrado com o email: ${emailMasked}`,
                  time: 10000
              });
              return;
          }
      },
      error: function (e) {}
      });
    });

    require('components/EstadoCidade')();
    var advancedAlerts = require('components/AdvancedAlerts');
    var resquestResponse = $("span[data-request-response]").data("request-response") || false;
    if (!resquestResponse) {
        return;
    }
    if (resquestResponse !== 200) {
        advancedAlerts.error({
            title: $("<span class='text-primary'>").html("Erro"),
            text: "Não conseguimos processar sua requisição, tente novamente mais tarde"
        });
        return;
    }
    advancedAlerts.success({
        text: $("<span>").html("Dados salvos com <b class='text-primary'>sucesso</b>"),
        title: $("<span class='text-primary'>").html("Sucesso"),
    });
};
