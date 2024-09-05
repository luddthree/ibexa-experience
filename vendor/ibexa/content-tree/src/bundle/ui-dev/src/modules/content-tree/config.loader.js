import ContentTreeModule, { treeBuilderConfig } from './content.tree.module';

(function (ibexa) {
    ibexa.addConfig('treeBuilder', treeBuilderConfig);
    ibexa.addConfig('modules.ContentTree', ContentTreeModule);
})(window.ibexa);
