import React, { useState } from 'react';
import PropTypes from 'prop-types';

import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

const BlocksGroup = ({ title, children }) => {
    const [isCollapsed, setIsCollapsed] = useState(false);
    const blocksGroupClassName = createCssClassNames({
        'c-pb-toolbox-blocks-group': true,
        'c-pb-toolbox-blocks-group--collapsed': isCollapsed,
    });
    const iconName = isCollapsed ? 'caret-down' : 'caret-up';

    return (
        <div className={blocksGroupClassName}>
            <div className="c-pb-toolbox-blocks-group__title-bar" onClick={() => setIsCollapsed((isCollapsedPrev) => !isCollapsedPrev)}>
                <div className="c-pb-toolbox-blocks-group__title">{title}</div>
                <div className="c-pb-toolbox-blocks-group__toggler">
                    <Icon name={iconName} extraClasses="ibexa-icon--tiny-small" />
                </div>
            </div>
            <div className="c-pb-toolbox-blocks-group__blocks">{children}</div>
        </div>
    );
};

BlocksGroup.propTypes = {
    children: PropTypes.node.isRequired,
    title: PropTypes.string,
};

BlocksGroup.defaultProps = {
    title: '',
};

export default BlocksGroup;
