let mix = require('laravel-mix');
let webpack = require('webpack');
let path = require('path');

let basePath = 'resources/assets';

mix.webpackConfig({
    plugins: [
      new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/)
    ],
    resolve: {
        modules: [path.resolve(__dirname, 'resources/assets/js'), 'node_modules'],
        alias: {
            SnBH: path.resolve('resources/assets/js/SnBH.js')
        }
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

mix.react('resources/assets/js/Main.js', 'public/js/app.js');
mix.sass('resources/assets/sass/app.scss', 'public/css');
mix.sourceMaps(!mix.inProduction(), 'source-map');
mix.copy(basePath + '/img', 'public/img');
mix.copy('node_modules/snbh-site/resources/assets/img/svg', 'public/img/svg');
mix.copy(basePath + '/fonts', 'public/fonts');
mix.copy('node_modules/font-awesome/fonts', 'public/node_modules/font-awesome/fonts');

