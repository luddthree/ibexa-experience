import React, { useState } from 'react';
import PropTypes from 'prop-types';

import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

const SidebarGroup = (props) => {
    const [collapsed, setCollapsed] = useState(false);
    const className = createCssClassNames({
        'c-image-editor-sidebar-group': true,
        'c-image-editor-sidebar-group--collapsed': collapsed,
    });
    const toggleComponent = () => {
        setCollapsed((collapsedPrev) => !collapsedPrev);
    };

    return (
        <div className={className}>
            <div className="c-image-editor-sidebar-group__header" onClick={toggleComponent}>
                <h3 className="c-image-editor-sidebar-group__label">{props.label}</h3>
                <Icon name="caret-down" extraClasses="ibexa-icon--small-medium c-image-editor-sidebar-group__toggler" />
            </div>
            <div className="c-image-editor-sidebar-group__items">{props.children}</div>
        </div>
    );
};

SidebarGroup.propTypes = {
    children: PropTypes.element.isRequired,
    label: PropTypes.string.isRequired,
};

SidebarGroup.defaultProps = {};

export default SidebarGroup;
