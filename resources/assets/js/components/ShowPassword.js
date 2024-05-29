module.exports = ShowPassword;

function ShowPassword(input) {
    let button = $(input).parent().find('.input-group-text');
    $(button).on('click', function () {
        $(this).parent().find('.fa').toggleClass('fa-eye fa-eye-slash');
        $(this).parent().find('i').hasClass('fa-eye')
            ? $(this).parent().parent().find('input').attr('type', 'text')
            : $(this).parent().parent().find('input').attr('type', 'password');
    });
}
