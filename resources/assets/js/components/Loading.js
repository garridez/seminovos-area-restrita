
require('components/JsBsModal');

var $ = require('jquery');

module.exports = {
    _instance: null,
    _showing: false,
    _persistent: false,
    _feedbackTexts: false,
    _feedbackTextsCycle: false,
    _getModal: function () {
        if (this._instance) {
            return this._instance;
        }
        var modalContent =
                '<div class="text-center">'
                + ' <div class="spinner-border text-laranja" style="width: 3rem; height: 3rem;" role="status">'
                + '  <span class="sr-only">Loading...</span>'
                + ' </div>'
                + ' <div class="feedback-text text-white animated pulse infinite">Carregando...</div>'
                + '</div>';

        var instance = $.jsBsModal({
            autoShow: false,
            contents: {
                'modal-content': modalContent,
                'close': false,
                'modal-header': false,
            }
        });

        instance.attr('title', 'Carregando...');
        instance.find('.modal-content')
                .addClass('loading-container');
        this._configureDisplayText(instance);
        instance.modal({
            backdrop: 'static',
            keyboard: false
        });
        return this._instance = instance;
    },
    open: function (persistent) {
        if (persistent !== undefined) {
            this._persistent = persistent;
        }
        if (this._showing) {
            return this._instance;
        }

        this._showing = true;
        var instance = this._getModal();
        instance.modal('show');
        return instance;
    },
    close: function (forceClose) {
        if (forceClose) {
            this._persistent = false;
        }
        if (this._persistent) {
            return;
        }
        this._showing = false;
        if (!this._instance) {
            return;
        }
        this._feedbackTexts = false;
        var modal = this._getModal().modal('hide');
        var self = this;
        // Para garantir que o modal vai desparecer caso o close seja chamado muito rapido
        var intervalID = setInterval(function () {
            if (modal) {
                modal.modal('hide');
            } else {
                clearInterval(intervalID);
            }
        }, 200);
        this._getModal().on('hidden.bs.modal', function () {
            $(this).modal('dispose').remove();
            self._instance = null;
            clearInterval(intervalID);
        });
        return this._getModal();
    },
    /**
     * 
     * @param string|array texts Texto ou um array texto
     * @returns {undefined}
     */
    addFeedbackTexts: function (texts, cycle) {
        this._feedbackTextsCycle = cycle;
        if (!Array.isArray(texts)) {
            texts = [texts];
        }
        this._feedbackTexts = texts;
    },
    _configureDisplayText: function (instance) {
        var self = this;
        var feedbackElement = instance.find('.feedback-text');
        var displayFeedbackText = function () {
            var texts = self._feedbackTexts;
            if (!Array.isArray(texts) || !texts.length) {
                return false;
            }
            var indexFeedback = feedbackElement.data('indexFeedback');
            if (indexFeedback === undefined) {
                indexFeedback = -1;
            }
            indexFeedback++;
            if (indexFeedback >= texts.length) {
                indexFeedback = 0;
                if (!self._feedbackTextsCycle) {
                    return false;
                }
            }
            feedbackElement.data('indexFeedback', indexFeedback);
            feedbackElement.fadeOut(function () {
                $(this).html(texts[indexFeedback]).fadeIn();
            });
            return true;
        };
        displayFeedbackText();
        var interval = setInterval(displayFeedbackText, 4000);
        instance.on('hide.bs.modal', function () {
            clearInterval(interval);
        });
    },

};
