import React, { useContext } from 'react';
import PropTypes from 'prop-types';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

import { CALENDAR_VIEWS, CurrentCalendarViewContext } from '../calendar-view/calendar.view';
import { SelectedDateContext } from '../../../calendar.module';
import EventsView from '../events-view/events.view';

const { ibexa } = window;
const { convertDateToTimezone } = ibexa.helpers.timezone;

const MonthDayView = ({ date, isDisabled, eventsData }) => {
    const [selectedDate, setSelectedDate] = useContext(SelectedDateContext);
    const [, setCurrentCalendarView] = useContext(CurrentCalendarViewContext);
    const today = convertDateToTimezone().startOf('day');
    const isSelectedDay = date.isSame(selectedDate, 'day');
    const isToday = date.isSame(today, 'day');
    const isPast = date.isBefore(convertDateToTimezone(), 'day');
    const isSelectable = !isPast;
    const events = eventsData ? eventsData.events : [];
    const eventsTotalCount = eventsData ? eventsData.totalCount : null;
    const className = createCssClassNames({
        'c-month-day-view': true,
        'c-month-day-view--past': isPast,
        'c-month-day-view--today': isToday,
        'c-month-day-view--selected': isSelectedDay,
    });
    const dayClassName = createCssClassNames({
        'c-month-day-view__day': true,
        'c-month-day-view__day--disabled': isDisabled,
        'c-month-day-view__day--past': isPast,
        'c-month-day-view__day--today': isToday,
        'c-month-day-view__day--selectable': !isPast,
    });
    const handleClick = () => {
        if (isSelectable) {
            setSelectedDate(date);
            setCurrentCalendarView(CALENDAR_VIEWS.DAY);
        }
    };
    const dayOfMonth = date.format('D');
    const label = dayOfMonth === '1' ? date.format('D MMM') : dayOfMonth;

    return (
        <div className={className}>
            <div className={dayClassName} onClick={handleClick}>
                {label}
            </div>
            <EventsView date={date} events={events} slotsCount={3} eventsTotalCount={eventsTotalCount} />
        </div>
    );
};

MonthDayView.propTypes = {
    date: PropTypes.object.isRequired,
    isDisabled: PropTypes.bool,
    eventsData: PropTypes.object,
};

MonthDayView.defaultProps = {
    isDisabled: false,
    eventsData: null,
};

export default MonthDayView;
