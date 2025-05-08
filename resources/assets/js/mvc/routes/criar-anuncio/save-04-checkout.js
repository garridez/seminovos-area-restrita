import 'bootstrap/js/dist/util.js';
import 'bootstrap/js/dist/collapse.js';

import cc from './checkout/cartao-de-credito';
import pagseguro from './checkout/pagseguro';
import pix from './checkout/pix';
import transferenciaFinalizar from './checkout/transferencia-finalizar';
import transferenciaUpload from './checkout/transferencia-upload';

export const seletor = '.c-criar-anuncio.a-index';
export const callback = ($) => {
    var handlers = {
        cc,
        pagseguro,
        pix,
        transferenciaFinalizar,
        transferenciaUpload,
    };

    var initialized = false;
    function init() {
        if (initialized) {
            return;
        }
        initialized = true;

        handlers.cc();
        handlers.pix();
        handlers.pagseguro();
        handlers.transferenciaFinalizar();
        handlers.transferenciaUpload();
    }
    $('.step-container').on('step:change:checkout', init);
};
