if (typeof window !== 'undefined' && require) {
    // @ts-ignore
    for (const key in require.cache) {
        const module: any = require.cache[key];

        if (module.exports && module.exports.seletor) {
            if (typeof module.exports === 'function') {
                module.exports();
            } else if (typeof module.exports.default === 'function') {
                module.exports.default();
            }
        }
    }
}
