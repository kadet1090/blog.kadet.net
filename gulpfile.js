var gulp = require("gulp");
var merge = require("merge-stream");
var sass = require('gulp-sass');
var concat = require('gulp-concat');
var gutil = require('gulp-util');
var autoprefixer = require('gulp-autoprefixer');

var dir = 'src/',
    out = 'web/';

var watch = !gutil.env.production;

gulp.task('fonts', function() {
    return gulp.src('node_modules/font-awesome/fonts/*')
        .pipe(gulp.dest(out + 'fonts'))
});

gulp.task("styles", function () {
    var scss =
        gulp.src(dir + 'sass/*.scss').pipe(sass({
            includePaths: [
                'node_modules/bootstrap-sass/assets/stylesheets/'
            ]
        }))
        .on('error', onError)
        .pipe(autoprefixer({
            browsers: ['last 5 versions'],
            cascade: false
        }))
        .pipe(gulp.dest(out + 'css/'));
});

gulp.task("images", function () {
    return gulp.src(dir + "img/**/*.{png,jpg}").pipe(gulp.dest(out + 'img'));
});

gulp.task("default", ["styles", "images", "fonts"]);
gulp.task('watch', ['default'], function() {
    watch = true;

    gulp.watch(dir + 'sass/**/*',   ['styles']);
    gulp.watch(dir + 'img/**/*',   ['images']);
});
function onError(error) {
    gutil.log(error.toString());
    this.emit('end');
}
