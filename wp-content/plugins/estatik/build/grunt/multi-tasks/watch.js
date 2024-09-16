'use strict';

module.exports = {
    options: {
        spawn: false // add spawn option in watch task
    },
    grunt: {
        files: ['Gruntfile.js'],
        tasks: ['jshint:grunt']
    },
    plugin: {
        files: ['admin/scss/**/*', 'public/scss/**/*'],
        tasks: ['sass:plugin','cssmin:plugin']
    },
    scripts: {
        files: [
            'admin/js/**/*',
            'public/js/**/*'
        ],
        tasks: ['jshint:src','uglify:scripts']
    }
};
