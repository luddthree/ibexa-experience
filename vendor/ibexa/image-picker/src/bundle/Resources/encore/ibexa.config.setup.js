const path = require('path');

module.exports = (Encore) => {
    Encore.addAliases({
        '@ibexa-image-picker': path.resolve('./vendor/ibexa/image-picker'),
    });
};
