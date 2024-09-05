import React, { useContext } from 'react';
import PropTypes from 'prop-types';

import ActionItem from '../../components/action-list-item/action.list.item';
import { BuildItemContext, DelayedChildrenSelectContext, TreeContext } from '../../tree.builder.module';
import { SelectedContext } from '../../components/selected-provider/selected.provider';
import { STORED_ITEMS_ADD } from '../../hooks/useStoredItemsReducer';
import { isItemEmpty } from '../../helpers/item';
import { ExpandContext } from '../../components/local-storage-expand-connector/local.storage.expand.connector';
import { DELAYED_CHILDREN_SELECT_ADD } from '../../hooks/useDelayedChildrenSelectReducer';
import { getAllChildren } from '../../helpers/tree';

const { Translator } = window;

const SelectAll = ({ item, label, useIconAsLabel }) => {
    const buildItem = useContext(BuildItemContext);
    const { dispatchSelectedData } = useContext(SelectedContext);
    const { dispatchExpandedData } = useContext(ExpandContext);
    const tree = useContext(TreeContext);
    const { dispatchDelayedChildrenSelectAction } = useContext(DelayedChildrenSelectContext);
    const isMultipleItemsAction = isItemEmpty(item);
    const getDefaultLabel = () => {
        if (isMultipleItemsAction) {
            return Translator.trans(
                /*@Desc("Select all elements")*/
                'actions.select.all.elements',
                {},
                'ibexa_tree_builder_ui',
            );
        }

        return Translator.trans(
            /*@Desc("Select all children")*/
            'actions.select.all',
            {},
            'ibexa_tree_builder_ui',
        );
    };
    const itemLabel = label || getDefaultLabel();

    if (isMultipleItemsAction && tree === null) {
        return <ActionItem label={itemLabel} labelIcon="checkmark" useIconAsLabel={useIconAsLabel} isDisabled={true} />;
    }

    const data = isMultipleItemsAction ? buildItem(tree) : item;
    const selectAll = () => {
        const allSubitemsWithLoadedSubitems = getAllChildren({
            data,
            buildItem,
            condition: (subitem) => subitem.subitems.length > 0 || subitem.id === item.id,
        });
        const shouldSelectAlsoParent = isMultipleItemsAction;

        dispatchExpandedData({ type: STORED_ITEMS_ADD, items: allSubitemsWithLoadedSubitems });

        if (shouldSelectAlsoParent) {
            dispatchSelectedData({ type: STORED_ITEMS_ADD, items: [data] });
        }

        dispatchDelayedChildrenSelectAction({ type: DELAYED_CHILDREN_SELECT_ADD, parentId: data.id });
    };

    return <ActionItem label={itemLabel} labelIcon="checkmark" useIconAsLabel={useIconAsLabel} onClick={selectAll} />;
};

SelectAll.propTypes = {
    item: PropTypes.object,
    label: PropTypes.node,
    useIconAsLabel: PropTypes.bool,
};

SelectAll.defaultProps = {
    item: {},
    label: null,
    useIconAsLabel: false,
};

export default SelectAll;
