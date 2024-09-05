import React, { useEffect, useState } from 'react';
import PropTypes from 'prop-types';

import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';
import useLocalStorage from '../../hooks/useLocalStorage';

const FiltersGroup = ({ id, title, hasError, children }) => {
    const { getLocalStorageData, saveLocalStorageData } = useLocalStorage(`filters.group.${id}.is_expanded`);
    const [isExpanded, setIsExpanded] = useState(getLocalStorageData() ?? false);
    const className = createCssClassNames({
        'c-ip-filters__collapsible': true,
        'c-ip-filters__collapsible--hidden': !isExpanded,
    });
    const toggleExpanded = () => {
        setIsExpanded((prevState) => !prevState);
    };

    useEffect(() => {
        saveLocalStorageData(isExpanded);
    }, [isExpanded]);

    return (
        <div className={className}>
            <div className="c-ip-filters__collapsible-title" onClick={toggleExpanded}>
                {title}
                {hasError && <Icon name="notice" extraClasses="ibexa-icon--small c-ip-filters__notice-icon" />}
            </div>
            <div className="c-ip-filters__collapsible-content">
                {isExpanded && <div className="c-ip-filters__collapsible-content-wrapper">{children}</div>}
            </div>
        </div>
    );
};

FiltersGroup.propTypes = {
    id: PropTypes.string.isRequired,
    title: PropTypes.node.isRequired,
    children: PropTypes.node.isRequired,
    hasError: PropTypes.bool,
};

FiltersGroup.defaultProps = {
    hasError: false,
};

export default FiltersGroup;
