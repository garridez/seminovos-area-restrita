
module.exports = function () {
    var {isDev} = require('components/Env');

    if (!isDev) {
        return;
    }
    var script = document.createElement('script');

    script.src = 'http://' + window.location.hostname + ':35729/livereload.js?ext=Chrome&extver=2.1.0';
    document.head.appendChild(script);
};
