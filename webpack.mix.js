const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.combine([
        'node_modules/jquery/dist/jquery.min.js',
        'node_modules/bootstrap/dist/js/bootstrap.bundle.min.js',
        'node_modules/moment/min/moment.min.js',
        'node_modules/bootbox/dist/bootbox.min.js',
        'node_modules/toastr/build/toastr.min.js',
        'node_modules/admin-lte/dist/js/adminlte.min.js',
        'node_modules/select2/dist/js/select2.full.min.js',
        'node_modules/@popperjs/core/dist/umd/popper.min.js',
        'node_modules/@eonasdan/tempus-dominus/dist/js/tempus-dominus.min.js',
        'resources/js/app.js'
    ], 'public/js/app.js')
    .copyDirectory('node_modules/@fortawesome/fontawesome-free/webfonts', 'public/webfonts')
    .styles([
        'node_modules/bootstrap/dist/css/bootstrap.min.css',
        'node_modules/@fortawesome/fontawesome-free/css/all.min.css',
        'node_modules/toastr/build/toastr.min.css',
        'node_modules/admin-lte/dist/css/adminlte.min.css',
        'node_modules/select2/dist/css/select2.min.css',
        'node_modules/select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css',
        'node_modules/@eonasdan/tempus-dominus/dist/css/tempus-dominus.min.css',
        'resources/css/app.css'
    ], 'public/css/app.css')
    .sourceMaps();

mix.js('resources/js/signage.js', 'js');