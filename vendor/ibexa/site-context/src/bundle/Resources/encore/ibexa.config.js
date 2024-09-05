const path = require('path');

module.exports = (Encore) => {
    Encore.addEntry('ibexa-site-context-preview-not-available-css', [path.resolve(__dirname, '../public/scss/preview-not-available.scss')]);

    Encore.addEntry('ibexa-site-context-location-preview-js', [
        path.resolve(__dirname, '../public/js/admin.location.preview.tree.js'),
    ]).addEntry('ibexa-site-context-location-view-js', [
        path.resolve(__dirname, '../public/js/admin.location.preview.view.js'),
        path.resolve('./vendor/ibexa/admin-ui/src/bundle/Resources/public/js/scripts/sidebar/extra.actions.js'),
        path.resolve('./vendor/ibexa/admin-ui/src/bundle/Resources/public/js/scripts/sidebar/btn/location.edit.js'),
        path.resolve('./vendor/ibexa/admin-ui/src/bundle/Resources/public/js/scripts/sidebar/btn/user.edit.js'),
        path.resolve('./vendor/ibexa/admin-ui/src/bundle/Resources/public/js/scripts/sidebar/btn/location.create.js'),
        path.resolve('./vendor/ibexa/admin-ui/src/bundle/Resources/public/js/scripts/sidebar/instant.filter.js'),
        path.resolve('./vendor/ibexa/admin-ui/src/bundle/Resources/public/js/scripts/sidebar/btn/content.edit.js'),
        path.resolve('./vendor/ibexa/admin-ui/src/bundle/Resources/public/js/scripts/admin.content.tree.js'),
    ]);
};
