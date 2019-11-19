window.$ = require('jquery');
require('./ads.js');


require('jquery')(function () {
    // Inicia a aplicação
    var SnBH = require('./SnBH')
            .autoRun
            .requireAndRegister();
    /**
     * @todo quando puder fazer um anúncio sem login, remove isso
     */
    if (window.SnBHRunning !== true) {
        window.SnBHRunning = true;
        SnBH.run();
    }
});
