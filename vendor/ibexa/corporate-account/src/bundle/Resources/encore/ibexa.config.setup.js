const path = require('path');

module.exports = (Encore) => {
    Encore.addAliases({
        '@ibexa-corporate-account': path.resolve('./vendor/ibexa/corporate-account'),
    });
};
