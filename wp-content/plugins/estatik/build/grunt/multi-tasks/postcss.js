'use strict';

module.exports = {
    options: {
        map: false, // turns off postcss sourcemap file
        processors: [
            require('postcss-flexbox')(), // add flexbox shortcuts
            require('autoprefixer')({browsers: 'last 4 versions'}) // add vendor prefixes
        ]
    },
    plugin: {
        files: [
            '<%= adminPath %>/css/*.css',
            '<%= publicPath %>/css/*.css',
            '<%= commonPath %>/icons/*.css',
        ]
    }
};
