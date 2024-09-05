import React from 'react';
import PropTypes from 'prop-types';
import EventMarker, { EVENT_MARKER_INSIDE_CALENDAR } from '../event-marker/event.marker';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

const DayView = ({ event, timestamp, isSelected, isSelectable, day, onChangeSelectedTimestamp }) => {
    const changeSelectedTimestamp = () => onChangeSelectedTimestamp(timestamp);
    const hasEvent = !!event;
    const wrapperClassName = createCssClassNames({
        'c-pb-day-view': true,
        'c-pb-day-view--is-selected': isSelected,
        'c-pb-day-view--is-selectable': isSelectable,
        'c-pb-day-view--has-event': hasEvent,
    });
    const wrapperAttrs = {
        className: wrapperClassName,
    };

    if (isSelectable) {
        wrapperAttrs.onClick = changeSelectedTimestamp;
    }

    return (
        <div {...wrapperAttrs}>
            <span className="c-pb-day-view__label">{day}</span>
            {hasEvent && <EventMarker event={event} inside={EVENT_MARKER_INSIDE_CALENDAR} />}
        </div>
    );
};

DayView.propTypes = {
    day: PropTypes.oneOfType([PropTypes.number.isRequired, PropTypes.string.isRequired]).isRequired,
    timestamp: PropTypes.number.isRequired,
    onChangeSelectedTimestamp: PropTypes.func.isRequired,
    event: PropTypes.object,
    isSelected: PropTypes.bool,
    isSelectable: PropTypes.bool,
};

DayView.defaultProps = {
    event: null,
    isSelectable: true,
    isSelected: false,
};

export default DayView;
