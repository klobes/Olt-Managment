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

// Set public path
mix.setPublicPath('public');

// JavaScript files
mix.js('resources/assets/js/dashboard.js', 'public/js')
   .js('resources/assets/js/datatables-init.js', 'public/js')
   .js('resources/assets/js/olt-management.js', 'public/js')
   .js('resources/assets/js/onu-management.js', 'public/js')
   .js('resources/assets/js/bandwidth-profiles.js', 'public/js')
   .js('resources/assets/js/settings.js', 'public/js')
   .js('resources/assets/js/topology.js', 'public/js');

// CSS files
mix.sass('resources/assets/css/plugin.scss', 'public/css')
   .sass('resources/assets/css/dashboard.scss', 'public/css')
   .sass('resources/assets/css/olt-management.scss', 'public/css')
   .sass('resources/assets/css/onu-management.scss', 'public/css')
   .css('resources/assets/css/topology.css', 'public/css');

// Options
mix.options({
    processCssUrls: false,
    postCss: [
        require('autoprefixer')
    ]
});

// Versioning for cache busting in production
if (mix.inProduction()) {
    mix.version();
} else {
    mix.sourceMaps();
}

// BrowserSync for live reload during development
mix.browserSync({
    proxy: 'localhost',
    files: [
        'resources/views/**/*.blade.php',
        'resources/assets/js/**/*.js',
        'resources/assets/css/**/*.css'
    ]
});