const path = require('path');

module.exports = (Encore) => {
    Encore.addEntry('ibexa-form-builder-common-js', [
        path.resolve(__dirname, '../public/js/submission.details.js'),
        path.resolve(__dirname, '../public/js/resize.form.preview.js'),
        path.resolve(__dirname, '../../ui-dev/src/modules/form.builder.module.js'),
        path.resolve(__dirname, '../public/js/fieldType/ezform.js'),
        path.resolve(__dirname, '../public/js/form.builder.create.js'),
    ])
        .addEntry('ibexa-form-builder-fields-config-js', [
            path.resolve(__dirname, '../public/js/config-form/field.config.content.height.js'),
            path.resolve(__dirname, '../public/js/config-form/fields/location.js'),
            path.resolve(__dirname, '../public/js/config-form/fields/options.js'),
            path.resolve(__dirname, '../public/js/config-form/fields/action.js'),
            path.resolve(__dirname, '../public/js/config-form/fields/action.redirect.content.js'),
            path.resolve(__dirname, '../public/js/config-form/fields/field.name.js'),
            path.resolve(__dirname, '../public/js/config-form/fields/regex.js'),
        ])
        .addEntry('ibexa-form-builder-submissions-tab-js', [
            path.resolve('./public/bundles/ibexaadminui/js/scripts/admin.location.change.language.js'),
        ])
        .addEntry('ibexa-form-builder-fields-config-css', [path.resolve(__dirname, '../public/scss/form-builder-config-form.scss')])
        .addEntry('ibexa-form-builder-submissions-tab-css', [path.resolve(__dirname, '../public/scss/form-builder-ui.scss')])
        .addEntry('ibexa-form-builder-form-preview-css', [path.resolve(__dirname, '../public/scss/form-preview.scss')])
        .addEntry('ibexa-form-builder-common-css', [path.resolve(__dirname, '../public/scss/form-builder-ui.scss')])
        .addEntry('ibexa-form-builder-ajax-captcha-js', [path.resolve(__dirname, '../public/js/fieldType/captcha.js')]);
};
