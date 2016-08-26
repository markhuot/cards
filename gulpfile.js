const elixir = require('laravel-elixir');
const gulp = require('gulp');

require('laravel-elixir-vue');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(mix => {
    mix.sass('app.scss')
       .version('css/app.css');
});

gulp.task('js', function() {
  gulp.src('./resources/assets/js/app.js')
      .pipe(gulp.dest('./public/js'));
});
