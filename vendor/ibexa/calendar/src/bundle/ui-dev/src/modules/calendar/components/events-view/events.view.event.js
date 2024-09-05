import React, { useContext } from 'react';
import PropTypes from 'prop-types';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

import { EventsSelectionContext, EventsConfigContext } from '../../../calendar.module';
import EventTooltip from '../event-tooltip/event.tooltip';

const TRANSPARENT_VALUE = '7f';

const EventsViewEvent = ({ event }) => {
    const [eventsSelection, dispatchSelectEventAction] = useContext(EventsSelectionContext);
    const eventsConfig = useContext(EventsConfigContext);
    const eventConfig = eventsConfig[event.type];
    const { isSelectable } = eventConfig;
    const isSelected = !!eventsSelection.selectedEvents[event.id];
    const labelClassName = createCssClassNames({
        'c-events-view-event__label': true,
        [`c-events-view-event__label--${event.type}`]: true,
        'c-events-view-event__label--selected': isSelected,
        'c-events-view-event__label--not-selectable': !isSelectable,
    });
    const style = {
        background: isSelected ? eventConfig.color : `${eventConfig.color}${TRANSPARENT_VALUE}`,
    };
    const handleClick = () => {
        if (!isSelectable) {
            return;
        }

        if (isSelected) {
            dispatchSelectEventAction({ type: 'DESELECT_EVENT', eventId: event.id });
        } else {
            dispatchSelectEventAction({ type: 'SELECT_EVENT', event });
        }
    };

    return (
        <div key={event.id} className="c-events-view-event">
            <div className={labelClassName} onClick={handleClick} style={style}>
                <div className="c-events-view-event__name">{event.name}</div>
            </div>
            <EventTooltip event={event} />
        </div>
    );
};

EventsViewEvent.propTypes = {
    event: PropTypes.object.isRequired,
};

export default EventsViewEvent;
