'use strict';

module.exports = {
    plugin: {
        files: {
            '<%= adminPath %>/css/admin.css': 'admin/scss/index.scss',
            '<%= adminPath %>/css/locations.css': 'admin/scss/locations.scss',
            '<%= adminPath %>/css/settings.css': 'admin/scss/settings.scss',
            '<%= adminPath %>/css/data-manager.css': 'admin/scss/data-manager.scss',
            '<%= adminPath %>/css/fields-builder.css': 'admin/scss/fields-builder.scss',
            '<%= adminPath %>/css/archive-entities.css': 'admin/scss/archive-entities.scss',
            '<%= adminPath %>/css/metabox.css': 'admin/scss/metabox.scss',
            '<%= adminPath %>/css/dashboard.css': 'admin/scss/dashboard.scss',
            '<%= adminPath %>/css/demo.css': 'admin/scss/demo.scss',
            '<%= adminPath %>/css/migration.css': 'admin/scss/migration.scss',
            '<%= commonPath %>/icons/icons.css': 'public/scss/icons.scss',
            '<%= publicPath %>/css/public.css': 'public/scss/index.scss',
        }
    }
};
