import './JsBsModal';

import $ from 'jquery';

//const Loading: LoadingType = {
class Loading {
    _instance: JQuery<HTMLElement> | null = null;
    _showing: boolean = false;
    _persistent: boolean = false;
    _feedbackTexts: string | string[] | false = false;
    _feedbackTextsCycle: boolean = false;
    _getModal() {
        if (this._instance) {
            return this._instance;
        }
        const modalContent =
            '<div class="text-center">' +
            ' <div class="spinner-border text-laranja" style="width: 3rem; height: 3rem;" role="status">' +
            '  <span class="sr-only">Loading...</span>' +
            ' </div>' +
            ' <div class="feedback-text text-white animated pulse infinite">Carregando...</div>' +
            '</div>';

        const instance = $.jsBsModal({
            autoShow: false,
            contents: {
                'modal-content': modalContent,
                close: false,
                'modal-header': false,
            },
        });

        instance.attr('title', 'Carregando...').addClass('ignore-help');
        instance.find('.modal-content').addClass('loading-container');
        this._configureDisplayText(instance);
        instance.modal({
            backdrop: 'static',
            keyboard: false,
        });
        return (this._instance = instance);
    }
    open(persistent?: boolean) {
        if (persistent !== undefined) {
            this._persistent = persistent;
        }
        if (this._showing) {
            return this._instance;
        }

        this._showing = true;
        const instance = this._getModal();
        instance.modal('show');
        return instance;
    }
    close(forceClose: boolean = false) {
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
        const modal = this._getModal().modal('hide');
        const self = this;
        // Para garantir que o modal vai desparecer caso o close seja chamado muito rapido
        const intervalID = setInterval(function () {
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
    }
    /**
     *
     * @param string|array texts Texto ou um array texto
     * @returns {undefined}
     */
    addFeedbackTexts(texts: string | string[], cycle: boolean = false) {
        this._feedbackTextsCycle = cycle;
        if (!Array.isArray(texts)) {
            texts = [texts];
        }
        this._feedbackTexts = texts;
    }
    _configureDisplayText(instance: JQuery<HTMLElement>) {
        const self = this;
        const feedbackElement = instance.find('.feedback-text');
        const displayFeedbackText = function () {
            const texts = self._feedbackTexts;
            if (!Array.isArray(texts) || !texts.length) {
                return false;
            }
            let indexFeedback = feedbackElement.data('indexFeedback');
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
        const interval = setInterval(displayFeedbackText, 4000);
        instance.on('hide.bs.modal', function () {
            clearInterval(interval);
        });
    }
}

export default new Loading();
