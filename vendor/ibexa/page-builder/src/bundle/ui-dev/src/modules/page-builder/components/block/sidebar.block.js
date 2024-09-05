import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

/**
 * @deprecated This component is deprecated and will be removed in 5.0; Use block.js
 */
class SidebarBlock extends PureComponent {
    constructor(props) {
        super(props);

        this.handleDragStart = this.handleDragStart.bind(this);
        this.handleMouseDown = this.handleMouseDown.bind(this);
        this.addNameEllipsis = this.addNameEllipsis.bind(this);
    }

    handleDragStart(event) {
        event.dataTransfer.effectAllowed = 'copy';
        event.dataTransfer.setData('text/html', event.currentTarget);
    }

    handleMouseDown() {
        const { onDragStart, type } = this.props;

        onDragStart(type);
    }

    addNameEllipsis(node) {
        if (node && node.scrollWidth > node.clientWidth) {
            node.title = this.props.name;

            window.ibexa.helpers.tooltips.parse(node);
        }
    }

    render() {
        const { name, thumbnail, type, isDraggable, isHidden } = this.props;
        const blockClassName = createCssClassNames({
            'c-pb-sidebar-block': true,
            'c-pb-sidebar-block--draggable': isDraggable,
            'c-pb-sidebar-block--unavailable': !isDraggable,
        });
        const blockAttrs = {
            className: blockClassName,
            'data-ibexa-sidebar-block-type': type,
            hidden: isHidden,
        };
        const blockContentAttrs = {
            className: 'c-pb-sidebar-block__content',
        };

        if (isDraggable) {
            blockContentAttrs.draggable = 'true';
            blockContentAttrs.onDragStart = this.handleDragStart;
            blockContentAttrs.onDrag = this.props.onDrag;
            blockContentAttrs.onMouseDown = this.handleMouseDown;
        }

        return (
            <div {...blockAttrs}>
                <div {...blockContentAttrs}>
                    <div className="c-pb-sidebar-block__drag">
                        <Icon name="drag" extraClasses="c-pb-sidebar-block__drag-icon ibexa-icon--tiny-small" />
                    </div>
                    <div className="c-pb-sidebar-block__type">
                        <Icon customPath={thumbnail} extraClasses="ibexa-icon--small" />
                    </div>
                    <div className="c-pb-sidebar-block__label" ref={this.addNameEllipsis}>
                        {name}
                    </div>
                </div>
            </div>
        );
    }
}

SidebarBlock.propTypes = {
    type: PropTypes.string.isRequired,
    name: PropTypes.string.isRequired,
    onDrag: PropTypes.func.isRequired,
    onDragStart: PropTypes.func.isRequired,
    thumbnail: PropTypes.string.isRequired,
    isDraggable: PropTypes.bool.isRequired,
    isHidden: PropTypes.bool.isRequired,
};

export default SidebarBlock;
