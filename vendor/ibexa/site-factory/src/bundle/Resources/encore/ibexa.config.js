const path = require('path');

module.exports = (Encore) => {
    Encore.addEntry('ibexa-site-factory-css', [path.resolve(__dirname, '../public/scss/ibexa.scss')])
        .addEntry('ibexa-site-factory-list-js', [
            path.resolve(__dirname, '../public/js/sites.list.js'),
            path.resolve(__dirname, '../public/js/sites.list.modal.delete.js'),
            path.resolve('./public/bundles/ibexaadminui/js/scripts/button.state.toggle.js'),
        ])
        .addEntry('ibexa-site-factory-form-js', [
            path.resolve('./public/bundles/ibexaadminui/js/scripts/admin.card.toggle.group.js'),
            path.resolve('./public/bundles/ibexaadminui/js/scripts/edit.header.js'),
            path.resolve(__dirname, '../public/js/form.public.access.js'),
            path.resolve(__dirname, '../public/js/form.design.js'),
            path.resolve(__dirname, '../public/js/form.languages.js'),
            path.resolve(__dirname, '../public/js/form.validate.js'),
            path.resolve(__dirname, '../public/js/form.parent.location.js'),
        ]);
};
