const path = require('path');

module.exports = (Encore) => {
    Encore.addEntry('ibexa-calendar-common-js', [path.resolve(__dirname, '../public/js/calendar.js')])
        .addEntry('ibexa-calendar-module-js', [path.resolve(__dirname, '../../ui-dev/src/modules/calendar.module.js')])
        .addEntry('ibexa-calendar-module-css', [path.resolve(__dirname, '../public/scss/calendar.scss')]);
};
