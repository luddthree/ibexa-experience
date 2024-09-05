const path = require('path');

module.exports = (ibexaConfig, ibexaConfigManager) => {
    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-layout-css',
        newItems: [path.resolve(__dirname, '../public/scss/image.picker.scss')],
    });

    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-udw-js',
        newItems: [
            path.resolve(__dirname, '../../ui-dev/src/modules/image-picker/config.loader.js'),
            path.resolve(__dirname, '../../ui-dev/src/modules/image-picker/image.picker.tab.module.js'),
        ],
    });
};
