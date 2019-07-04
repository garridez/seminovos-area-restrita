require('SnBH').autoRun.registerCallback('.c-auth.a-login', function ($) {
    require('bootstrap/js/dist/modal');
    var HandleApiError = require('components/HandleApiError');
    var Alert = require('components/Alerts');
    var formsContainer = $('div.forms-group > div');

    var ShowPassword = require('components/ShowPassword');
    ShowPassword($("input[type='password']"));
    
    formsContainer.filter('.hide').hide().removeClass('hide');
    $('form.tipo-cadastro-container input').change(function () {
        let seletectedForm = '.' + $(this).val();
        formsContainer
                .slideUp()
                .filter(seletectedForm)
                .slideDown();
    });
    var url = window.location.href;
    if (url.search('#erro') > 0) {
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
                Alert.info('Confira a caixa de entrada do email <b>' + email + '</b>', 'Email enviado');
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

