const path = require('path');

module.exports = (Encore) => {
    Encore.addAliases({
        '@ibexa-content-tree': path.resolve('./vendor/ibexa/content-tree'),
    });
};
