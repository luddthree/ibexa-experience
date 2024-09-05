const path = require('path');

module.exports = (Encore) => {
    Encore.addEntry('ibexa-version-comparison-js', [
        path.resolve(__dirname, '../public/js/scripts/admin.version.comparison.js'),
        path.resolve('./vendor/ibexa/admin-ui-assets/src/bundle/Resources/public/vendors/leaflet/dist/leaflet.js'),
        path.resolve('./public/bundles/ibexaadminui/js/scripts/admin.location.load.map.js'),
    ]).addEntry('ibexa-version-comparison-css', [
        path.resolve(__dirname, '../public/scss/version_comparison.scss'),
        path.resolve('./vendor/ibexa/admin-ui-assets/src/bundle/Resources/public/vendors/leaflet/dist/leaflet.css'),
    ]);
};
