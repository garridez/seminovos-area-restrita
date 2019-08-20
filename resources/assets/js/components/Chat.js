var $ = require("jquery");
var moment;
var Chat = function ($chat) {
    this.$chat = $chat;
    this.listChats = $chat.find('.list-chats');
    moment = require('moment');
    require('moment/locale/pt-br')

    this.templates.parent = this;

    this.init();
};
Chat.prototype = {
    setElementState: function (node, state) {
        $.each(state, function (seletor, value) {
            var element = node.find(seletor);
            var method = typeof value === 'function' ? 'each' : 'html';
            element[method](value);
        });
        return node;
    },
    templates: {
        cloneTemplate: function (seletor) {
            // É adicionado uma div e depois retirado para extrair o elemento do document-fragment
            return $('<div>').append(this.parent
                    .$chat
                    .find(seletor)
                    .get(0)
                    .content
                    .cloneNode(true)).find(' > *');

        },
        chatSubject: function (state) {
            var node = this.cloneTemplate('template.template-chat-subject');
            if (state) {
                node = this.parent.setElementState(node, state);
            }
            return node;
        }
    },
    formatters: {
        chatSubject: function (conversa) {
            var lastMsg = Object.values(conversa.mensagens)[0];
            console.log(lastMsg);
            return {
                '.chat-title': conversa.marca + conversa.modelo,
                '.chat-name': conversa.responsavelNomeInteressado,
                '.chat-last-msg': lastMsg.mensagem,
                '.chat-img': function () {
                    $(this).find('img').attr('src', conversa.foto);
                },
                '.chat-date': function () {
                    $(this)
                            .data('date', lastMsg.enviadoEm)
                            .html(moment(lastMsg.enviadoEm).calendar());
                },
                '.chat-status': 'status',
            };
        }
    },
    init: function () {
        var self = this;
        $.getJSON('/chat/mensagens')
                .then(function (data) {
                    $.each(data, function (idConversa, conversa) {
                        console.log(conversa);
                        self.addSubject(conversa);
                    });
                    self.sortChats();
                });
    },
    addSubject: function (conversa) {
        var self = this;

        var chatSubject = self.templates.chatSubject();
        chatSubject.data('chat', conversa);
        var state = this.formatters.chatSubject(conversa);
        this.setElementState(chatSubject, state);
        this.listChats.prepend(chatSubject);

    },
    setSubjectState: function (chatSubject, conversa) {
        chatSubject.data('chat', conversa);
    },
    sortChats: function () {
        this.listChats.find('> .chat').sort(function (a, b) {
            console.log($(a).data('chat'));
            a = $(a).data('chat').mensagens;
            b = $(b).data('chat').mensagens;
            a = Object.values(a)[0].enviadoEm;
            b = Object.values(b)[0].enviadoEm;
            console.log(a, b);
            return (a > b);
        });
    },

};


module.exports = Chat;