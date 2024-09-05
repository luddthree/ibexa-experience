import React, { useState } from 'react';
import PropTypes from 'prop-types';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

const SidebarFieldsGroup = (props) => {
    const [isCollapsed, setIsCollapsed] = useState(props.isCollapsed);
    const fieldsGroupClassName = createCssClassNames({
        'c-ibexa-fb-sidebar-fields-group': true,
        'c-ibexa-fb-sidebar-fields-group--collapsed': isCollapsed,
    });

    return (
        <div className={fieldsGroupClassName}>
            <div
                className="c-ibexa-fb-sidebar-fields-group__title-bar"
                onClick={() => setIsCollapsed((isCollapsedPrev) => !isCollapsedPrev)}
            >
                <div className="c-ibexa-fb-sidebar-fields-group__title">{props.title}</div>
                <div className="c-ibexa-fb-sidebar-fields-group__toggler">
                    <Icon name="caret-down" extraClasses="ibexa-icon--tiny-small" />
                </div>
            </div>
            <div className="c-ibexa-fb-sidebar-fields-group__fields">{props.children}</div>
        </div>
    );
};

SidebarFieldsGroup.propTypes = {
    children: PropTypes.node.isRequired,
    isCollapsed: PropTypes.bool,
    title: PropTypes.string,
};

SidebarFieldsGroup.defaultProps = {
    isCollapsed: false,
    title: '',
};

export default SidebarFieldsGroup;
