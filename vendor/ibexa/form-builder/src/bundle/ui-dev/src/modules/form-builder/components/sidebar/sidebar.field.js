import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

class SidebarField extends PureComponent {
    constructor(props) {
        super(props);

        this.handleDragStart = this.handleDragStart.bind(this);
        this.handleDragEnd = this.handleDragEnd.bind(this);
        this.appendNewField = this.appendNewField.bind(this);
        this.state = {
            isDragging: false,
        };
    }

    handleDragStart(event) {
        event.dataTransfer.effectAllowed = 'copy';
        event.dataTransfer.setData('text/html', event.currentTarget);

        const { onDragStart, type } = this.props;

        onDragStart({ identifier: type });

        this.setState(() => ({ isDragging: true }));
    }

    handleDragEnd() {
        this.props.onDragEnd();

        this.setState(() => ({ isDragging: false }));
    }

    appendNewField() {
        const { appendNewField, type } = this.props;

        appendNewField(type);
    }

    render() {
        const { name, thumbnail, type, isHidden } = this.props;
        const sidebarFieldClassName = createCssClassNames({
            'c-ibexa-fb-sidebar-field': true,
            'c-ibexa-fb-sidebar-field--hidden': isHidden,
            'c-ibexa-fb-sidebar-field--is-dragging-out': this.state.isDragging,
        });

        return (
            <div
                className={sidebarFieldClassName}
                onDragStart={this.handleDragStart}
                onDragEnd={this.handleDragEnd}
                data-ibexa-sidebar-field-type={type}
                onClick={this.appendNewField}
            >
                <div className="c-ibexa-fb-sidebar-field__content" draggable={true}>
                    <div className="c-ibexa-fb-sidebar-field__drag">
                        <Icon name="drag" extraClasses="c-ibexa-fb-sidebar-field__drag-icon ibexa-icon--tiny-small" />
                    </div>
                    <div className="c-ibexa-fb-sidebar-field__type">
                        <Icon customPath={thumbnail} extraClasses="c-ibexa-fb-sidebar-field__type-icon ibexa-icon--small" />
                    </div>
                    <div className="c-ibexa-fb-sidebar-field__label">{name}</div>
                </div>
            </div>
        );
    }
}

SidebarField.propTypes = {
    type: PropTypes.string.isRequired,
    name: PropTypes.string.isRequired,
    onDragStart: PropTypes.func.isRequired,
    onDragEnd: PropTypes.func.isRequired,
    thumbnail: PropTypes.string.isRequired,
    isHidden: PropTypes.bool.isRequired,
    appendNewField: PropTypes.func.isRequired,
};

export default SidebarField;
