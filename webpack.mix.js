let mix = require('laravel-mix');
let webpack = require('webpack');
let path = require('path');
var plugins = [];

var isProd = mix.inProduction();


if (!isProd) {
    const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
    plugins.push(new BundleAnalyzerPlugin({
        analyzerMode: 'static',
        openAnalyzer: false,
        reportFilename: 'webpack-report.html',
        defaultSizes: 'gzip',
        generateStatsFile: false,
        statsFilename: 'webpack-stats.json',
        statsOptions: null,
        logLevel: 'info',
        excludeAssets: [],
    }));
}

mix.webpackConfig({
    plugins: [
        ...plugins
    ],
    resolve: {
        modules: [path.resolve(__dirname, 'resources/assets/js'), 'node_modules'],
        alias: {
            SnBH: path.resolve('resources/assets/js/SnBH.js')
        }
    },
    stats: {
        cachedModules: true
    }
});

mix.options({
    publicPath: 'public',
    processCssUrls: false
});

mix.webpackConfig({
    output: {
        publicPath: '/',
        chunkFilename: 'js/chunks/[name].[chunkhash].js',
        sourceMapFilename: 'js/map/[file].map',
    },
    optimization: {
        splitChunks: {
            chunks: 'async',
        }
    },
});

mix.ts('resources/assets/js/Main.js', 'public/js/app.js');
mix.sass('resources/assets/sass/app.scss', 'public/css');
mix.sourceMaps(!mix.inProduction(), 'source-map');
mix.copy('resources/assets/img', 'public/img');
mix.copy('resources/assets/fonts', 'public/fonts');
mix.copy('node_modules/snbh-site/resources/assets/img/svg', 'public/img/svg');
mix.copy('node_modules/@fortawesome/fontawesome-free/webfonts', 'public/webfonts');

