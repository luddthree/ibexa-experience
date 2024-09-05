const path = require('path');

module.exports = (Encore) => {
    Encore.addAliases({
        '@ibexa-dashboard': path.resolve('./vendor/ibexa/dashboard'),
    });
};
