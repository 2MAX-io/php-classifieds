const { src, dest, parallel } = require('gulp');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const cssnano = require('gulp-cssnano');
const sourcemaps = require('gulp-sourcemaps');
const cleanCSS = require('gulp-clean-css');
const rev = require('gulp-rev');
const rename = require('gulp-rename');

function css() {
  return src([
      'asset/bootstrap.css',
      'asset/style.css',
    ])
    .pipe(concat('app.css'))
    .pipe(cleanCSS({level: {1: {specialComments: false}}}))
    .pipe(cssnano())
    // .pipe(rev())
    // .pipe(dest('asset/build'))
    // .pipe(rename({
    //   dirname: "asset/build" // rename dir in manifest
    // }))
    // .pipe(rev.manifest('asset/build/rev-manifest.json', {
    //   base: 'asset/build',
    //   merge: true
    // }))
    .pipe(dest('asset/build'))
}

function cssBottom() {
  return src([
      'asset/lib/fancybox/jquery.fancybox.css',
    ])
    .pipe(concat('app.bottom.css'))
    .pipe(cleanCSS({level: {1: {specialComments: false}}}))
    .pipe(cssnano())
    // .pipe(rev())
    // .pipe(dest('asset/build'))
    // .pipe(rename({
    //   dirname: "asset/build" // rename dir in manifest
    // }))
    // .pipe(rev.manifest('asset/build/rev-manifest.json', {
    //   base: 'asset/build',
    //   merge: true
    // }))
    .pipe(dest('asset/build'))
}

function js() {
  return src([
    'asset/jquery.js',
    'asset/bundles/fosjsrouting/js/router.js',
    'asset/bundles/bazingajstranslation/js/translator.min.js',
    'asset/lib/fancybox/jquery.fancybox.js',
    'asset/lib/cleave/cleave.js',
    'asset/bootstrap.bundle.js',
    'asset/main.js',
  ])
    .pipe(sourcemaps.init())
    .pipe(concat('app.js'))
    .pipe(uglify())
    .pipe(sourcemaps.write('sourcemaps'))
    // .pipe(rev())
    // .pipe(dest('asset/build'))
    // .pipe(rename({
    //   dirname: "asset/build" // rename dir in manifest
    // }))
    // .pipe(rev.manifest('asset/build/rev-manifest.json', {
    //   base: 'asset/build',
    //   merge: true
    // }))
    .pipe(dest('asset/build'))
}

exports.js = js;
exports.css = css;
exports.cssBottom = cssBottom;
exports.default = parallel(css, cssBottom, js);
