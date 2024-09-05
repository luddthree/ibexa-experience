import React, { useContext } from 'react';
import PropTypes from 'prop-types';

import ListItemSingle from '../list-item-single/list.item.single';
import PlaceholderDestination from '../placeholder-destination/placeholder.destination';
import PlaceholderSource from '../placeholder-source/placeholder.source';
import useBuildItem from '../../hooks/useBuildItem';
import { IntermediateActionContext } from '../intermediate-action-provider/intermediate.action.provider';
import { DraggableContext } from '../dnd-provider/dnd.provider';
import Portal from '../portal/portal';

const ListItem = (props) => {
    const { showPlaceholder, item: originalItem, ...restProps } = props;
    const item = useBuildItem(originalItem, restProps);
    const { portalRef } = useContext(DraggableContext);
    const { intermediateAction, groupingItemId } = useContext(IntermediateActionContext);
    const isEqualItem = (itemToCompare) => itemToCompare.id === item.id;
    const isSource =
        intermediateAction.isActive && !intermediateAction.highlightDestination && intermediateAction.listItems.some(isEqualItem);

    if (isSource && groupingItemId.current === null) {
        groupingItemId.current = item.id;
    }

    const renderContent = () => {
        if (isSource && groupingItemId.current !== item.id) {
            return <PlaceholderSource />;
        }

        if (isSource && groupingItemId.current === item.id) {
            return (
                <>
                    <PlaceholderSource />
                    <Portal ref={portalRef} extraClasses="c-tb-drag-and-drop-portal">
                        <div className="c-tb-list-item c-tb-list-item--grouped-source">
                            <ul className="c-tb-list-item__group">
                                {intermediateAction.listItems.map((listItem) => (
                                    <ListItemSingle key={listItem.id} item={listItem} {...restProps} />
                                ))}
                            </ul>
                            <div className="c-tb-list-item__cover" />
                        </div>
                    </Portal>
                </>
            );
        }

        return (
            <>
                <ListItemSingle item={item} {...restProps} />
                {showPlaceholder && <PlaceholderDestination />}
            </>
        );
    };

    return <li>{renderContent()}</li>;
};

ListItem.propTypes = {
    showPlaceholder: PropTypes.bool.isRequired,
    item: PropTypes.object.isRequired,
};

export default ListItem;
