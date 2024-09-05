const path = require('path');

module.exports = (ibexaConfig, ibexaConfigManager) => {
    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-layout-js',
        newItems: [path.resolve(__dirname, '../../ui-dev/src/modules/tree-builder/config.loader.js')],
    });
    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-layout-css',
        newItems: [path.resolve(__dirname, '../public/scss/tree.builder.scss')],
    });
};
