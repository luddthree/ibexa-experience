import React, { useContext, useMemo } from 'react';
import { getMonthDays } from '../../helpers/date.helper';
import { useEventsGroupedByDayFetch } from '../../hooks/useEventsGroupedByDayFetch';
import { SelectedDateContext } from '../../../calendar.module';
import LoadingSpinner from '../spinner/spinner';
import MonthDayView from './month.day.view';
import { getWeekdaysShortNames } from '../../helpers/date.formatter.helper';

const MonthView = () => {
    const [selectedDate] = useContext(SelectedDateContext);
    const { daysBefore, days, daysAfter } = useMemo(() => getMonthDays(selectedDate), [selectedDate]);
    const [fromDate] = days;
    const toDate = daysAfter[daysAfter.length - 1].clone().endOf('day');
    const [groupedEventsMap, isLoading] = useEventsGroupedByDayFetch(fromDate, toDate);

    return (
        <div className="c-month-view">
            <LoadingSpinner isLoading={isLoading} />
            <div className="c-month-view__week-days c-month-view__week-days--month">
                {getWeekdaysShortNames().map((dayName) => (
                    <div key={dayName} className="c-month-view__day-name">
                        {dayName}
                    </div>
                ))}
            </div>
            <div className="c-month-view__days">
                {daysBefore.map((day) => (
                    <MonthDayView key={day.unix()} date={day} isDisabled={true} />
                ))}
                {days.map((day) => {
                    const dayEventsData = isLoading ? {} : groupedEventsMap[day.unix()];

                    return <MonthDayView key={day.unix()} date={day} eventsData={dayEventsData} />;
                })}
                {daysAfter.map((day) => {
                    const dayEventsData = isLoading ? {} : groupedEventsMap[day.unix()];

                    return <MonthDayView key={day.unix()} date={day} eventsData={dayEventsData} isDisabled={true} />;
                })}
            </div>
        </div>
    );
};

export default MonthView;
