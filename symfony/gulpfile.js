const { src, dest, parallel } = require('gulp');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const cssnano = require('gulp-cssnano');

function css() {
  return src([
      'asset/bootstrap.css',
      'asset/style.css',
    ])
    .pipe(cssnano())
    .pipe(concat('app.css'))
    .pipe(dest('asset'))
}

function cssBottom() {
  return src([
      'asset/lib/fancybox/jquery.fancybox.css',
    ])
    .pipe(cssnano())
    .pipe(concat('app.bottom.css'))
    .pipe(dest('asset'))
}

function js() {
  return src([
    'asset/jquery.js',
    'asset/lib/fancybox/jquery.fancybox.js',
    'bundles/fosjsrouting/js/router.min.js',
    'asset/lib/cleave/cleave.js',
    'bundles/bazingajstranslation/js/translator.min.js',
    'asset/main.js',
  ], { sourcemaps: true })
    .pipe(uglify())
    .pipe(concat('app.js'))
    .pipe(dest('asset', { sourcemaps: true }))
}

exports.js = js;
exports.css = css;
exports.cssBottom = cssBottom;
exports.default = parallel(css, cssBottom, js);
