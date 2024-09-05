const path = require('path');

module.exports = (ibexaConfig, ibexaConfigManager) => {
    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-layout-js',
        newItems: [
            path.resolve(__dirname, '../public/js/admin.context.switcher.js'),
            path.resolve(__dirname, '../public/js/admin.location.preview.tab.js'),
            path.resolve(__dirname, '../public/js/user_menu.focus_mode.toggler.js'),
        ],
    });

    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-layout-css',
        newItems: [path.resolve(__dirname, '../public/scss/site-context.scss')],
    });
};
