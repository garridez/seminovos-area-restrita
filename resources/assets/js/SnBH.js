"use strict";

const $ = require('jquery');

const SnBH = {
    autoRun: {
        isFinished: false,
        registered: {},
        registerCallback: function (seletor, callback, prepend) {
            seletor = seletor && seletor.trim();

            if ("string" === typeof seletor
                    && seletor !== ""
                    && typeof callback === "function") {

                this.registered[seletor] = this.registered[seletor] || [];

                if (prepend) {
                    this.registered[seletor].unshift(callback);
                } else {
                    this.registered[seletor].push(callback);
                }
                if (this.isFinished) {
                    $("body").is(seletor) && callback();
                }
            }
        },
        run: function () {
            this.isFinished = true;
            const body = $("body");
            for (const seletor in this.registered) {
                if (body.is(seletor)) {
                    let callbacks = this.registered[seletor];
                    callbacks.map(function (callback) {
                        callback($, window, document, document.body);
                    });
                }
            }
        }
    }
};

module.exports = SnBH;