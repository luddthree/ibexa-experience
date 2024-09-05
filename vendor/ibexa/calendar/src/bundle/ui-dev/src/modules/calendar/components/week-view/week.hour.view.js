import React from 'react';
import PropTypes from 'prop-types';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

import EventsView from '../events-view/events.view';
import { forceBrowserTimezone } from '../../helpers/date.formatter.helper';

const { Translator, ibexa } = window;
const { convertDateToTimezone } = ibexa.helpers.timezone;

const WeekHourView = ({ date, events, selectedDate }) => {
    const today = convertDateToTimezone().startOf('day');
    const isSelected = date.isSame(selectedDate, 'day');
    const isPast = date.isBefore(convertDateToTimezone(), 'day');
    const isToday = date.isSame(today, 'day');
    const hourIn24Format = forceBrowserTimezone(date).format('HH');
    const timePMsymbol = Translator.trans(/*@Desc("PM")*/ 'calendar.time_pm_symbol', {}, 'ibexa_calendar_widget');
    const className = createCssClassNames({
        'c-week-hour-view': true,
        'c-week-hour-view--past': isPast,
        'c-week-hour-view--today': isToday,
        'c-week-hour-view--selected': isSelected,
        'c-week-hour-view--first-pm': hourIn24Format === '12',
    });

    return (
        <div className={className} data-time-pm-symbol={timePMsymbol}>
            <EventsView events={events} />
        </div>
    );
};

WeekHourView.propTypes = {
    date: PropTypes.object.isRequired,
    events: PropTypes.array,
    selectedDate: PropTypes.object.isRequired,
};

WeekHourView.defaultProps = {
    events: [],
};

export default WeekHourView;
