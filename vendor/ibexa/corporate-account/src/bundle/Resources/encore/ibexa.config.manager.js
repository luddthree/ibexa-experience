const path = require('path');

module.exports = (ibexaConfig, ibexaConfigManager) => {
    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-udw-extras-js',
        newItems: [path.resolve(__dirname, '../public/js/udw/config.loader.js')],
    });
};
