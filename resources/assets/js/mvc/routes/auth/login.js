require('SnBH').autoRun.registerCallback('.c-auth.a-login', function ($) {
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
});

