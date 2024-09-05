import React from 'react';
import PropTypes from 'prop-types';

import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';

const Block = ({ type, name, onDrag, onDragStart, thumbnail, isDraggable, isHidden }) => {
    const handleDragStart = (event) => {
        event.dataTransfer.effectAllowed = 'copy';
        event.dataTransfer.setData('text/html', event.currentTarget);
    };
    const handleMouseDown = () => {
        onDragStart(type);
    };
    const addNameEllipsisRef = (node) => {
        if (node && node.scrollWidth > node.clientWidth) {
            node.title = name;

            window.ibexa.helpers.tooltips.parse(node);
        }
    };
    const blockAttrs = {
        className: 'c-pb-toolbox-block',
        hidden: isHidden,
    };
    const blockContentAttrs = {
        className: 'c-pb-toolbox-block__content',
    };

    if (isDraggable) {
        blockAttrs.className = `${blockAttrs.className} ${blockAttrs.className}--draggable`;
        blockContentAttrs.draggable = 'true';
        blockContentAttrs.onDragStart = handleDragStart;
        blockContentAttrs.onDrag = onDrag;
        blockContentAttrs.onMouseDown = handleMouseDown;
    }

    return (
        <div {...blockAttrs}>
            <div {...blockContentAttrs}>
                <div className="c-pb-toolbox-block__drag">
                    <Icon name="drag" extraClasses="c-pb-toolbox-block__drag-icon ibexa-icon--tiny-small" />
                </div>
                <div className="c-pb-toolbox-block__type">
                    <Icon customPath={thumbnail} extraClasses="ibexa-icon--small" />
                </div>
                <div ref={addNameEllipsisRef} className="c-pb-toolbox-block__label">
                    {name}
                </div>
            </div>
        </div>
    );
};

Block.propTypes = {
    type: PropTypes.string.isRequired,
    name: PropTypes.string.isRequired,
    onDrag: PropTypes.func.isRequired,
    onDragStart: PropTypes.func.isRequired,
    thumbnail: PropTypes.string.isRequired,
    isDraggable: PropTypes.bool.isRequired,
    isHidden: PropTypes.bool.isRequired,
};

export default Block;
