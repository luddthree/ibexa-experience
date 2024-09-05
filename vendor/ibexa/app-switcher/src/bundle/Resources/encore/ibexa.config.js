const path = require('path');

module.exports = (Encore) => {
    Encore.addEntry('ibexa-app-switcher-js', [path.resolve(__dirname, '../public/js/app.switcher.js')]).addEntry('ibexa-app-switcher-css', [
        path.resolve(__dirname, '../public/scss/app-switcher.scss'),
    ]);
};
