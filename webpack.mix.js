let mix = require('laravel-mix');
let path = require('path');

let basePath = 'resources/assets';

mix.webpackConfig({
    resolve: {
        alias: {
            SnBH: path.resolve('resources/assets/js/SnBH.js')
        }
    }
});

mix.options({
    publicPath: 'public',
    processCssUrls: false
});

mix.js('resources/assets/js/Main.js', 'public/js/app.js');
mix.standaloneSass('resources/assets/sass/app.scss', 'public/css');
mix.copy(basePath + '/img', 'public/img');
mix.copy(basePath + '/fonts', 'public/fonts');
mix.copy('node_modules/font-awesome/fonts', 'public/node_modules/font-awesome/fonts');

