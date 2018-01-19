var gulp = require('gulp'),
    sass = require('gulp-sass');

    gulp.task('sass', function() {
        return gulp.src('assets/scss/style.scss')
            .pipe(sass())
            .pipe(gulp.dest('assets/css'));
    });

    gulp.task('default', ['sass', 'watch']);

    gulp.task('watch', function() {
        gulp.watch('assets/scss/*.scss', ['sass']);
    });
