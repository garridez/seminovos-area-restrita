var $ = require("jquery");
var moment;
var Chat = function ($chat) {
    this.$chat = $chat;
    this.listChats = $chat.find('.list-chats');
    this.convCont = $chat.find('.conversation-container');
    moment = require('moment');
    require('moment/locale/pt-br');

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
        },
        conversation: function (state) {
            var node = this.cloneTemplate('template.template-conversation-template');
            if (state) {
                node = this.parent.setElementState(node, state);
            }
            return node;
        }
    },
    formatters: {
        chatSubject: function (conversa) {
            var lastMsg = Object.values(conversa.mensagens)[0];
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
        },
        conversation: function (conversa) {
            return {
                '.conversation-name': conversa.responsavelNomeInteressado,  
            };
        }
    },
    init: function () {
        var self = this;
        $.getJSON('/chat/mensagens')
                .then(function (data) {
                    $.each(data, function (idConversa, conversa) {
                        self.addSubject(conversa);
                    });
                    self.sortChats();
                })
                .then(function () {
                    self.setEvents();
                })
                .then(function () {
                    self.listChats.find(' > *').eq(0).click();
                });
    },
    addSubject: function (conversa) {
        var self = this;

        var chatSubject = self.templates.chatSubject();
        var conversation = self.templates.conversation();

        chatSubject.data('chat-data', conversa);
        chatSubject.data('conversation', conversation);


        var stateChatSubject = this.formatters.chatSubject(conversa);
        this.setElementState(chatSubject, stateChatSubject);

        var stateConversation = this.formatters.conversation(conversa);
        this.setElementState(conversation, stateConversation);


        this.listChats.prepend(chatSubject);

    },
    setSubjectState: function (chatSubject, conversa) {
        chatSubject.data('chat-data', conversa);
    },
    sortChats: function () {
        this.listChats.find('> .chat').sort(function (a, b) {
            a = $(a).data('chat-data').mensagens;
            b = $(b).data('chat-data').mensagens;
            a = Object.values(a)[0].enviadoEm;
            b = Object.values(b)[0].enviadoEm;
            return (a > b);
        });
    },
    setEvents: function () {
        var self = this;
        this.listChats.on('click', '.chat-subject', function () {
            self.convCont.prepend($(this).data('conversation'));
        });
    },

};


module.exports = Chat;