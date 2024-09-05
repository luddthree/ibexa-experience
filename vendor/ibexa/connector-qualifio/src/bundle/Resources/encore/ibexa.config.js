const path = require('path');

module.exports = (Encore) => {
    Encore.addEntry('ibexa-qualifio-css', [path.resolve(__dirname, '../public/scss/qualifio.scss')]).addEntry('ibexa-qualifio-block-css', [
        path.resolve(__dirname, '../public/scss/qualifio-block.scss'),
    ]);
};
