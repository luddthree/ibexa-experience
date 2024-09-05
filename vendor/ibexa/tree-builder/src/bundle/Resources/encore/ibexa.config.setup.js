const path = require('path');

module.exports = (Encore) => {
    Encore.addAliases({
        '@ibexa-tree-builder': path.resolve('./vendor/ibexa/tree-builder'),
    });
};
