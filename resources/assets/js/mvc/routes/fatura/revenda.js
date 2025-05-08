import 'printthis';
export const seletor = '.c-fatura.a-revenda';
export const callback = ($) => {
    $(document).bind('keydown', function (e) {
        if (e.ctrlKey && e.keyCode == 80) {
            $('.card-body').printThis({
                loadCSS: '/css/app.css',
            });
            return false;
        }
    });
    $('.btn-print').click(function () {
        $('.card-body').printThis({
            loadCSS: '/css/app.css',
        });
    });
};
