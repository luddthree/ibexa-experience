import React, { useRef, useContext } from 'react';
import PropTypes from 'prop-types';

import ActionItem from '../../components/action-list-item/action.list.item';
import { CallbackContext } from '../../tree.builder.module';
import { PlaceholderContext } from '../../components/placeholder-provider/placeholder.provider';
import { SelectedContext } from '../../components/selected-provider/selected.provider';
import { IntermediateActionContext } from '../../components/intermediate-action-provider/intermediate.action.provider';
import { DraggableContext } from '../../components/dnd-provider/dnd.provider';
import { STORED_ITEMS_CLEAR } from '../../hooks/useStoredItemsReducer';
import { isItemDisabled, isItemEmpty } from '../../helpers/item';

export const MOVE_ID = 'MOVE';
const ACTION_TIMEOUT = 250;
const MOVED_INDICATOR_TIMEOUT = 1000;
const { Translator } = window;

const Move = ({ item, label, useIconAsLabel, canBeDestination, reorderAvailable, checkIsDisabled }) => {
    const actionTimeout = useRef();
    const { startDragging } = useContext(DraggableContext);
    const { selectedData: contextSelectedData, dispatchSelectedData } = useContext(SelectedContext);
    const { callbackMoveElements } = useContext(CallbackContext);
    const { setIntermediateAction, groupingItemId, clearIntermediateAction } = useContext(IntermediateActionContext);
    const { clearPlaceholder } = useContext(PlaceholderContext);
    const itemLabel =
        label ||
        Translator.trans(
            /*@Desc("Move")*/
            'actions.move',
            {},
            'ibexa_tree_builder_ui',
        );
    const selectedData = isItemEmpty(item) ? contextSelectedData : [item];
    const isDisabled = selectedData.length === 0 || checkIsDisabled(selectedData);
    const startMoving = (event) => {
        groupingItemId.current = null;

        if (!reorderAvailable) {
            setIntermediateAction({
                isActive: true,
                listItems: selectedData,
                id: MOVE_ID,
                isItemDisabled: (itemToMove, extras) => {
                    return isItemDisabled(itemToMove, { ...extras, selectedData }) || !canBeDestination(itemToMove);
                },
                callback: (itemToMove) => {
                    clearTimeout(actionTimeout.current);

                    actionTimeout.current = setTimeout(() => {
                        groupingItemId.current = null;

                        callbackMoveElements(itemToMove, { selectedData }).then(() => {
                            clearPlaceholder();
                            setIntermediateAction((prevState) => ({
                                ...prevState,
                                highlightDestination: true,
                            }));
                            dispatchSelectedData({ type: STORED_ITEMS_CLEAR });

                            setTimeout(() => {
                                clearIntermediateAction();
                            }, MOVED_INDICATOR_TIMEOUT);
                        });
                    }, ACTION_TIMEOUT);
                },
            });
        } else {
            startDragging(event, { item });
        }
    };

    return <ActionItem label={itemLabel} labelIcon="move" useIconAsLabel={useIconAsLabel} onClick={startMoving} isDisabled={isDisabled} />;
};

Move.propTypes = {
    item: PropTypes.object,
    label: PropTypes.node,
    useIconAsLabel: PropTypes.bool,
    canBeDestination: PropTypes.func,
    checkIsDisabled: PropTypes.func,
    reorderAvailable: PropTypes.bool,
};

Move.defaultProps = {
    item: {},
    label: null,
    useIconAsLabel: false,
    canBeDestination: () => true,
    checkIsDisabled: () => false,
    reorderAvailable: true,
};

export default Move;
