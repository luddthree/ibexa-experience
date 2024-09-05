import React, { useState, createContext, useContext, useEffect, useRef } from 'react';
import PropTypes from 'prop-types';

import { getRootDOMElement } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';

import { DraggableDisabledContext } from '../../tree.builder.module';
import { IntermediateActionContext } from '../intermediate-action-provider/intermediate.action.provider';
import { PlaceholderContext } from '../../components/placeholder-provider/placeholder.provider';
import { SelectedContext } from '../../components/selected-provider/selected.provider';
import { isItemDisabled } from '../../helpers/item';
import { STORED_ITEMS_CLEAR } from '../../hooks/useStoredItemsReducer';

export const DraggableContext = createContext();
export const DRAG_ID = 'DRAG';
const MOVED_INDICATOR_TIMEOUT = 1000;
const DRAGGED_ELEMENT_CENTER_POS = {
    x: 25,
    y: 16,
};

const DndProvider = ({ children, callbackMoveElements, treeRef }) => {
    const rootDOMElement = getRootDOMElement();
    const dragDisabled = useContext(DraggableDisabledContext);
    const { setIntermediateAction, intermediateAction, groupingItemId, clearIntermediateAction } = useContext(IntermediateActionContext);
    const { placeholderDataRef, setActiveItemsData, clearPlaceholder } = useContext(PlaceholderContext);
    const { selectedData: contextSelectedData, dispatchSelectedData } = useContext(SelectedContext);
    const portalRef = useRef(null);
    const [isDragging, setIsDragging] = useState(false);
    const [currentMousePosition, setCurrentMousePosition] = useState({});
    const getMousePosition = (event) => ({
        x: event.pageX - DRAGGED_ELEMENT_CENTER_POS.x,
        y: event.pageY - DRAGGED_ELEMENT_CENTER_POS.y,
    });
    const startDragging = (event, { item }) => {
        const isModalOpen = rootDOMElement.classList.contains('modal-open');

        if (dragDisabled || isModalOpen) {
            return;
        }

        event.preventDefault();
        setIsDragging(true);

        const selectedData = contextSelectedData.length ? contextSelectedData : [item];

        groupingItemId.current = null;

        setCurrentMousePosition(getMousePosition(event));
        setActiveItemsData(selectedData);
        setIntermediateAction({
            isActive: true,
            id: DRAG_ID,
            isItemDisabled: (itemToDisable, extras) => isItemDisabled(itemToDisable, { ...extras, selectedData }),
            listItems: selectedData,
        });
    };
    const clearDragAction = () => {
        clearIntermediateAction();
        clearPlaceholder();
        setIsDragging(false);
    };
    const toggleDragging = (disable) => {
        const scrollableWrapper = document.querySelector('.c-tb-tree__scrollable-wrapper');

        scrollableWrapper?.classList.toggle('c-tb-tree__scrollable-wrapper--disabled', !disable);
    };
    const stopDragging = () => {
        if (dragDisabled || !intermediateAction.isActive) {
            return;
        }

        groupingItemId.current = null;
        setIsDragging(false);
        callbackMoveElements(placeholderDataRef.current, { selectedData: intermediateAction.listItems }).then(() => {
            clearPlaceholder();
            setIntermediateAction((prevState) => ({
                ...prevState,
                highlightDestination: true,
            }));
            dispatchSelectedData({ type: STORED_ITEMS_CLEAR });
            toggleDragging(false);

            setTimeout(() => {
                clearIntermediateAction();
                toggleDragging(true);
            }, MOVED_INDICATOR_TIMEOUT);
        });
    };
    const handleMouseUpOutsideTree = (event) => {
        const treeList = treeRef.current.querySelector('.c-tb-list');

        if (treeList && !treeList.contains(event.target)) {
            clearDragAction();
        }
    };

    const handleMouseMove = (event) => {
        setCurrentMousePosition(getMousePosition(event));
    };

    useEffect(() => {
        if (intermediateAction.isActive) {
            rootDOMElement.addEventListener('mousemove', handleMouseMove);
            rootDOMElement.addEventListener('mouseup', handleMouseUpOutsideTree);
        }

        return () => {
            rootDOMElement.removeEventListener('mousemove', handleMouseMove);
            rootDOMElement.removeEventListener('mouseup', handleMouseUpOutsideTree);
        };
    }, [intermediateAction.isActive]);

    useEffect(() => {
        if (portalRef.current) {
            portalRef.current.setPortalPosition(currentMousePosition);
        }
    }, [currentMousePosition.x, currentMousePosition.y]);

    return (
        <DraggableContext.Provider value={{ startDragging, stopDragging, clearDragAction, isDragging, portalRef }}>
            {children}
        </DraggableContext.Provider>
    );
};

DndProvider.propTypes = {
    children: PropTypes.node.isRequired,
    callbackMoveElements: PropTypes.func,
    treeRef: PropTypes.object,
};

DndProvider.defaultProps = {
    callbackMoveElements: () => Promise.resolve(),
    treeRef: { current: null },
};

export default DndProvider;
