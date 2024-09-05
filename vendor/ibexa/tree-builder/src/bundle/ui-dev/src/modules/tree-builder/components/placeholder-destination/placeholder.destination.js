import React, { useContext } from 'react';

import { DraggableContext } from '../dnd-provider/dnd.provider';
import { IntermediateActionContext } from '../intermediate-action-provider/intermediate.action.provider';

const PlaceholderDestination = () => {
    const { stopDragging } = useContext(DraggableContext);
    const { intermediateAction } = useContext(IntermediateActionContext);
    const stopDraggingItem = (event) => stopDragging(event);
    const stopIntermediateAction = (event) => {
        if (intermediateAction.isActive) {
            event.preventDefault();

            if (intermediateAction.callback) {
                intermediateAction.callback();
            }
        }
    };

    return (
        <div
            className="c-tb-list-item-single c-tb-list-placeholder-destination"
            onDragEnd={(event) => stopDragging(event)}
            onClick={stopIntermediateAction}
            onMouseUp={stopDraggingItem}
        />
    );
};

export default PlaceholderDestination;
