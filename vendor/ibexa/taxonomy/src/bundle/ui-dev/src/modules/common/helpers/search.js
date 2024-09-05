import { getMenuActions as baseGetMenuActions } from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/helpers/tree';

const getMenuActions = ({ actions, item }) => {
    const activeActionsIds = ['add', 'delete', 'assignContent'];

    return baseGetMenuActions({ actions, item, activeActionsIds });
};

export const getSearchTreeProps = ({ searchTree, searchActive }) => {
    if (!searchActive) {
        return {};
    }

    return {
        tree: searchTree,
        isLocalStorageActive: false,
        getMenuActions,
        extraClasses: 'c-tb-tree--non-expandable',
    };
};
