require('SnBH').autoRun.registerCallback('.c-auth.a-login', function ($) {
    require('bootstrap/js/dist/modal');
    var HandleApiError = require('components/HandleApiError');
    var Alert = require('components/Alerts');
    var formsContainer = $('div.forms-group > div');

    var ShowPassword = require('components/ShowPassword');
    ShowPassword($("input[type='password']"));
    
    $("body").on("click", "input.radioTipoCadastro[data-cookie]", function(e){
        let $this = $(this);
        let cookieDate = new Date;
        cookieDate.setFullYear(cookieDate.getFullYear() +1);
        document.cookie = `login-tipoCadastro=${$this.data("cookie")}; expires=${cookieDate.toGMTString()};`;
    });

    formsContainer.filter('.hide').hide().removeClass('hide');
    $('form.tipo-cadastro-container input').change(function () {
        let seletectedForm = '.' + $(this).val();
        formsContainer
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
    $('#formLembrarSenha').submit(function (e) {
        e.preventDefault();
        $('#modalLembrarSenha').modal('hide');
        var email = $(this).find('#emailLembrarSenha').val();
        $.ajax({
            type: 'POST',
            url: '/remember-pass',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (data) {
                if (!HandleApiError(data)) {
                    return;
                }
                
                // Alert.info('Confira a caixa de entrada do email <b>' + email + '</b>', 'Email enviado');
                $('#emailEnviado').text(data.email);
                $('#modalEmailEnviado').modal('show');
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
});

