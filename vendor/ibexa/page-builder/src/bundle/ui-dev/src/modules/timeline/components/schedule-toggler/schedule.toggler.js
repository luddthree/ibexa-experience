import React from 'react';
import PropTypes from 'prop-types';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

import { LEFT_PANEL_TYPES } from '../../../common/components/config-panel/config.panel';

const ScheduleToggler = ({ isVisible, toggle }) => {
    const className = createCssClassNames({
        'btn ibexa-btn ibexa-btn--no-text ibexa-btn--selector': true,
        'ibexa-pb-action-bar__action-btn': true,
        [`ibexa-pb-action-bar__action-btn--${LEFT_PANEL_TYPES.SCHEDULER}`]: true,
        'ibexa-btn--selected': isVisible,
    });

    return (
        <button className={className} onClick={toggle} type="button">
            <Icon name="timeline" extraClasses="ibexa-icon--medium" />
        </button>
    );
};

ScheduleToggler.propTypes = {
    isVisible: PropTypes.bool.isRequired,
    toggle: PropTypes.func.isRequired,
};

export default ScheduleToggler;
