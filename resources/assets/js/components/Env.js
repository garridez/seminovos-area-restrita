var isDev = /(localhost|192|172)/.test(window.location.host);
module.exports = {
    isDev,
    isProd: !isDev
};
