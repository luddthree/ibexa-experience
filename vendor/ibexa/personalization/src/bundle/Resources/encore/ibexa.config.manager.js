const path = require('path');

module.exports = (ibexaConfig, ibexaConfigManager) => {
    const blockConfigJsEntryName = 'ibexa-page-builder-block-config-js';
    const blockConfigCssEntryName = 'ibexa-page-builder-config-css';
    const blockConfigEditCssEntryName = 'ibexa-page-builder-edit-css';
    const blockConfigCreateCssEntryName = 'ibexa-page-builder-create-css';
    const dashboardBlockJsEntryName = 'ibexa-dashboard-blocks-js';
    const dashboardBlockCssEntryName = 'ibexa-dashboard-blocks-css';

    const isBlockConfigJsEntryPresent = ibexaConfig.entry[blockConfigJsEntryName];
    const isBlockConfigCssEntryPresent = ibexaConfig.entry[blockConfigCssEntryName];
    const isBlockConfigEditCssEntryPresent = ibexaConfig.entry[blockConfigEditCssEntryName];
    const isBlockConfigCreateCssEntryPresent = ibexaConfig.entry[blockConfigCreateCssEntryName];
    const isDashboardBlockJsEntryPresent = ibexaConfig.entry[dashboardBlockJsEntryName];
    const isDashboardBlockCssEntryPresent = ibexaConfig.entry[dashboardBlockCssEntryName];

    if (isBlockConfigJsEntryPresent) {
        ibexaConfigManager.add({
            ibexaConfig,
            entryName: blockConfigJsEntryName,
            newItems: [
                path.resolve(__dirname, '../public/js/widgets/targeted.scenario.map.js'),
                path.resolve(__dirname, '../public/js/widgets/personalized.js'),
            ],
        });
    }

    if (isBlockConfigCssEntryPresent) {
        ibexaConfigManager.add({
            ibexaConfig,
            entryName: blockConfigCssEntryName,
            newItems: [path.resolve(__dirname, '../public/scss/ui/config-form/widgets/dynamic_targeting.scss')],
        });
    }

    if (isBlockConfigEditCssEntryPresent) {
        ibexaConfigManager.add({
            ibexaConfig,
            entryName: blockConfigEditCssEntryName,
            newItems: [path.resolve(__dirname, '../public/scss/ui/page-builder.scss')],
        });
    }

    if (isBlockConfigCreateCssEntryPresent) {
        ibexaConfigManager.add({
            ibexaConfig,
            entryName: blockConfigCreateCssEntryName,
            newItems: [path.resolve(__dirname, '../public/scss/ui/page-builder.scss')],
        });
    }

    if (isDashboardBlockJsEntryPresent) {
        ibexaConfigManager.add({
            ibexaConfig,
            entryName: dashboardBlockJsEntryName,
            newItems: [path.resolve(__dirname, '../public/js/dashboard/blocks/top_clicks.js')],
        });
    }

    if (isDashboardBlockCssEntryPresent) {
        ibexaConfigManager.add({
            ibexaConfig,
            entryName: 'ibexa-dashboard-blocks-css',
            newItems: [path.resolve(__dirname, '../public/scss/blocks/top_clicks.scss')],
        });
    }
};
