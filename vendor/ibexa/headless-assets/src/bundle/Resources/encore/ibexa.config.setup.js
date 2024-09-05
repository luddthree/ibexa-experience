const path = require('path');

module.exports = (Encore) => {
    Encore.addAliases({
        '@ibexa-headless-assets': path.resolve('./vendor/ibexa/headless-assets'),
    });
};
