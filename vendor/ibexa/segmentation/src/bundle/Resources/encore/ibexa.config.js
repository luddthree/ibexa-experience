const path = require('path');

module.exports = (Encore) => {
    Encore.addEntry('ibexa-segmentation-common-js', [
        path.resolve(__dirname, '../public/js/add.segment.group.js'),
        path.resolve('./public/bundles/ibexaadminui/js/scripts/button.state.toggle.js'),
    ]).addEntry('ibexa-segmentation-common-css', [path.resolve(__dirname, '../public/scss/segmentation.scss')]);
};
