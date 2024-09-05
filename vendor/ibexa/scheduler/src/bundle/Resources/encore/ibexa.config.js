const path = require('path');

module.exports = (Encore) => {
    Encore.addEntry('date-based-publisher-common-js', [
        path.resolve(__dirname, '../public/js/future.scheduling.js'),
        path.resolve(__dirname, '../public/js/timeline.event.listener.js'),
        path.resolve(__dirname, '../public/js/scheduling.modal.js'),
        path.resolve(__dirname, '../public/js/base.schedule.btn.js'),
        path.resolve(__dirname, '../public/js/reschedule.btn.js'),
        path.resolve(__dirname, '../public/js/unschedule.btn.js'),
        path.resolve(__dirname, '../public/js/dashboard.js'),
    ]).addEntry('date-based-publisher-common-css', [path.resolve(__dirname, '../public/scss/date-based-publisher.scss')]);
};
