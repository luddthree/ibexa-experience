const path = require('path');

module.exports = (ibexaConfig, ibexaConfigManager) => {
    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-udw-extras-js',
        newItems: [path.resolve(__dirname, '../public/js/udw/config.loader.js')],
    });

    if (ibexaConfig.entry['ibexa-dashboard-blocks-css']) {
        ibexaConfigManager.add({
            ibexaConfig,
            entryName: 'ibexa-dashboard-blocks-css',
            newItems: [
                path.resolve(__dirname, '../public/scss/blocks/product-category.scss'),
                path.resolve(__dirname, '../public/scss/blocks/lowest-stock.scss'),
            ],
        });
    }

    if (ibexaConfig.entry['ibexa-dashboard-blocks-js']) {
        ibexaConfigManager.add({
            ibexaConfig,
            entryName: 'ibexa-dashboard-blocks-js',
            newItems: [path.resolve(__dirname, '../public/js/product.category.block.js')],
        });
    }
};
