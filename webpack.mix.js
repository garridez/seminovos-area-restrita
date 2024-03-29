let mix = require('laravel-mix');
let webpack = require('webpack');
let path = require('path');

let basePath = 'resources/assets';

mix.webpackConfig({
    plugins: [
        new webpack.IgnorePlugin({
            resourceRegExp: /^\.\/locale$/,
            contextRegExp: /moment$/
        })
    ],
    resolve: {
        modules: [path.resolve(__dirname, 'resources/assets/js'), 'node_modules'],
        alias: {
            SnBH: path.resolve('resources/assets/js/SnBH.js')
        }
    },
    stats: {
        children: true
    }
});

mix.options({
    publicPath: 'public',
    processCssUrls: false
});

mix.webpackConfig({
    output: {
        publicPath: '/',
        chunkFilename: 'js/chunks/[name].[chunkhash].js'
    }
});

mix.ts('resources/assets/js/Main.js', 'public/js/app.js');
mix.sass('resources/assets/sass/app.scss', 'public/css');
mix.sourceMaps(!mix.inProduction(), 'source-map');
mix.copy(basePath + '/img', 'public/img');
mix.copy('node_modules/snbh-site/resources/assets/img/svg', 'public/img/svg');
mix.copy(basePath + '/fonts', 'public/fonts');
mix.copy('node_modules/@fortawesome/fontawesome-free/webfonts', 'public/webfonts');

