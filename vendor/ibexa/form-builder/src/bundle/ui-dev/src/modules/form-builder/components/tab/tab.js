import React from 'react';
import PropTypes from 'prop-types';

import Collapsible from 'react-collapsible';

const renderHeader = (title) => {
    return (
        <header className="c-tab__header">
            <h3 className="c-tab__title">{title}</h3>
            <svg className="c-tab__icon c-tab__icon--opened ibexa-icon ibexa-icon--small">
                <use xlinkHref={window.ibexa.helpers.icon.getIconPath('caret-down')} />
            </svg>
            <svg className="c-tab__icon c-tab__icon--closed ibexa-icon ibexa-icon--small">
                <use xlinkHref={window.ibexa.helpers.icon.getIconPath('caret-up')} />
            </svg>
        </header>
    );
};
const Tab = ({ title, isOpened, children }) => {
    const attrs = {
        classParentString: 'Collapsible c-tab',
        trigger: renderHeader(title),
        open: isOpened,
        easing: 'cubic-bezier(0.25, 0.8, 0.25, 1)',
        transitionTime: 300,
        openedClassName: 'Collapsible c-tab--opened',
    };

    return <Collapsible {...attrs}>{children}</Collapsible>;
};

Tab.propTypes = {
    title: PropTypes.string.isRequired,
    isOpened: PropTypes.bool,
    children: PropTypes.node,
};

Tab.defaultProps = {
    isOpened: false,
    children: null,
};

export default Tab;
