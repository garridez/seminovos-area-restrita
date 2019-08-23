module.exports.seletor = ".c-meus-veiculos.a-chat";
var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
module.exports.callback = $ => {
    import('components/Chat').then(function (Chat) {

        new Chat($('.section-chat'));
        var jQuery = require("jquery");
        window.jQuery = jQuery;
        /**
         require("emojionearea");
         window.el = $("#text_area_chat").emojioneArea({});
         window.el[0].emojioneArea.saveEmojisAs = "shortname";*/
        if (!isMobile) {
            $(".conversation-active").removeClass("invisible");
            return;
        }
        $(".chat").click(function (e) {
            $(".conversation-active").removeClass("invisible");
            $(".list-chats").addClass("invisible");
        });
        $(".chat-back").click(function (e) {
            $(".conversation-active").addClass("invisible");
            $(".list-chats").removeClass("invisible");
        });
    })

};
