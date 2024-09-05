const path = require('path');

module.exports = (ibexaConfig, ibexaConfigManager) => {
    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-calendar-common-js',
        newItems: [
            path.resolve(__dirname, '../public/js/calendar.discard.publish.later.js'),
            path.resolve(__dirname, '../public/js/calendar.discard.hide.later.js'),
            path.resolve(__dirname, '../public/js/calendar.reschedule.publish.later.js'),
            path.resolve(__dirname, '../public/js/calendar.reschedule.hide.later.js'),
        ],
    });

    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-location-view-js',
        newItems: [
            path.resolve(__dirname, '../public/js/content.schedule.hide.js'),
            path.resolve(__dirname, '../public/js/content.schedule.hide.cancel.js'),
        ],
    });
};
