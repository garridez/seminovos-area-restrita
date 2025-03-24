import $ from 'jquery';

export default function showPassword(input) {
    let button = $(input).parent().find('.input-group-text');
    $(button).on('click', function () {
        $(this).parent().find('.fa').toggleClass('fa-eye fa-eye-slash');
        if ($(this).parent().find('i').hasClass('fa-eye')) {
            $(this).parent().parent().find('input').attr('type', 'text');
        } else {
            $(this).parent().parent().find('input').attr('type', 'password');
        }
    });
}
