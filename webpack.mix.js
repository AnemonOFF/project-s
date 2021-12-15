const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/slept.js', 'public/js/slept.js')
    .sass('resources/css/style.scss', 'public/css/style.css')
    .css('resources/css/normalize.css', 'public/css/normalize.css');
