const path = require('path');

module.exports = (Encore) => {
    Encore.addAliases({
        '@ibexa-taxonomy': path.resolve('./vendor/ibexa/taxonomy'),
    });
};
