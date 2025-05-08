/* eslint-disable no-unreachable */
import env from './Env';

export default function () {
    return;
    if (!env.isDev) {
        return;
    }
    var script = document.createElement('script');

    script.src =
        'http://' + window.location.hostname + ':35729/livereload.js?ext=Chrome&extver=2.1.0';
    document.head.appendChild(script);
};
