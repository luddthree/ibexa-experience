const path = require('path');

module.exports = (Encore) => {
    Encore.addAliases({
        '@ibexa-product-catalog': path.resolve('./vendor/ibexa/product-catalog'),
    });
};
