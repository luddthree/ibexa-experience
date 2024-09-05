const path = require('path');

module.exports = (ibexaConfig, ibexaConfigManager) => {
    if (ibexaConfig.entry['ibexa-dashboard-blocks-css']) {
        ibexaConfigManager.add({
            ibexaConfig,
            entryName: 'ibexa-dashboard-blocks-css',
            newItems: [path.resolve(__dirname, '../public/scss/blocks/ibexa-recent-activity.scss')],
        });
    }

    if (ibexaConfig.entry['ibexa-dashboard-blocks-js']) {
        ibexaConfigManager.add({
            ibexaConfig,
            entryName: 'ibexa-dashboard-blocks-js',
            newItems: [path.resolve(__dirname, '../public/js/recent.activity.block.js')],
        });
    }
};
