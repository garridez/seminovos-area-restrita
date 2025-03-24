'use strict';

import $ from 'jquery';

export default {
    autoRun: {
        isFinished: false,
        registered: {},
        registerCallback: function (seletor, callback, prepend) {
            seletor = seletor && seletor.trim();

            if ('string' === typeof seletor && seletor !== '' && typeof callback === 'function') {
                this.registered[seletor] = this.registered[seletor] || [];

                if (prepend) {
                    this.registered[seletor].unshift(callback);
                } else {
                    this.registered[seletor].push(callback);
                }
                if (this.isFinished) {
                    if ($('body').is(seletor)) {
                        callback();
                    }
                }
            }
        },
        run: function () {
            this.isFinished = true;
            const body = $('body');
            for (const seletor in this.registered) {
                if (body.is(seletor)) {
                    let callbacks = this.registered[seletor];
                    callbacks.map(function (callback) {
                        callback($, window, document, document.body);
                    });
                }
            }
        },
        requireAndRegister: function () {
            const webpackContext = require.context('./mvc', true, /\.(j|t)s$/);
            var uniquePaths = [];

            webpackContext
                .keys()
                .filter(function (file) {
                    var fileNormalized = file.replace(/^\.\//, 'mvc/');
                    if (uniquePaths.includes(fileNormalized)) {
                        return false;
                    }
                    uniquePaths.push(fileNormalized);
                    return true;
                })
                .forEach((file) => {
                    let module = webpackContext(file);
                    if (module.seletor) {
                        this.registerCallback(
                            module.seletor,
                            module.callback || module,
                            !!module.prepend,
                        );
                    }
                });
            return this;
        },
    },
};
