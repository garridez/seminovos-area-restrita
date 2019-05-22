require('SnBH').autoRun.registerCallback('.c-auth.a-login', function ($) {
    require('bootstrap/js/dist/modal');
    window.$ = $;
    var formsContainer = $('div.forms-group > div');

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
            $('#modalErroSenha').modal('hide')
        }, 8000);

    }
    if (url.search('#cuidado') > 0) {
        $('#modalCuidado').modal('show');
        setTimeout(function () {
            $('#modalCuidado').modal('hide')
        }, 8000);

    }
});

