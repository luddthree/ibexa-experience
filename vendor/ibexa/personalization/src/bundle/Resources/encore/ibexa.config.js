const path = require('path');

module.exports = (Encore) => {
    Encore.addEntry('ibexa-personalization-dashboard-js', [
        path.resolve(__dirname, '../public/js/dashboard.js'),
        path.resolve(__dirname, '../public/js/page.title.js'),
        path.resolve(__dirname, '../public/js/time.range.js'),
    ])
        .addEntry('ibexa-personalization-scenarios-list-js', [
            path.resolve(__dirname, '../public/js/scenarios.list.js'),
            path.resolve(__dirname, '../public/js/page.title.js'),
        ])
        .addEntry('ibexa-personalization-models-list-js', [path.resolve(__dirname, '../public/js/page.title.js')])
        .addEntry('ibexa-personalization-import-js', [path.resolve(__dirname, '../public/js/page.title.js')])
        .addEntry('ibexa-personalization-scenarios-preview-js', [path.resolve(__dirname, '../public/js/scenarios.preview.js')])
        .addEntry('ibexa-personalization-welcome-js', [path.resolve(__dirname, '../public/js/welcome.js')])
        .addEntry('ibexa-personalization-account-js', [path.resolve(__dirname, '../public/js/account.js')])
        .addEntry('ibexa-personalization-model-details-js', [path.resolve(__dirname, '../public/js/model.details.js')])
        .addEntry('ibexa-personalization-model-edit-js', [
            path.resolve(__dirname, '../public/js/model.edit.js'),
            path.resolve(__dirname, '../public/js/model.edit.editorial.js'),
            path.resolve(__dirname, '../public/js/model.edit.segments.js'),
        ])
        .addEntry('ibexa-personalization-scenarios-edit-js', [path.resolve(__dirname, '../public/js/scenarios.edit.js')])
        .addEntry('ibexa-personalization-client-js', [path.resolve(__dirname, '../public/js/PersonalizationClient.js')]);
    Encore.addEntry('ibexa-personalization-css', [path.resolve(__dirname, '../public/scss/ibexa-personalization.scss')]).addEntry(
        'ibexa-recommendation-client-css',
        [path.resolve(__dirname, '../public/css/recommendations.css')],
    );
};
