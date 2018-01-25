var gulp = require('gulp');
var zip = require('gulp-zip');
var clean = require('gulp-clean');
var pump = require('pump');

// Copy template
gulp.task('copy_tmpl', function(){
    return gulp.src(['./templates/shaper_helixultimate/**/*.*', '!./templates/shaper_helixultimate/en-GB.tpl_shaper_helixultimate.ini'])
        .pipe(gulp.dest('build/template'))
});

// Copy Template Language
gulp.task('copy_tmpl_lang', function(){
    return gulp.src(['./language/en-GB/en-GB.tpl_shaper_helixultimate.ini'])
        .pipe(gulp.dest('build/template'))
});

// Copy system plugin
gulp.task('copy_system_plugin', function(){
    return gulp.src(['./plugins/system/helixultimate/**/*.*', '!./plugins/system/language/en-GB.plg_system_helixultimate.ini'])
        .pipe(gulp.dest('build/plugins/system'))
});

// Copy system plugin Language
gulp.task('copy_system_plugin_lang', function(){
    return gulp.src(['./administrator/language/en-GB/en-GB.plg_system_helixultimate.ini'])
        .pipe(gulp.dest('build/plugins/system/language'))
});

// Copy ajax plugin
gulp.task('copy_ajax_plugin', function(){
    return gulp.src(['./plugins/ajax/helixultimate/**/*.*'])
        .pipe(gulp.dest('build/plugins/ajax'))
});

// Copy Installer
gulp.task('copy_installer', function(){
    return gulp.src(['./helix-installer/**/*.*'])
        .pipe(gulp.dest('build/'))
});

gulp.task('copy', ['copy_tmpl', 'copy_tmpl_lang', 'copy_system_plugin', 'copy_system_plugin_lang', 'copy_ajax_plugin', 'copy_installer']);

gulp.task('build', ['copy'], function(){
    return gulp.src('./build/**/*.*')
        .pipe(zip('helix-ultimate.zip'))
        .pipe(gulp.dest('./'))
});

gulp.task('zip_it', function(){
    return gulp.src('./build/**/*.*')
        .pipe(zip('helix-ultimate.zip'))
        .pipe(gulp.dest('./'))
});

gulp.task('clean_build', function () {
    return gulp.src('./build', {read: false})
        .pipe(clean());
});

gulp.task('clean_zip', function () {
    return gulp.src('./helix-ultimate.zip', {read: false})
        .pipe(clean());
});

gulp.task('default', [ 'build' ]);
gulp.task('clean', [ 'clean_build', 'clean_zip' ]);