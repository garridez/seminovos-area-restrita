let mix = require('laravel-mix');

let basePath = 'resources/assets';

mix.js([
  'node_modules/jquery/dist/jquery.js',
  basePath + '/js/app.js'
], 'public/js/app.js')
	.sass('resources/assets/sass/app.scss', 'public/css');

