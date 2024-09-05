const path = require('path');

module.exports = (Encore) => {
    Encore.addEntry('ibexa-workflow-common-js', [
        path.resolve(__dirname, '../public/js/workflow.transition.apply.widget.js'),
        path.resolve(__dirname, '../public/js/workflow.admin.dashboard.js'),
    ])
        .addEntry('ibexa-workflow-view-js', [path.resolve('./public/bundles/ibexaadminui/js/scripts/admin.location.tab.js')])
        .addEntry('ibexa-workflow-common-css', [path.resolve(__dirname, '../public/scss/workflow.scss')]);
};
