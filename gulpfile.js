const elixir = require('laravel-elixir');
const gulp = require('gulp');
const sass = require('gulp-sass');

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

// elixir(mix => {
//     mix.sass('app.scss')
//        .version('css/app.css');
// });

gulp.task('sass', function () {
  return gulp.src('resources/assets/sass/app.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(gulp.dest('./public/css'));
});

gulp.task('js', function () {
  gulp.src('./resources/assets/js/app.js')
      .pipe(gulp.dest('./public/js'));
});
