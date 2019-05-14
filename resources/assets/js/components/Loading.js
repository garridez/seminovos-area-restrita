
require('components/JsBsModal');

var $ = require('jquery');

module.exports = {
    _instance: null,
    _showing: false,
    _getModal: function () {
        if (this._instance) {
            return this._instance;
        }
        var modalContent =
                '<div class="text-center">'
                + ' <div class="spinner-border text-laranja" style="width: 3rem; height: 3rem;" role="status">'
                + '  <span class="sr-only">Loading...</span>'
                + ' </div>'
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

        instance.modal({
            backdrop: 'static',
            keyboard: false
        });
        return this._instance = instance;
    },
    open: function () {
        if (this._showing) {
            return this._instance;
        }

        this._showing = true;
        var instance = this._getModal();
        instance.modal('show');
        return instance;
    },
    close: function () {
        if (!this._instance) {
            return;
        }
        this._showing = false;
        this._getModal().modal('hide');
        var self = this;
        this._getModal().on('hidden.bs.modal', function () {
            $(this).modal('dispose').remove();
            self._instance = null;
        });
        return this._getModal();
    }
};