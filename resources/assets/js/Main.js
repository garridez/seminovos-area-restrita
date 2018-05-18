const $ = require('jquery');

import 'bootstrap';

//const SnBH = require('./SnBH');;
//SnBH.a = 'Sou a';

// Inclui todos arquivos '.js'  dentro de 'mvc'
(function (r) {
    r.keys().forEach(r);
}(require.context('./mvc', true, /\.js$/)));


// Inicia a aplicação


$(function () {
    require('./SnBH').autoRun.run();
});

