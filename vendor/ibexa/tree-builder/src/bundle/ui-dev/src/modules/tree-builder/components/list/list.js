import React, { Fragment, useContext, useEffect, useRef } from 'react';
import PropTypes from 'prop-types';

import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';
import ListItem from '../list-item/list.item';
import PlaceholderDestination from '../placeholder-destination/placeholder.destination';
import { PlaceholderContext } from '../placeholder-provider/placeholder.provider';
import { WidthContainerContext, checkIsTreeCollapsed } from '../width-container/width.container';
import { DraggableContext } from '../dnd-provider/dnd.provider';
import { IntermediateActionContext } from '../intermediate-action-provider/intermediate.action.provider';

const List = ({
    parents,
    isExpanded,
    subitems,
    depth,
    itemRef,
    setParentIndentHeight,
    rootSelectionDisabled,
    selectionDisabled,
    rootElementDisabled,
}) => {
    const prevIsExpanded = useRef(isExpanded);
    const { placeholderData } = useContext(PlaceholderContext);
    const [widthContainer] = useContext(WidthContainerContext);
    const { isDragging } = useContext(DraggableContext);
    const { intermediateAction } = useContext(IntermediateActionContext);
    const containerWidth = widthContainer.resizedContainerWidth ?? widthContainer.containerWidth;
    const isCollapsed = checkIsTreeCollapsed(containerWidth);
    const childrenDepth = depth + 1;
    let placeholderIndex = null;
    const isItemSource = (itemId) => {
        const isEqualItem = (itemToCompare) => itemToCompare.id === itemId;

        return intermediateAction.isActive && !intermediateAction.highlightDestination && intermediateAction.listItems.some(isEqualItem);
    };

    useEffect(() => {
        if (!subitems.length) {
            return;
        }

        if (isExpanded !== prevIsExpanded.current) {
            document.dispatchEvent(new CustomEvent('ibexa-tb-toggled-expand'));
        }

        prevIsExpanded.current = isExpanded;
    }, [isExpanded, subitems]);

    if (!subitems || !isExpanded || isCollapsed) {
        return null;
    }

    if (placeholderData.nextParent && parents.length && placeholderData.nextParent.id === parents[parents.length - 1].id) {
        placeholderIndex = placeholderData.nextIndex;
    }

    const listClasses = createCssClassNames({
        'c-tb-list': true,
        'c-tb-list--dragging': isDragging,
    });
    const isFirstSubitemSource = isItemSource(subitems[0]?.id);

    return (
        <ul className={listClasses}>
            {!isFirstSubitemSource && placeholderIndex === 0 && <PlaceholderDestination />}
            {subitems.map((subitem, index) => {
                const nextSubitemId = subitems[index + 1]?.id;
                const isNextSubitemSource = isItemSource(nextSubitemId);

                return (
                    <Fragment key={subitem.id ?? `def-${index}`}>
                        <ListItem
                            parents={parents}
                            item={subitem}
                            index={index}
                            itemDepth={childrenDepth}
                            isRoot={childrenDepth === 0}
                            rootSelectionDisabled={rootSelectionDisabled}
                            selectionDisabled={selectionDisabled}
                            itemRef={itemRef}
                            isLastItem={index === subitems.length - 1}
                            setParentIndentHeight={setParentIndentHeight}
                            showPlaceholder={!isNextSubitemSource && placeholderIndex === index + 1}
                            showHighlight={placeholderData?.nextParent?.id === subitem.id && placeholderData?.nextIndex === -1}
                            rootElementDisabled={rootElementDisabled}
                        />
                    </Fragment>
                );
            })}
        </ul>
    );
};

List.propTypes = {
    isExpanded: PropTypes.bool.isRequired,
    depth: PropTypes.number,
    parents: PropTypes.array,
    subitems: PropTypes.array,
    itemRef: PropTypes.object,
    setParentIndentHeight: PropTypes.func,
    rootSelectionDisabled: PropTypes.bool,
    selectionDisabled: PropTypes.bool,
    rootElementDisabled: PropTypes.bool,
};

List.defaultProps = {
    depth: 0,
    parents: [],
    subitems: [],
    itemRef: null,
    setParentIndentHeight: () => {},
    rootSelectionDisabled: false,
    selectionDisabled: false,
    rootElementDisabled: false,
};

export default List;
