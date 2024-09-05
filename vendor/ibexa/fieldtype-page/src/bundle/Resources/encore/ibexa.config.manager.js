const path = require('path');

module.exports = (ibexaConfig, ibexaConfigManager) => {
    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-content-type-edit-js',
        newItems: [path.resolve(__dirname, '../public/js/page-select-items.js')],
    });
};
