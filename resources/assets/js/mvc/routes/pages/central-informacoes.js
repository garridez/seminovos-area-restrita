module.exports.seletor = '.c-pages.a-central-informacoes';

module.exports.callback = ($) => {
    var animationTime = 200;
    $('body').on('click', '.opcoes .opcao', function () {
        var target = $(this).data('target');
        if (!target) return;
        var $target = $(target);

        var action = 'open';
        if ($target.hasClass('collapsed')) action = 'close';

        $('.content >.conteudo >div').slideUp(animationTime).addClass('collapsed');

        if (action == 'close') {
            $target.removeClass('collapsed');
            $target.slideDown(animationTime);

            window.setTimeout(() => {
                $('html, body').animate(
                    {
                        scrollTop: $(target).offset().top - 50,
                    },
                    200,
                );
            }, animationTime);
            return;
        }

        $target.addClass('collapsed');
        $target.slideUp(animationTime);
        return;
    });
};
