const path = require('path');

module.exports = (ibexaConfig, ibexaConfigManager) => {
    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-layout-css',
        newItems: [path.resolve(__dirname, '../public/scss/taxonomy.scss')],
    });
    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-layout-js',
        newItems: [
            path.resolve(__dirname, '../public/js/select.parent.js'),
            path.resolve(__dirname, '../public/js/move.entry.js'),
            path.resolve(__dirname, '../public/js/tag.content.js'),
        ],
    });
    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-content-edit-parts-js',
        newItems: [path.resolve(__dirname, '../public/js/fieldType/ibexa_taxonomy_entry_assignment.js')],
    });
};
