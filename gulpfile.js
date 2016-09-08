const gulp = require('gulp');
const sass = require('gulp-sass');
const uglify = require('gulp-uglify');

gulp.task('css', function () {
  return gulp.src('resources/assets/sass/app.scss')
    .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
    .pipe(gulp.dest('./public/css'));
});

gulp.task('js', function () {
  gulp.src('./resources/assets/js/app.js')
    .pipe(uglify())
    .pipe(gulp.dest('./public/js'));
});

gulp.task('watch', function () {
  gulp.watch('./resources/**/*.scss', ['css']);
  gulp.watch('./resources/**/*.js', ['js']);
});
