import React from 'react';

import useLocalStorage from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/hooks/useLocalStorage';
import TreeBuilderCollapseAll from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/actions/collapse-all/collapse.all';

const removeFromSubtree = (subtree, idsToRemove) => {
    subtree.forEach((item) => {
        if (idsToRemove.includes(item.locationId)) {
            item.children = [];
        } else {
            removeFromSubtree(item.children, idsToRemove);
        }
    });
};

const CollapseAll = (props) => {
    const { getLocalStorageData, saveLocalStorageData } = useLocalStorage('subtree');
    const afterCollapseCallback = (items) => {
        const subtree = getLocalStorageData();
        const idsToRemove = items.map((item) => item.id);

        removeFromSubtree(subtree, idsToRemove);

        saveLocalStorageData(subtree);
    };

    return <TreeBuilderCollapseAll {...props} afterCollapseCallback={afterCollapseCallback} />;
};

export default CollapseAll;
