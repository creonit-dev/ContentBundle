var gulp = require('gulp'),
    gulpif = require('gulp-if'),
    stylus = require('gulp-stylus'),
    uglify = require('gulp-uglify'),
    concat = require('gulp-concat'),
    runSequence = require('run-sequence').use(gulp),
    streamqueue = require('streamqueue'),
    browserSync,
    nib = require('nib'),
    merge = require('merge-stream'),
    path = require('path'),
    ts = require('gulp-typescript'),
    fs = require('fs'),
    sort = require('gulp-sort'),
    replace = require('gulp-replace'),
    through = require('through');

var outputDir = './src/Resources/public',
    optimize;

/**
 * @task css:main
 * Generate /web/css/*.css
 */
gulp.task('css', function(callback){
    var stream = gulp.src('./assets/css/*.styl')
        .pipe(stylus({use: [nib()]}))
        .on('error', function(error){
            console.log(error.message);
            callback();
        })
        .pipe(gulp.dest(outputDir + '/css'));

    if(browserSync){
        stream.pipe(browserSync.reload({stream: true}));
    }

    return stream;
});



/**
 * @task js
 * Generate /web/js/*.js
 */
gulp.task('js', function(callback){
    var dir = './assets/js',
        dest = outputDir + '/js',
        stream;

    if(!fs.existsSync(dir)){
        return callback();
    }

    stream = fs
        .readdirSync(dir)
        .filter(function(file){
            return fs.statSync(path.join(dir, file)).isDirectory() && file != 'vendor';
        })
        .map(function(folder){
            return merge(gulp.src(path.join(dir, folder, '/**/*.js')), gulp.src(path.join(dir, folder, '/**/*.ts')).pipe(ts({noImplicitAny: false, out: 'ts.js', target: 'es5'})).js)
                .pipe(sort())
                .pipe(concat(folder + '.js'))
                .pipe(gulpif(optimize, uglify()))
                .pipe(gulp.dest(dest));
        });

    stream = merge(
        stream,
        merge(gulp.src(dir + '/*.js'), gulp.src(dir + '/*.ts').pipe(ts({noImplicitAny: false})).js)
            .pipe(sort())
            .pipe(concat('common.js'))
            .pipe(gulpif(optimize, uglify()))
            .pipe(gulp.dest(dest))
    );

    if(browserSync){
        stream.pipe(browserSync.reload({stream: true}));
    }

    return stream;
});

/**
 * @task watch
 */
gulp.task('watch', function(){
    gulp.watch('./assets/css/*.styl', ['css']);
    gulp.watch(['./assets/js/**/*.js', './assets/js/**/*.ts', '!./assets/js/vendor/**/*.js', '!./assets/js/vendor/**/*.ts'], ['js']);

});


gulp.task('default', function(){
    runSequence(
        ['css', 'js'],
        'watch'
    );
});

gulp.task('build', function(){
    optimize = true;
    runSequence(
        ['css', 'js']
    );
});

