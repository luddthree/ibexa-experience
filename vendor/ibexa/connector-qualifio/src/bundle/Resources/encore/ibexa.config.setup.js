const path = require('path');

module.exports = (Encore) => {
    Encore.addAliases({
        '@ibexa-connector-qualifio': path.resolve('./vendor/ibexa/connector-qualifio'),
    });
};
