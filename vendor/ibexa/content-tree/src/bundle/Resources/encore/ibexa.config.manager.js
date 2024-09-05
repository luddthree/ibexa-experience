const path = require('path');

module.exports = (ibexaConfig, ibexaConfigManager) => {
    const contentTreeActionItems = [
        path.resolve(__dirname, '../public/js/add.translation.js'),
        path.resolve(__dirname, '../public/js/create.content.js'),
        path.resolve(__dirname, '../public/js/edit.content.js'),
        path.resolve(__dirname, '../public/js/hide.reveal.content.js'),
    ];

    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-layout-css',
        newItems: [path.resolve(__dirname, '../public/scss/content.tree.scss')],
    });
    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-location-view-js',
        newItems: contentTreeActionItems,
    });
    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-site-context-location-view-js',
        newItems: contentTreeActionItems,
    });
    ibexaConfigManager.replace({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-content-tree-js',
        itemToReplace: path.resolve('./vendor/ibexa/admin-ui/src/bundle/ui-dev/src/modules/content-tree/config.loader.js'),
        newItem: path.resolve(__dirname, '../../ui-dev/src/modules/content-tree/config.loader.js'),
    });
};
