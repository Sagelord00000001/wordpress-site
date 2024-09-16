'use strict';

module.exports = {
    options: {
        keepSpecialComments: 0
    },
    plugin: {
        files: [{
            expand: true,
            cwd: '<%= publicPath %>/css',
            src: ['*.css', '!*.min.css'],
            dest: '<%= publicPath %>/css',
            ext: '.min.css'
        }, {
            expand: true,
            cwd: '<%= adminPath %>/css',
            src: ['*.css', '!*.min.css'],
            dest: '<%= adminPath %>/css',
            ext: '.min.css'
        }, {
            expand: true,
            cwd: '<%= commonPath %>/icons',
            src: ['*.css', '!*.min.css'],
            dest: '<%= commonPath %>/icons',
            ext: '.min.css'
        }]
    }
};
