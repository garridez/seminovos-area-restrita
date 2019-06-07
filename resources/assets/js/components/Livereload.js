module.exports = function () {
    /**
     * @todo busca do env se é ou não dev
     */
    console.log();
    var host = window.location.host;
    var isDev = false;
    if (host.indexOf('localhost') !== -1) {
        isDev = true;
    }
    if (host.indexOf('192') !== -1) {
        isDev = true;
    }
    if (host.indexOf('172') !== -1) {
        isDev = true;
    }

    if (!isDev) {
        return;
    }
    var script = document.createElement('script');

    script.src = 'http://' + window.location.hostname + ':35729/livereload.js?ext=Chrome&extver=2.1.0';
    document.head.appendChild(script);
};  