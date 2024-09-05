const path = require('path');
const fs = require('fs');

const attributeTypesPath = path.resolve(__dirname, '../public/js/attributeType/');
const fieldTypesPath = path.resolve(__dirname, '../public/js/fieldType/');
const attributeTypes = [];
const fieldTypes = [];

fs.readdirSync(attributeTypesPath).forEach((file) => {
    if (path.extname(file) === '.js') {
        attributeTypes.push(path.resolve(attributeTypesPath, file));
    }
});

fs.readdirSync(fieldTypesPath).forEach((file) => {
    if (path.extname(file) === '.js') {
        fieldTypes.push(path.resolve(fieldTypesPath, file));
    }
});

module.exports = (Encore) => {
    Encore.addEntry('ibexa-product-catalog-css', [path.resolve(__dirname, '../public/scss/ibexa-product-catalog.scss')])
        .addEntry('ibexa-product-catalog-list-js', [
            path.resolve(__dirname, '../public/js/base.list.js'),
            path.resolve('./public/bundles/ibexaadminui/js/scripts/sidebar/extra.actions.js'),
        ])
        .addEntry('ibexa-product-catalog-product-list-editable-js', [
            path.resolve('./public/bundles/ibexaadminui/js/scripts/sidebar/extra.actions.js'),
        ])
        .addEntry('ibexa-product-catalog-product-price-edit-js', [path.resolve(__dirname, '../public/js/product.price.edit.js')])
        .addEntry('ibexa-product-catalog-product-availability-edit-js', [
            path.resolve(__dirname, '../public/js/product.availability.edit.js'),
        ])
        .addEntry('ibexa-product-catalog-edit-js', [path.resolve(__dirname, '../public/js/edit.form.validator.js')])
        .addEntry('ibexa-product-catalog-product-edit-js', [...attributeTypes, ...fieldTypes])
        .addEntry('ibexa-product-catalog-product-type-view-js', [path.resolve(__dirname, '../public/js/details.language.switcher.js')])
        .addEntry('ibexa-product-catalog-product-type-edit-js', [path.resolve(__dirname, '../public/js/product.type.edit.js')])
        .addEntry('ibexa-product-catalog-attribute-definition-edit-js', [
            path.resolve(__dirname, '../public/js/attribute.selection.edit.js'),
        ])
        .addEntry('ibexa-product-catalog-catalog-edit-js', [
            path.resolve(__dirname, '../public/js/filterConfig/choice.filter.config.js'),
            path.resolve(__dirname, '../public/js/filterConfig/taggify.filter.config.js'),
            path.resolve(__dirname, '../public/js/filterConfig/radio.filter.config.js'),
            path.resolve(__dirname, '../public/js/filterConfig/price.filter.config.js'),
            path.resolve(__dirname, '../public/js/filterConfig/daterange.filter.config.js'),
            path.resolve(__dirname, '../public/js/filterConfig/number.range.filter.config.js'),
            path.resolve(__dirname, '../public/js/filterConfig/taxonomy.filter.config.js'),
            path.resolve(__dirname, '../public/js/catalog.edit.js'),
        ])
        .addEntry('ibexa-product-catalog-catalog-view-js', [path.resolve(__dirname, '../public/js/catalog.transition.js')])
        .addEntry('ibexa-product-catalog-attribute-group-view-js', [
            path.resolve(__dirname, '../public/js/base.list.js'),
            path.resolve('./public/bundles/ibexaadminui/js/scripts/admin.location.tab.js'),
            path.resolve('./public/bundles/ibexaadminui/js/scripts/sidebar/extra.actions.js'),
        ])
        .addEntry('ibexa-product-catalog-product-view-js', [
            path.resolve(__dirname, '../public/js/product.view.js'),
            path.resolve(__dirname, '../public/js/product.price.tab.js'),
            path.resolve(__dirname, '../public/js/details.language.switcher.js'),
            path.resolve(__dirname, '../public/js/generate.variants.js'),
            path.resolve(__dirname, '../public/js/product.completeness.tab.js'),
            path.resolve(__dirname, '../public/js/product.assets.tab.js'),
            path.resolve(__dirname, '../public/js/product.variants.tab.js'),
            path.resolve('./public/bundles/ibexaadminui/js/scripts/sidebar/extra.actions.js'),
            path.resolve('./public/bundles/ibexaadminui/js/scripts/admin.location.add.custom_url.js'),
            path.resolve('./public/bundles/ibexaadminui/js/scripts/button.state.toggle.js'),
        ])
        .addEntry('ibexa-product-catalog-category-products-list-js', [path.resolve(__dirname, '../public/js/base.list.js')])
        .addEntry('ibexa-catalog-block-js', [path.resolve(__dirname, '../public/js/catalog.block.js')])
        .addEntry('ibexa-product-collection-block-js', [path.resolve(__dirname, '../public/js/product.collection.block.js')])
        .addEntry('ibexa-catalog-filter-js', [
            path.resolve(__dirname, '../../ui-dev/src/modules/category-filter-tree/config.loader.js'),
            path.resolve(__dirname, '../public/js/category.filter.tree.js'),
        ]);
};
