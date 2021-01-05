module.exports.seletor = '.c-criar-anuncio.a-index';
module.exports.prepend = true;

module.exports.callback = ($) => {
    var advancedAlerts = require('components/AdvancedAlerts');
    var Confirms = require('components/Confirms');
    var stepContainer = $('.step-container');
    var stopEvent = require('helpers/StopEvent');
    var idPlano = $("#idPlano").val();

    stepContainer.on('step:change:checkout', function (e) {
        // handler certificado no checkout
        window.fromCheckout = true;

        $('.btn-voltar').removeClass('hide d-none');

        var ctx = ' .step-checkout ';
        var classAcitive = 'certificado-active';

        $('.btn-voltar').on('click',function(e){
          handdlerCertificado(true);
        });

        $(ctx +'a.btn-control-certificado').on('click',function(e){
          stopEvent(e);
          handdlerCertificado();
        });

        function handdlerCertificado(ForceRemover = false){
          var valorCertificado = 24.90; //preço certificado
          var valorTotal = 0;
          var remover = $(ctx + '.flagCertificado').is(':checked');

          if($('#dados-basicos .acao').val() != 'addCertificado'){
              var valorPlano = parseFloat($(ctx + ' .plano-selecionado input[data-valor-plano]').val());
              valorTotal = valorCertificado + valorPlano;
          }else{
            valorTotal = valorCertificado;
          }

          $('.valor-total').find('[data-valor-total]').html(valorTotal.toFixed(2));

          $(ctx +'.resumo-compra').addClass(classAcitive);
          $(ctx + '.flagCertificado').prop('checked','checked');
          $('#dados-basicos .certificado').val(1);

          if(remover || ForceRemover){
            $(ctx +'.resumo-compra').removeClass(classAcitive);
            $(ctx + '.flagCertificado').prop('checked',false);
            $('#dados-basicos .certificado').val('');
            if(!valorPlano){
              valorPlano = 0;
            }
            valorTotal = valorPlano;
            $('.valor-total').find('[data-valor-total]').html(valorTotal.toFixed(2));

            $('.certificado-adicionar').slideDown();
            $('.certificado-resumo').slideUp();
          }
          else{
            $('.certificado-adicionar').slideUp();
            $('.certificado-resumo').slideDown();
          }
          return;
        }
        let btnSaibaMais = $(ctx + 'a.saiba-mais');

        // handler modal certificado
        btnSaibaMais.on('click',function(e){
          e.preventDefault();
          $('body').prepend($('<div class="modal-fade"></div>'));
          $(ctx).addClass('modal-open');

        });
        let btnModalClose = $(ctx + '.modal-sobre-certificado .close');
        btnModalClose.on('click',function(e){
          e.preventDefault();
          $('body').find('.modal-fade').remove();
          $(ctx).removeClass('modal-open');

        });





        var location = window.location;
        if (location.hash && location.hash.indexOf('comprovante') !== -1) {
            var btnTranferencia = $('#accordion-payment [data-target="#transferencia"]');
            btnTranferencia.click();
            setTimeout(function () {
                $("html, body").animate({
                    scrollTop: btnTranferencia.offset().top
                }, 400);
            }, 1000);
        }

        if (location.hash && location.hash.indexOf('trocarPlano') !== -1 && location.hash.indexOf('planoNitroHome') !== -1) {
            if(!$('#radio-idPlano-4').is(':checked') && idPlano==4){
                $('#radio-idPlano-4').attr('checked',true);
                $("#idPlano").val(4);
            }
            $("#acao").val("trocarPlano");
        }

        if (location.hash && location.hash.indexOf('addCertificado') !== -1 && location.hash.indexOf('planoCem') !== -1) {
            $("#acao").val("addCertificado");

            let ctx = '.handle-certificado';
            $(ctx + ' .titulo,'+ ctx +' .btn-control-certificado').hide();
            $(ctx).addClass('border-0');
            $(ctx + ' .btn-control-certificado .add-certificado').click();

            var $btnVoltar = $('.step-controls').find('.btn-voltar');
            $btnVoltar.replaceWith($btnVoltar.clone());

            $('.step-controls').find('.btn-voltar').on('click',function(e){
              location.href = '/meus-veiculos';
              $(this).addClass('disabled').attr('disabled','disabled')
              stopEvent(e);
            });
        }

        window.location = '#checkout';
        var planoAtual = $('#dados-basicos #idPlano').val();
        // Se for grátis vai para a tela de selecionar um plano
        if (planoAtual === '1') {
            stepContainer.stepPlugin('goTo', '.step-plano');
            $('#form_Plano input[name="idPlano"][value="1"]')
                    .prop('checked', false)
                    .change(function (e) {
                        Confirms.info({
                            title:"Quer realmente permanecer no plano grátis?",
                            text: "O anúncio grátis não tem tanta visibilidade e não possui tantas fotos.",
                            confirmCallback:()=>{
                                e.preventDefault();
                                $(this).prop('checked', false);
                                return false;
                            }
                        })
                    });
                    advancedAlerts.info({text:'Você está no plano <b>SIMPLES</b>.<br>'
                    + 'Escolha um dos planos de acordo com a <b>prioridade</b> que deseja vender seu veículo<br>'
                    + 'Lembrando que quanto mais <b>alto o plano</b>, mais seu veículo é <b>visto</b> ;)', title:'Escolha um plano', time:25000});
        }


    }).on('step:exit:checkout', function (e) {
        window.location = '#';
    });
};

