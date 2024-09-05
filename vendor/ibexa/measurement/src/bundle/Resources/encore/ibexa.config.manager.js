const path = require('path');

module.exports = (ibexaConfig, ibexaConfigManager) => {
    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-content-type-edit-js',
        newItems: [path.resolve(__dirname, '../public/js/admin.settings.measurement.js')],
    });

    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-product-catalog-attribute-definition-edit-js',
        newItems: [path.resolve(__dirname, '../public/js/admin.settings.measurement.js')],
    });

    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-product-catalog-product-edit-js',
        newItems: [path.resolve(__dirname, '../public/js/attribute.measurement.validator.js')],
    });

    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-content-edit-parts-js',
        newItems: [path.resolve(__dirname, '../public/js/field.measurement.validator.js')],
    });

    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-layout-css',
        newItems: [path.resolve(__dirname, '../public/scss/measurement-content-type.scss')],
    });

    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-content-edit-parts-css',
        newItems: [path.resolve(__dirname, '../public/scss/measurement-content.scss')],
    });
};
