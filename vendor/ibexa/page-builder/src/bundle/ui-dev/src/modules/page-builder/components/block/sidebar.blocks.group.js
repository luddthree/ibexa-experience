import React, { useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

/**
 * @deprecated This component is deprecated and will be removed in 5.0; Use block.group.js
 */

const SidebarBlocksGroup = (props) => {
    const [isCollapsed, setIsCollapsed] = useState(props.isCollapsed);
    const blocksGroupClassName = createCssClassNames({
        'c-pb-sidebar-blocks-group': true,
        'c-pb-sidebar-blocks-group--collapsed': isCollapsed,
    });
    const iconName = isCollapsed ? 'caret-down' : 'caret-up';

    useEffect(() => {
        setIsCollapsed(props.isCollapsed);
    }, [props.isCollapsed]);

    useEffect(() => {
        props.onCollapseChange();
    }, [isCollapsed]);

    return (
        <div className={blocksGroupClassName}>
            <div className="c-pb-sidebar-blocks-group__title-bar" onClick={() => setIsCollapsed((isCollapsedPrev) => !isCollapsedPrev)}>
                <div className="c-pb-sidebar-blocks-group__title">{props.title}</div>
                <div className="c-pb-sidebar-blocks-group__toggler">
                    <Icon name={iconName} extraClasses="ibexa-icon--tiny-small" />
                </div>
            </div>
            <div className="c-pb-sidebar-blocks-group__blocks">{props.children}</div>
        </div>
    );
};

SidebarBlocksGroup.propTypes = {
    children: PropTypes.node.isRequired,
    isCollapsed: PropTypes.bool,
    title: PropTypes.string,
    onCollapseChange: PropTypes.func,
};

SidebarBlocksGroup.defaultProps = {
    isCollapsed: false,
    title: '',
    onCollapseChange: () => {},
};

export default SidebarBlocksGroup;
