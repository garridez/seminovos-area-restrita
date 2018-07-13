window.$ = require('jquery');

require('jquery')(function () {
    // Inicia a aplicação
    require('./SnBH')
            .autoRun
            .requireAndRegister()
            .run();
});
