import React from 'react';
import PropTypes from 'prop-types';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

import { LEFT_PANEL_TYPES } from '../../../common/components/config-panel/config.panel';

const { Translator, ibexa } = window;

const LayoutSwitcher = ({ isSwitchingLayout, isDisabled, hasErrorState, onClick }) => {
    const btnTitle = Translator.trans(/*@Desc("Switch layout")*/ 'switch_layout.label', {}, 'ibexa_page_builder');
    const btnClassName = createCssClassNames({
        'btn ibexa-btn ibexa-btn--selector ibexa-btn--no-text ibexa-pb-action-bar__action-btn': true,
        [`ibexa-pb-action-bar__action-btn--${LEFT_PANEL_TYPES.LAYOUT_SELECTOR}`]: true,
        'ibexa-btn--selected': isSwitchingLayout,
        'ibexa-btn--error': hasErrorState,
        'disabled ': isDisabled,
    });

    return (
        <button className={btnClassName} onClick={onClick} title={btnTitle} type="button" ref={ibexa.helpers.tooltips.parse}>
            <Icon name="layout-switch" extraClasses="ibexa-icon--medium" />
        </button>
    );
};

LayoutSwitcher.propTypes = {
    onClick: PropTypes.func.isRequired,
    isSwitchingLayout: PropTypes.bool.isRequired,
    isDisabled: PropTypes.bool.isRequired,
    hasErrorState: PropTypes.bool.isRequired,
};

export default LayoutSwitcher;
