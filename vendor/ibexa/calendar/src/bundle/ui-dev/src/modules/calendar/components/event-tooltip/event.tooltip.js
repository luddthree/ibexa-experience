import React from 'react';
import PropTypes from 'prop-types';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

import EventBadge from '../event-badge/event.badge';

const EventTooltip = ({ event }) => {
    const className = createCssClassNames({
        'c-event-tooltip': true,
    });

    return (
        <div className={className}>
            <EventBadge event={event} />
        </div>
    );
};

EventTooltip.propTypes = {
    event: PropTypes.object,
};

EventTooltip.defaultProps = {
    event: {},
};

export default EventTooltip;
