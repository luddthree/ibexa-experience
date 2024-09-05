const path = require('path');

module.exports = (Encore) => {
    Encore.addEntry('ibexa-page-builder-block-config-js', [
        path.resolve('./public/bundles/ibexaadminui/js/scripts/fieldType/base/base-field.js'),
        path.resolve(__dirname, '../public/js/config-form/block.config.attributes.group.js'),
        path.resolve(__dirname, '../public/js/config-form/base/block.config.embed.field.base.js'),
        path.resolve(__dirname, '../public/js/config-form/base/block.config.schedule.field.base.js'),
        path.resolve(__dirname, '../public/js/config-form/base/block.config.scheduler.js'),
        path.resolve(__dirname, '../public/js/config-form/base/block.config.fields.errors.js'),
        path.resolve(__dirname, '../public/js/config-form/base/block.config.navigation.js'),
        path.resolve(__dirname, '../public/js/config-form/widgets/embed.js'),
        path.resolve(__dirname, '../public/js/config-form/widgets/video.js'),
        path.resolve(__dirname, '../public/js/config-form/widgets/checkbox.js'),
        path.resolve(__dirname, '../public/js/config-form/widgets/collection.js'),
        path.resolve(__dirname, '../public/js/config-form/widgets/schedule.js'),
        path.resolve(__dirname, '../public/js/config-form/widgets/richtext.js'),
        path.resolve(__dirname, '../public/js/config-form/widgets/dropdown.js'),
    ])
        .addEntry('ibexa-page-builder-create-js', [path.resolve(__dirname, '../public/js/page.builder.create.js')])
        .addEntry('ibexa-page-builder-edit-js', [path.resolve(__dirname, '../public/js/page.builder.edit.js')])
        .addEntry('ibexa-page-builder-parts-javascript-js', [
            path.resolve(__dirname, '../../ui-dev/src/modules/page.builder.module.js'),
            path.resolve(__dirname, '../public/js/infobar.js'),
            path.resolve(__dirname, '../public/js/page.builder.config.panel.opening.js'),
            path.resolve(__dirname, '../public/js/page.builder.fields.config.panel.js'),
            path.resolve(__dirname, '../public/js/fieldType/ezlandingpage.js'),
            path.resolve(__dirname, '../public/js/page.builder.publish.js'),
            path.resolve(__dirname, '../public/js/page.builder.react.blocks.js'),
        ])
        .addEntry('ibexa-page-builder-preview-js', [
            path.resolve(__dirname, '../../ui-dev/src/modules/timeline.module.js'),
            path.resolve(__dirname, '../public/js/infobar.js'),
            path.resolve(__dirname, '../public/js/page.builder.config.panel.opening.js'),
            path.resolve(__dirname, '../public/js/page.builder.fields.config.panel.js'),
            path.resolve(__dirname, '../public/js/versions.js'),
            path.resolve(__dirname, '../public/js/page.builder.view.js'),
            path.resolve(__dirname, '../public/js/page.builder.preview.info.js'),
            path.resolve(__dirname, '../public/js/timeline.view.js'),
            path.resolve('./public/bundles/ibexaadminui/js/scripts/sidebar/instant.filter.js'),
            path.resolve('./public/bundles/ibexaadminui/js/scripts/admin.version.edit.conflict.js'),
        ])
        .addEntry('ibexa-page-builder-preview-in-siteaccess-js', [
            path.resolve('./public/bundles/ibexaadminui/js/scripts/sidebar/btn/location.edit.js'),
        ])
        .addEntry('ibexa-page-builder-preview-non-location-based-js', [
            path.resolve(__dirname, '../public/js/infobar.js'),
            path.resolve(__dirname, '../public/js/page.builder.config.panel.opening.js'),
            path.resolve(__dirname, '../public/js/page.builder.fields.config.panel.js'),
            path.resolve(__dirname, '../public/js/page.builder.view.js'),
            path.resolve(__dirname, '../public/js/page.builder.preview.info.js'),
        ])
        .addEntry('ibexa-page-builder-translate-js', [path.resolve(__dirname, '../public/js/page.builder.edit.js')])
        .addEntry('ibexa-page-builder-config-css', [
            path.resolve(__dirname, '../public/scss/ui/config-form/base.scss'),
            path.resolve(__dirname, '../public/scss/ui/config-form/scheduler-tab.scss'),
            path.resolve(__dirname, '../public/scss/ui/config-form/attributes-group-wrapper.scss'),
            path.resolve(__dirname, '../public/scss/ui/config-form/widgets/embed.scss'),
            path.resolve(__dirname, '../public/scss/ui/config-form/widgets/radio-field.scss'),
            path.resolve(__dirname, '../public/scss/ui/config-form/widgets/reveal-hide-date-time.scss'),
            path.resolve(__dirname, '../public/scss/ui/config-form/widgets/checkbox-field.scss'),
            path.resolve(__dirname, '../public/scss/ui/config-form/widgets/collection.scss'),
            path.resolve(__dirname, '../public/scss/ui/config-form/widgets/schedule.scss'),
            path.resolve(__dirname, '../public/scss/ui/config-form/widgets/schedule-active-item.scss'),
            path.resolve(__dirname, '../public/scss/ui/config-form/widgets/schedule-queue-item.scss'),
        ])
        .addEntry('ibexa-page-builder-config-richtext-css', [path.resolve(__dirname, '../public/scss/ui/config-form/richtext.scss')])
        .addEntry('ibexa-page-builder-translate-css', [
            path.resolve(__dirname, '../public/scss/page-base-ui.scss'),
            path.resolve(__dirname, '../public/scss/page-builder-module-ui.scss'),
            path.resolve(__dirname, '../public/scss/timeline-module-ui.scss'),
        ])
        .addEntry('ibexa-page-builder-iframe-editor-ui-css', [path.resolve(__dirname, '../public/scss/iframe-editor-ui.scss')])
        .addEntry('ibexa-page-builder-create-css', [
            path.resolve(__dirname, '../public/scss/page-base-ui.scss'),
            path.resolve(__dirname, '../public/scss/page-builder-module-ui.scss'),
            path.resolve(__dirname, '../public/scss/timeline-module-ui.scss'),
        ])
        .addEntry('ibexa-page-builder-edit-css', [
            path.resolve(__dirname, '../public/scss/page-base-ui.scss'),
            path.resolve(__dirname, '../public/scss/page-builder-module-ui.scss'),
            path.resolve(__dirname, '../public/scss/timeline-module-ui.scss'),
        ])
        .addEntry('ibexa-page-builder-preview-css', [
            path.resolve(__dirname, '../public/scss/page-base-ui.scss'),
            path.resolve(__dirname, '../public/scss/timeline-module-ui.scss'),
        ])
        .addEntry('ibexa-page-builder-preview-non-location-based-css', [
            path.resolve(__dirname, '../public/scss/page-base-ui.scss'),
            path.resolve(__dirname, '../public/scss/timeline-module-ui.scss'),
        ])
        .addEntry('ibexa-page-builder-layout-css', [
            path.resolve('./public/bundles/ibexafieldtypepage/scss/page-fieldtype-editorial-mode.scss'),
        ]);
};
