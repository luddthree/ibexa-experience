const path = require('path');

module.exports = (Encore) => {
    Encore.addEntry('ibexa-corporate-account-css', [path.resolve(__dirname, '../public/scss/ibexa-corporate-account.scss')])
        .addEntry('ibexa-corporate-account-js', [
            path.resolve('./public/bundles/ibexaadminui/js/scripts/sidebar/extra.actions.js'),
            path.resolve('./public/bundles/ibexaadminui/js/scripts/button.state.toggle.js'),
        ])
        .addEntry('ibexa-customer-portal-css', [path.resolve(__dirname, '../public/scss/ibexa-customer-portal.scss')])
        .addEntry('ibexa-customer-portal-js', [path.resolve(__dirname, '../public/js/customer-portal/user.menu.js')])
        .addEntry('ibexa-corporate-account-common-js', [
            path.resolve(__dirname, '../public/js/common/update.default.shipping.address.js'),
            path.resolve(__dirname, '../public/js/common/bulk.delete.js'),
            path.resolve(__dirname, '../public/js/common/filters.action.btns.js'),
            path.resolve(__dirname, '../public/js/common/invitation.table.js'),
            path.resolve(__dirname, '../public/js/common/invite.members.js'),
            path.resolve(__dirname, '../public/js/common/tucked.menu.js'),
            path.resolve(__dirname, '../public/js/common/user.role.change.js'),
            path.resolve(__dirname, '../public/js/common/user.status.change.confirmation.modal.js'),
        ])
        .addEntry('ibexa-customer-portal-login-css', [
            path.resolve('./public/bundles/ibexaadminui/scss/ibexa-bootstrap.scss'),
            path.resolve('./public/bundles/ibexaadminui/scss/ibexa.scss'),
            path.resolve(__dirname, '../public/scss/ibexa-customer-portal-login.scss'),
        ])
        .addEntry('ibexa-customer-portal-login-js', [
            path.resolve('./public/bundles/ibexaadminui/js/scripts/admin.input.text.js'),
            path.resolve(__dirname, '../public/js/customer-portal/login.js'),
        ]);
};
