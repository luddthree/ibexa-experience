import React from 'react';
import PropTypes from 'prop-types';

import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';

const { ibexa } = window;

const ToolboxTabBtn = ({ iconName, changeActiveTab, tab, tabName, name, title }) => {
    const btnClassName = createCssClassNames({
        'btn ibexa-btn ibexa-btn--no-text ibexa-btn--selector ibexa-pb-action-bar__action-btn ibexa-preview-switcher__action': true,
        'ibexa-btn--selected': tab.isOpened && tab.activeTab === tabName,
    });

    return (
        <button
            data-tab-name={name}
            className={btnClassName}
            onClick={() => changeActiveTab(tabName)}
            type="button"
            title={title}
            ref={ibexa.helpers.tooltips.parse}
        >
            <Icon name={iconName} extraClasses="ibexa-icon--medium" />
        </button>
    );
};

ToolboxTabBtn.propTypes = {
    changeActiveTab: PropTypes.func.isRequired,
    iconName: PropTypes.string.isRequired,
    tab: PropTypes.shape({
        activeTab: PropTypes.string.isRequired,
        isOpened: PropTypes.bool.isRequired,
    }).isRequired,
    tabName: PropTypes.string.isRequired,
    name: PropTypes.string.isRequired,
    title: PropTypes.string.isRequired,
};

export default ToolboxTabBtn;
