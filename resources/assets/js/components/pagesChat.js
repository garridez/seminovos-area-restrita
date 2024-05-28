module.exports = function ($) {
    'use strict';
    //To Open Chat When Clicked
    $('[data-chat-input]').on('keypress', function (e) {
        if (e.which == 13) {
            var el = $(this).attr('data-chat-conversation');
            $(el).append(
                '<div class="message clearfix">' +
                    '<div class="chat-bubble from-me">' +
                    $(this).val() +
                    '</div></div>',
            );
            $(this).val('');
        }
    });
};
