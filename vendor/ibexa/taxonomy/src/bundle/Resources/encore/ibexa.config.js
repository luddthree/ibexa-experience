const path = require('path');

module.exports = (Encore) => {
    Encore.addEntry('ibexa-taxonomy-location-view-js', [
        path.resolve(__dirname, '../../ui-dev/src/modules/taxonomy-tree/taxonomy.tree.module.js'),
        path.resolve(__dirname, '../public/js/admin.taxonomy.tree.js'),
        path.resolve(__dirname, '../public/js/admin.taxonomy.content.assign.js'),
        path.resolve(__dirname, '../public/js/extra.actions.js'),
    ]);
};
