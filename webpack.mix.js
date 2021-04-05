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

mix.js('resources/js/app.js', 'public/js')
    .js('resources/js/sw.js', 'public')
    .copy('resources/logo.svg', 'public/logo.svg')
    .copy('resources/logo_purple.svg', 'public/logo_purple.svg')
    .copy('resources/site.webmanifest', 'public/site.webmanifest')
    .postCss('resources/css/app.css', 'public/css').options({
        postCss: [
            require('tailwindcss'), //require('@tailwindcss/jit'), not working :(
            require('autoprefixer'),
            require('postcss-import')
        ],
    });

if (mix.inProduction()) {
    mix.version();
}
