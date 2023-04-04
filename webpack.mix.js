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

mix.js('resources/js/app.js', 'js')
    .extract([
        'jquery',
        'bootstrap',
        'moment',
        'bootbox',
        'toastr',
        'admin-lte',
        'select2',
        '@popperjs/core',
        '@eonasdan/tempus-dominus'
    ])
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
    .styles([
        'resources/css/installer.css'   
    ], 'public/css/installer.min.css')
    .sourceMaps()
    .js('resources/js/loans.js', 'js')
    .js('resources/js/setups.js', 'js')
    .preact()
    .webpackConfig({
        resolve: {
            alias: {
                'react': 'preact/compat',
                'react-dom/test-utils': 'preact/test-utils',
                'react-dom': 'preact/compat',     // Must be below test-utils
                'react/jsx-runtime': 'preact/jsx-runtime'
            },
        }
    });

mix.js('resources/js/signage.js', 'js').preact();