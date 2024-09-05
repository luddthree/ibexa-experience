const path = require('path');

module.exports = (Encore) => {
    Encore.addEntry('ibexa-activity-log-list-js', [path.resolve(__dirname, '../public/js/filters.js')]).addEntry(
        'ibexa-activity-log-widget-js',
        [path.resolve(__dirname, '../public/js/recent.activity.block.js')],
    );
};
