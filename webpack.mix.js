const mix = require("laravel-mix");

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

mix
  .js("resources/js/app.js", "public/js")
  .js("resources/js/adminlte.js", "public/js")
  .copy("resources/js/common.js", "public/js")
  .copy("resources/js/timecard.js", "public/js")
  .copy("resources/js/holiday.js", "public/js")
  .copy("public/plugins/tabulator/dist/js/tabulator.min.js", "public/js")
  .sass("resources/sass/app.scss", "public/css")
  .sass("public/plugins/tabulator/src/scss/tabulator_custom.scss", "public/css")
  .sass("public/plugins/tabulator/src/scss/tabulator.scss", "public/css")
  .sass("public/plugins/tabulator/src/scss/bootstrap/tabulator_bootstrap4.scss", "public/css")
  .styles(["resources/css/adminlte.css"], "public/css/adminlte.css");
