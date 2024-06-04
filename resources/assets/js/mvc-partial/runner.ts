if (typeof window !== 'undefined' && require) {
    for (const key in require.cache) {
        const module = require.cache[key];

        if (module && module.exports && module.exports.seletor) {
            if (typeof module.exports === 'function') {
                module.exports();
            } else if (typeof module.exports.default === 'function') {
                module.exports.default();
            }
        }
    }
}
