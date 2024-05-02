import * as $ from 'jquery';
import SnBH from './SnBH';
window.$ = $;
//require('./ads.js');

$(function () {
    // Inicia a aplicação
    SnBH.autoRun.requireAndRegister();
    /**
     * @todo quando puder fazer um anúncio sem login, remove isso
     */
    if (window.SnBHRunning !== true) {
        window.SnBHRunning = true;
        SnBH.autoRun.run();
    }
});
