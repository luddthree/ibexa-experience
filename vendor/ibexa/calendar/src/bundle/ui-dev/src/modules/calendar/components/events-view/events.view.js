import React, { useState, createRef } from 'react';
import PropTypes from 'prop-types';

import { getMonthShortName } from '../../helpers/date.formatter.helper';
import EventsViewEvent from './events.view.event';
import EventsListTooltip from './events.list.tooltip';

const EventsView = ({ date, events, slotsCount, eventsTotalCount }) => {
    const [isTooltipVisible, setIsTooltipVisible] = useState(false);
    const allEventsFit = slotsCount === null || events.length <= slotsCount;
    const eventsToDisplay = allEventsFit ? events : events.slice(0, slotsCount - 1);
    const eventsToDisplayInTooltip = !allEventsFit ? events.slice(slotsCount - 1, events.length) : [];
    const totalCount = eventsTotalCount ? eventsTotalCount : events.length;
    const refEvents = createRef(null);
    const refTooltip = createRef(null);
    const showTooltip = () => {
        setIsTooltipVisible(true);
    };
    const hideTooltip = () => {
        setIsTooltipVisible(false);
    };
    const renderTooltip = () => {
        if (!isTooltipVisible || allEventsFit) {
            return null;
        }

        const title = `${date.format('D')} ${getMonthShortName(date)}`;

        return (
            <EventsListTooltip refEvents={refEvents} ref={refTooltip} closeFunc={hideTooltip} title={title}>
                {eventsToDisplayInTooltip.map((event) => (
                    <EventsViewEvent key={event.id} event={event} refEvents={refTooltip} />
                ))}
            </EventsListTooltip>
        );
    };
    const renderExpandButton = () => {
        if (allEventsFit) {
            return null;
        }

        return (
            <button className="c-events__expand btn ibexa-btn ibexa-btn--secondary" onClick={showTooltip} type="button">
                {`+${totalCount - slotsCount + 1}`}
            </button>
        );
    };

    return (
        <div className="c-events" ref={refEvents}>
            {eventsToDisplay.map((event) => (
                <EventsViewEvent key={event.id} event={event} refEvents={refEvents} />
            ))}
            <div className="c-events-expanded">
                {renderExpandButton()}
                {renderTooltip()}
            </div>
        </div>
    );
};

EventsView.propTypes = {
    date: PropTypes.object.isRequired,
    events: PropTypes.array,
    slotsCount: PropTypes.number,
    eventsTotalCount: PropTypes.number,
};

EventsView.defaultProps = {
    events: [],
    slotsCount: null,
    eventsTotalCount: null,
};

export default EventsView;
