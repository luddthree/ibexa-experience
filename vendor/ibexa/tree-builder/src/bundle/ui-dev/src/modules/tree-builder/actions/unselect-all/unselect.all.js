import React, { useContext } from 'react';
import PropTypes from 'prop-types';

import ActionItem from '../../components/action-list-item/action.list.item';
import { BuildItemContext, TreeContext } from '../../tree.builder.module';
import { SelectedContext } from '../../components/selected-provider/selected.provider';
import { STORED_ITEMS_REMOVE } from '../../hooks/useStoredItemsReducer';
import { getAllChildren } from '../../helpers/tree';
import { isItemEmpty } from '../../helpers/item';

const { Translator } = window;

const UnselectAll = ({ item, label, useIconAsLabel }) => {
    const buildItem = useContext(BuildItemContext);
    const { dispatchSelectedData } = useContext(SelectedContext);
    const tree = useContext(TreeContext);
    const isMultipleItemsAction = isItemEmpty(item);
    const getDefaultLabel = () => {
        if (isItemEmpty(item)) {
            return Translator.trans(
                /*@Desc("Unselect all elements")*/
                'actions.unselect.all.elements',
                {},
                'ibexa_tree_builder_ui',
            );
        }

        return Translator.trans(
            /*@Desc("Unselect all children")*/
            'actions.unselect.all',
            {},
            'ibexa_tree_builder_ui',
        );
    };
    const itemLabel = label || getDefaultLabel();

    if (isMultipleItemsAction && tree === null) {
        return <ActionItem label={itemLabel} labelIcon="checkmark" useIconAsLabel={useIconAsLabel} isDisabled={true} />;
    }

    const data = isMultipleItemsAction ? tree : item;
    const unselectAll = () => {
        const items = getAllChildren({ data, buildItem });

        dispatchSelectedData({ type: STORED_ITEMS_REMOVE, items });
    };

    return <ActionItem label={itemLabel} labelIcon="checkmark" useIconAsLabel={useIconAsLabel} onClick={unselectAll} />;
};

UnselectAll.propTypes = {
    item: PropTypes.object,
    label: PropTypes.node,
    useIconAsLabel: PropTypes.bool,
};

UnselectAll.defaultProps = {
    item: {},
    label: null,
    useIconAsLabel: false,
};

export default UnselectAll;
