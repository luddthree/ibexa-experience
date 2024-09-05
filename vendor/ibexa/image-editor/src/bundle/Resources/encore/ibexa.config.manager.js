const path = require('path');

module.exports = (ibexaConfig, ibexaConfigManager) => {
    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-layout-js',
        newItems: [
            path.resolve(__dirname, '../../ui-dev/src/modules/image-editor/components/actions/flip/flip.js'),
            path.resolve(__dirname, '../../ui-dev/src/modules/image-editor/components/actions/focal-point/focal.point.js'),
            path.resolve(__dirname, '../../ui-dev/src/modules/image-editor/components/actions/crop/crop.js'),
            path.resolve(__dirname, '../../ui-dev/src/modules/image-editor/image.editor.modules.js'),
        ],
    });

    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-content-edit-parts-js',
        newItems: [path.resolve(__dirname, '../public/js/ezimage.edit.js'), path.resolve(__dirname, '../public/js/ezimageasset.edit.js')],
    });

    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-layout-css',
        newItems: [path.resolve(__dirname, '../public/scss/ui/image-editor.scss')],
    });
};
