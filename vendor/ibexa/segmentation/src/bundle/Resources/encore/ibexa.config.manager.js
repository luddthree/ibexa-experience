const path = require('path');

module.exports = (ibexaConfig, ibexaConfigManager) => {
    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-page-builder-block-config-js',
        newItems: [path.resolve(__dirname, '../public/js/widgets/targeted.content.map.js')],
    });

    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-page-builder-config-css',
        newItems: [path.resolve(__dirname, '../public/scss/ui/config-form/widgets/segmentation.scss')],
    });

    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-page-builder-parts-javascript-js',
        newItems: [path.resolve(__dirname, '../../ui-dev/src/modules/page.builder.update.preview.request.params.js')],
    });

    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-page-builder-edit-css',
        newItems: [path.resolve(__dirname, '../public/scss/ui/page-builder.scss')],
    });

    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-page-builder-create-css',
        newItems: [path.resolve(__dirname, '../public/scss/ui/page-builder.scss')],
    });
};
