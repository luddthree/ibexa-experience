const path = require('path');

module.exports = (Encore) => {
    Encore.addEntry('ibexa-page-fieldtype-css', [path.resolve(__dirname, '../public/scss/ibexa-page-fieldtype.scss')]).addEntry(
        'ibexa-page-fieldtype-common-css',
        [path.resolve(__dirname, '../public/scss/page-fieldtype-editorial-mode.scss')],
    );
};
