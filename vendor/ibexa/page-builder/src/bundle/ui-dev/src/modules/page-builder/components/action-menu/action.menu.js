import React, { forwardRef } from 'react';
import PropTypes from 'prop-types';

import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

const ActionMenu = forwardRef(({ label, handleDragStart, isEditable, children }, ref) => {
    const attrs = {
        className: createCssClassNames({
            'c-pb-action-menu': true,
            'c-pb-action-menu--editable': isEditable,
        }),
    };
    const handleMouseDown = isEditable ? handleDragStart : null;

    return (
        <div {...attrs} ref={ref}>
            <div className="c-pb-action-menu__name-wrapper" onMouseDown={handleMouseDown}>
                <div className="c-pb-action-menu__drag">
                    <Icon name="drag" extraClasses="c-pb-action-menu__drag-icon" />
                </div>
                <div className="c-pb-action-menu__name">{label}</div>
            </div>
            <div className="c-pb-action-menu__actions">{children}</div>
        </div>
    );
});

ActionMenu.propTypes = {
    label: PropTypes.node.isRequired,
    children: PropTypes.element.isRequired,
    handleDragStart: PropTypes.func.isRequired,
    isEditable: PropTypes.bool.isRequired,
};

ActionMenu.displayName = 'ActionMenu';

export default ActionMenu;
