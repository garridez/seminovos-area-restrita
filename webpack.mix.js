/* eslint @typescript-eslint/no-var-requires: 0 */
const tsNode = require('ts-node');
const tsConfigPaths = require('tsconfig-paths');

tsNode.register({
    esm: 'node',
    project: './tsconfig.json',
});

tsConfigPaths.register({
    baseUrl: './',
    paths: {},
});

require('tsx/cjs');

const mix = require('laravel-mix');
const path = require('path');
const glob = require('glob');
const plugins = [];

const isProd = mix.inProduction();

if (!isProd) {
    const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
    plugins.push(
        new BundleAnalyzerPlugin({
            analyzerMode: 'static',
            openAnalyzer: false,
            reportFilename: 'webpack-report.html',
            defaultSizes: 'gzip',
            generateStatsFile: false,
            statsFilename: 'webpack-stats.json',
            statsOptions: null,
            logLevel: 'info',
            excludeAssets: [],
        }),
    );
}

mix.webpackConfig({
    plugins: [...plugins],
    stats: {
        cachedModules: true,
    },
});

mix.options({
    publicPath: 'public',
    processCssUrls: false,
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
        },
    },
});

const mvcPartials = glob.sync('resources/assets/js/mvc-partial/*/*/*.{tsx,js,ts}');
const mvcPartialsSelectorsGroup = {};

console.log('MVC Partial');
for (let filename of mvcPartials) {
    const moduleData = require(filename);
    console.log(moduleData.seletor + ': ' + filename);
    mvcPartialsSelectorsGroup[moduleData.seletor] =
        mvcPartialsSelectorsGroup[moduleData.seletor] || [];
    mvcPartialsSelectorsGroup[moduleData.seletor].push(filename);
}

for (let selector in mvcPartialsSelectorsGroup) {
    let filename = mvcPartialsSelectorsGroup[selector];
    mix.ts(
        [...filename, 'resources/assets/js/mvc-partial/runner.ts'],
        'public/js/mvc-partial/' + selector + '.js',
    );
}

mix.ts('resources/assets/js/Main.js', 'public/js/app.js');
mix.sass('resources/assets/sass/app.scss', 'public/css');
mix.copy('resources/assets/img', 'public/img');
mix.copy('resources/assets/fonts', 'public/fonts');
mix.copy('node_modules/snbh-site/resources/assets/img/svg', 'public/img/svg');
mix.copy('node_modules/@fortawesome/fontawesome-free/webfonts', 'public/webfonts');
mix.sourceMaps(!mix.inProduction());
