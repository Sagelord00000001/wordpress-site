'use strict';

module.exports = {
    scripts: {
        options: {
            sourceMap: false
        },
        files: {
            '<%= publicPath %>/js/elementor.min.js': ['public/js/elementor.js'],
            '<%= publicPath %>/js/public.min.js': ['public/js/public.js'],
            '<%= publicPath %>/js/ajax-entities.min.js': ['public/js/ajax-entities.js'],
            '<%= publicPath %>/js/gm-popup.min.js': ['public/js/gm-popup.js'],
            '<%= adminPath %>/js/admin.min.js': ['admin/js/admin.js'],
            '<%= adminPath %>/js/demo.min.js': ['admin/js/demo.js'],
            '<%= adminPath %>/js/fields-builder.min.js': ['admin/js/fields-builder.js'],
            '<%= adminPath %>/js/data-manager.min.js': ['admin/js/data-manager.js'],
            '<%= adminPath %>/js/entities-list.min.js': ['admin/js/entities-list.js'],
            '<%= adminPath %>/js/settings.min.js': ['admin/js/settings.js'],
            '<%= adminPath %>/js/property-metabox.min.js': ['admin/js/property-metabox.js'],
            '<%= adminPath %>/js/migration.min.js': ['admin/js/migration.js']
        }
    }
};
