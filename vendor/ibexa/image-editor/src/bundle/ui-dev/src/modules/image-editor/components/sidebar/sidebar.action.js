import React, { useState } from 'react';
import PropTypes from 'prop-types';

import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

const SidebarAction = (props) => {
    const Component = props.component;
    const [collapsed, setCollapsed] = useState(false);
    const className = createCssClassNames({
        'c-image-editor-sidebar-action': true,
        'c-image-editor-sidebar-action--collapsed': collapsed,
    });
    const toggleComponent = () => {
        setCollapsed((collapsedPrev) => !collapsedPrev);
    };

    return (
        <div className={className}>
            <div className="c-image-editor-sidebar-action__header" onClick={toggleComponent}>
                <Icon customPath={props.icon} extraClasses="ibexa-icon--small-medium c-image-editor-sidebar-action__icon" />
                <h4 className="c-image-editor-sidebar-action__label">{props.label}</h4>
                <Icon name="caret-down" extraClasses="ibexa-icon--small-medium c-image-editor-sidebar-action__toggler" />
            </div>
            <div className="c-image-editor-sidebar-action__component">
                <Component config={props.componentConfig} />
            </div>
        </div>
    );
};

SidebarAction.propTypes = {
    component: PropTypes.element.isRequired,
    icon: PropTypes.string.isRequired,
    label: PropTypes.string.isRequired,
    identifier: PropTypes.string.isRequired,
    componentConfig: PropTypes.object.isRequired,
};

SidebarAction.defaultProps = {};

export default SidebarAction;
