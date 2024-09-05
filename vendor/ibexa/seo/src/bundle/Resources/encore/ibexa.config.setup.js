const path = require('path');

module.exports = (Encore) => {
    Encore.addAliases({
        '@ibexa-seo': path.resolve('./vendor/ibexa/seo'),
    });
};
