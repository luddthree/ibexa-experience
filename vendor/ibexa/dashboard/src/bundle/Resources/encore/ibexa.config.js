const path = require('path');

module.exports = (Encore) => {
    Encore.addEntry('ibexa-dashboard-css', [path.resolve(__dirname, '../public/scss/dashboard.scss')])
        .addEntry('ibexa-dashboard-blocks-css', [
            path.resolve(__dirname, '../public/scss/block/news.scss'),
            path.resolve(__dirname, '../public/scss/block/quick-actions.scss'),
        ])
        .addEntry('ibexa-dashboard-blocks-js', [
            path.resolve(__dirname, '../public/js/dashboard.blocks.js'),
            path.resolve(__dirname, '../public/js/blocks/quick.actions.js'),
        ])
        .addEntry('ibexa-dashboard-view-js', [path.resolve(__dirname, '../public/js/dashboard.view.js')]);
};
