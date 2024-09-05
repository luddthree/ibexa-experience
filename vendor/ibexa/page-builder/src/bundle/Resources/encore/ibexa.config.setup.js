const path = require('path');

module.exports = (Encore) => {
    Encore.addAliases({
        '@ibexa-page-builder': path.resolve('./vendor/ibexa/page-builder'),
    });
};
