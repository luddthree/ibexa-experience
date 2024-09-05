const path = require('path');

module.exports = (Encore) => {
    Encore.addEntry('ibexa-activity-log-list-css', [path.resolve(__dirname, '../public/scss/ibexa-activity-log.scss')]).addEntry(
        'ibexa-activity-log-widget-css',
        [path.resolve(__dirname, '../public/scss/blocks/ibexa-recent-activity.scss')],
    );
};
