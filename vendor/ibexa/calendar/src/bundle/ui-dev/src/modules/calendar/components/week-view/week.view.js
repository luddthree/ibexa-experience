import React, { useMemo, useContext } from 'react';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

import { getWeekDays } from '../../helpers/date.helper';
import { CALENDAR_VIEWS, CurrentCalendarViewContext } from '../calendar-view/calendar.view';
import { SelectedDateContext } from '../../../calendar.module';
import { useEventsGroupedByFetch } from '../../hooks/useEventsGroupedByFetch';
import LoadingSpinner from '../spinner/spinner';
import WeekHourView from './week.hour.view';
import { getAdminPanelLanguageCode, getAmPmTimeLabel, forceBrowserTimezone } from '../../helpers/date.formatter.helper';

const { convertDateToTimezone } = window.ibexa.helpers.timezone;

const WeekView = () => {
    const [selectedDate, setSelectedDate] = useContext(SelectedDateContext);
    const [, setCurrentCalendarView] = useContext(CurrentCalendarViewContext);
    const today = convertDateToTimezone().startOf('day');
    const now = convertDateToTimezone();
    const days = useMemo(() => getWeekDays(selectedDate), [selectedDate]);
    const [fromDate] = days;
    const toDate = days[days.length - 1].clone().endOf('day');
    const [{ events }, isLoading] = useEventsGroupedByFetch(fromDate, toDate, 'hour');
    const weekHours = [];
    const languageCode = getAdminPanelLanguageCode();

    for (let i = 0; i < 24; i++) {
        const hours = [];

        days.forEach((day) => {
            hours.push(day.clone().add(i, 'hour'));
        });

        weekHours.push(hours);
    }

    return (
        <div className="c-week-view">
            <LoadingSpinner isLoading={isLoading} />
            <div className="c-week-view__days-names">
                <div className="c-week-view__day-time c-week-view__day-time--no-border" />
                {days.map((day) => {
                    const dayName = day.toDate().toLocaleDateString(languageCode, { weekday: 'short' });
                    const isToday = day.isSame(today, 'day');
                    const isSelectedDay = day.isSame(selectedDate, 'day');
                    const dayNameClassName = createCssClassNames({
                        'c-week-view__day-name': true,
                        'c-week-view__day-name--today': isToday,
                        'c-week-view__day-name--selected': isSelectedDay,
                    });

                    return (
                        <div key={dayName} className={dayNameClassName}>
                            {dayName}
                        </div>
                    );
                })}
            </div>
            <div className="c-week-view__days-dates">
                <div className="c-week-view__day-time c-week-view__day-time--no-border" />
                {days.map((date) => {
                    const isSelectedDay = date.isSame(selectedDate, 'day');
                    const isPast = date.isBefore(now, 'day');
                    const isToday = date.isSame(today, 'day');
                    const isSelectable = !isSelectedDay && !isPast;
                    const wrapperClassName = createCssClassNames({
                        'c-week-view__day-date-wrapper': true,
                        'c-week-view__day-date-wrapper--today': isToday,
                        'c-week-view__day-date-wrapper--past': isPast,
                        'c-week-view__day-date-wrapper--selected': isSelectedDay,
                    });
                    const dayDateClassName = createCssClassNames({
                        'c-week-view__day-date': true,
                        'c-week-view__day-date--past': isPast,
                        'c-week-view__day-date--today': isToday,
                    });
                    const handleClick = () => {
                        if (isSelectable) {
                            setSelectedDate(date);
                            setCurrentCalendarView(CALENDAR_VIEWS.DAY);
                        }
                    };

                    return (
                        <div key={date.unix()} className={wrapperClassName}>
                            <span className={dayDateClassName} onClick={handleClick}>
                                {date.format('D')}
                            </span>
                        </div>
                    );
                })}
            </div>
            <div className="c-week-view__days">
                {weekHours.map((rowHours) => {
                    const forceBrowserTimezoneHour = forceBrowserTimezone(rowHours[0]);
                    const hourIn24Format = forceBrowserTimezoneHour.format('HH');
                    const dayTimeLabel = getAmPmTimeLabel(forceBrowserTimezoneHour.toDate());
                    const dayTimeClassName = createCssClassNames({
                        'c-week-view__day-time': true,
                        'c-week-view__day-time--first-pm': hourIn24Format === '12',
                    });

                    return (
                        <div key={rowHours[0].unix()} className="c-week-view__hour-row">
                            <div className={dayTimeClassName}>{dayTimeLabel}</div>

                            {rowHours.map((hour) => {
                                const hourEventsData = isLoading ? [] : events[hour.unix()];

                                return <WeekHourView key={hour.unix()} events={hourEventsData} date={hour} selectedDate={selectedDate} />;
                            })}
                        </div>
                    );
                })}
            </div>
        </div>
    );
};

export default WeekView;
