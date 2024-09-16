module.exports = function(grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        publicPath: '../public',
        commonPath: '../common',
        adminPath: '../admin',

        // compile sass files into css
        sass: require('./grunt/multi-tasks/sass'),

        // postcss
        postcss: require('./grunt/multi-tasks/postcss'),

        // minify css
        cssmin: require('./grunt/multi-tasks/cssmin'),

        // test javascript and notify of errors
        jshint: require('./grunt/multi-tasks/jshint'),

        // minimise javascript
        uglify: require('./grunt/multi-tasks/uglify'),

        // watch files and make changes
        watch: require('./grunt/multi-tasks/watch'),

        webfont: {
            icons: {
                src: 'common/icons/*.svg',
                dest: '../common/fonts',
                destScss: 'common/scss/',
                options: {
                    templateOptions: {
                        baseClass: 'es-icon',
                        classPrefix: 'es-icon_'
                    },
                    font: 'es-icon',
                    stylesheet: 'scss',
                    relativeFontPath: '../../common/fonts'
                }
            }
        }
    });

    grunt.loadNpmTasks( 'grunt-webfont' );

    // load externally defined tasks
    require('load-grunt-tasks')(grunt);

    // register tasks for command line
    grunt.registerTask('default',['sass:plugin','cssmin:plugin','jshint:src','jshint:modules','uglify:scripts']); // default
    grunt.registerTask('plugin',['sass:plugin','cssmin:plugin']); // plugin styles
    grunt.registerTask('scripts',['jshint:src','uglify:scripts']); // scripts
    grunt.registerTask('lint',['jshint:dist']); // lint compiled scripts
};
