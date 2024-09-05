import React, { useContext } from 'react';

import { DraggableContext } from '../dnd-provider/dnd.provider';

const PlaceholderSource = () => {
    const { clearDragAction } = useContext(DraggableContext);

    return <div onMouseUp={clearDragAction} className="c-tb-list-item-single c-tb-list-placeholder-source" />;
};

export default PlaceholderSource;
