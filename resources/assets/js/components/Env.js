var isDev = /(localhost|192|172)/.test(window.location.host);
export default {
    isDev,
    isProd: !isDev,
};
