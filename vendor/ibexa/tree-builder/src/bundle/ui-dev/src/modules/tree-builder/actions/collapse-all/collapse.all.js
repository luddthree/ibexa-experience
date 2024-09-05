import React, { useContext } from 'react';
import PropTypes from 'prop-types';

import ActionItem from '../../components/action-list-item/action.list.item';
import { ExpandContext } from '../../components/local-storage-expand-connector/local.storage.expand.connector';
import { BuildItemContext, TreeContext } from '../../tree.builder.module';
import { STORED_ITEMS_REMOVE } from '../../hooks/useStoredItemsReducer';
import { getAllChildren } from '../../helpers/tree';
import { isItemEmpty } from '../../helpers/item';

const { Translator } = window;

const CollapseAll = ({ item, label, useIconAsLabel, afterCollapseCallback }) => {
    const buildItem = useContext(BuildItemContext);
    const { dispatchExpandedData } = useContext(ExpandContext);
    const tree = useContext(TreeContext);
    const itemLabel =
        label ||
        Translator.trans(
            /*@Desc("Collapse all")*/
            'actions.collapse_all',
            {},
            'ibexa_tree_builder_ui',
        );
    const data = isItemEmpty(item) ? tree : item;
    const canItemBeExpanded = (itemToCollapse) => !!itemToCollapse.subitems && itemToCollapse.subitems.length;
    const collapseAllNodes = () => {
        const items = getAllChildren({ data, buildItem, condition: canItemBeExpanded });

        dispatchExpandedData({ items, type: STORED_ITEMS_REMOVE });
        afterCollapseCallback(items);
    };

    return <ActionItem label={itemLabel} labelIcon="caret-up" useIconAsLabel={useIconAsLabel} onClick={collapseAllNodes} />;
};

CollapseAll.propTypes = {
    item: PropTypes.object,
    label: PropTypes.node,
    useIconAsLabel: PropTypes.bool,
    afterCollapseCallback: PropTypes.func,
};

CollapseAll.defaultProps = {
    item: {},
    label: null,
    useIconAsLabel: false,
    afterCollapseCallback: () => {},
};

export default CollapseAll;
