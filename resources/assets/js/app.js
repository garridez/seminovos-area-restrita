const $ = require('jquery');

import 'bootstrap';

const SnBH = require('./SnBH');

import './rotas';

$(function () {
    SnBH.autoRun.run();
});

