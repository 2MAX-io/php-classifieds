const { src, dest, parallel, series } = require('gulp');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const cssnano = require('gulp-cssnano');
const sourcemaps = require('gulp-sourcemaps');
const cleanCSS = require('gulp-clean-css');
const rev = require('gulp-rev');
const rename = require('gulp-rename');
const clean = require('gulp-clean');

function cleanBuild() {
  return src([
    'asset/build/*-*.js',
    'asset/build/*-*.css',
    'asset/build/sourcemaps/*.map',
  ], {read: false})
      .pipe(clean());
}

function css() {
  return src([
      'asset/lib/bootstrap/bootstrap.css',
      'asset/style.css',
    ])
    .pipe(sourcemaps.init())
    .pipe(concat('app.css'))
    .pipe(cleanCSS({level: {1: {specialComments: false}}}))
    .pipe(cssnano())
    .pipe(rev())
    .pipe(sourcemaps.write('sourcemaps'))
    .pipe(dest('asset/build'))
    .pipe(rename({
      dirname: "asset/build" // rename dir in manifest
    }))
    .pipe(rev.manifest('asset/build/rev-manifest.json', {
      base: 'asset/build',
      merge: true
    }))
    .pipe(dest('asset/build'))
}

function adminCss() {
  return src([
      'asset/lib/bootstrap/bootstrap.css',
      'asset/style.css',
      'asset/admin.css',
    ])
    .pipe(sourcemaps.init())
    .pipe(concat('admin.css'))
    .pipe(cleanCSS({level: {1: {specialComments: false}}}))
    .pipe(cssnano())
    .pipe(rev())
    .pipe(sourcemaps.write('sourcemaps'))
    .pipe(dest('asset/build'))
    .pipe(rename({
      dirname: "asset/build" // rename dir in manifest
    }))
    .pipe(rev.manifest('asset/build/rev-manifest.json', {
      base: 'asset/build',
      merge: true
    }))
    .pipe(dest('asset/build'))
}

function cssBottom() {
  return src([
      'asset/lib/fancybox/jquery.fancybox.css',
    ])
    .pipe(sourcemaps.init())
    .pipe(concat('app.bottom.css'))
    .pipe(cleanCSS({level: {1: {specialComments: false}}}))
    .pipe(cssnano())
    .pipe(rev())
    .pipe(sourcemaps.write('sourcemaps'))
    .pipe(dest('asset/build'))
    .pipe(rename({
      dirname: "asset/build" // rename dir in manifest
    }))
    .pipe(rev.manifest('asset/build/rev-manifest.json', {
      base: 'asset/build',
      merge: true
    }))
    .pipe(dest('asset/build'))
}

function js() {
  return src([
    'asset/lib/jquery/jquery.js',
    'asset/bundles/fosjsrouting/js/router.js',
    'asset/bundles/bazingajstranslation/js/translator.min.js',
    'asset/lib/fancybox/jquery.fancybox.js',
    'asset/lib/cleave/cleave.js',
    'asset/lib/bootstrap/bootstrap.bundle.js',
    'asset/component/textarea_autosize/textarea-autosize.js',
    'asset/main.js',
  ])
    .pipe(sourcemaps.init())
    .pipe(concat('app.js'))
    .pipe(uglify())
    .pipe(rev())
    .pipe(sourcemaps.write('sourcemaps'))
    .pipe(dest('asset/build'))
    .pipe(rename({
      dirname: "asset/build" // rename dir in manifest
    }))
    .pipe(rev.manifest('asset/build/rev-manifest.json', {
      base: 'asset/build',
      merge: true
    }))
    .pipe(dest('asset/build'))
}

function adminJs() {
  return src([
    'asset/lib/jquery/jquery.js',
    'asset/bundles/fosjsrouting/js/router.js',
    'asset/bundles/bazingajstranslation/js/translator.min.js',
    'asset/lib/sortable_js/Sortable.min.js',
    'asset/lib/sortable_js/jquery-sortable.js',
    'asset/lib/cleave/cleave.js',
    'asset/lib/bootstrap/bootstrap.bundle.js',
    'asset/component/textarea_autosize/textarea-autosize.js',
    'asset/main.js',
    'asset/main-admin.js',
  ])
    .pipe(sourcemaps.init())
    .pipe(concat('admin.js'))
    .pipe(uglify())
    .pipe(rev())
    .pipe(sourcemaps.write('sourcemaps'))
    .pipe(dest('asset/build'))
    .pipe(rename({
      dirname: "asset/build" // rename dir in manifest
    }))
    .pipe(rev.manifest('asset/build/rev-manifest.json', {
      base: 'asset/build',
      merge: true
    }))
    .pipe(dest('asset/build'))
}

exports.js = js;
exports.css = css;
exports.cssBottom = cssBottom;
exports.adminCss = adminCss;
exports.adminJs = adminJs;
exports.default = series(cleanBuild, css, cssBottom, js, adminCss, adminJs);
