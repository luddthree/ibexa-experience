import React, { useContext } from 'react';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';
import { getPreviousMonthDate, getNextMonthDate, getMonthDays } from '../../helpers/date.helper';
import { getMonthName, getWeekdaysShortNames } from '../../helpers/date.formatter.helper';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import { SelectedDateContext } from '../../../calendar.module';

const { convertDateToTimezone } = window.ibexa.helpers.timezone;

const SidebarDayPicker = () => {
    const [selectedDate, setSelectedDate] = useContext(SelectedDateContext);
    const { daysBefore, days, daysAfter } = getMonthDays(selectedDate);
    const renderDayBefore = (day) => {
        const className = createCssClassNames({
            'c-day-picker__day': true,
            'c-day-picker__day--disabled': true,
            'c-day-picker__day--past': day.isBefore(convertDateToTimezone(), 'day'),
        });

        return (
            <div className={className} key={day.unix()}>
                {day.format('D')}
            </div>
        );
    };
    const renderMonthDay = (day) => {
        const isSelectedDay = day.isSame(selectedDate, 'day');
        const isToday = day.isSame(today, 'day');
        const isPast = day.isBefore(convertDateToTimezone(), 'day');
        const isSelectable = !isSelectedDay && !isPast;
        const className = createCssClassNames({
            'c-day-picker__day': true,
            'c-day-picker__day--today': isToday,
            'c-day-picker__day--selected': isSelectedDay,
            'c-day-picker__day--past': isPast,
            'c-day-picker__day--selectable': isSelectable,
        });
        const handleClick = isSelectable ? () => setSelectedDate(day) : null;

        return (
            <div className={className} key={day.unix()} onClick={handleClick}>
                {day.format('D')}
            </div>
        );
    };
    const renderDayAfter = (day) => (
        <div className="c-day-picker__day c-day-picker__day--disabled" key={day.unix()}>
            {day.format('D')}
        </div>
    );

    const previousDate = getPreviousMonthDate(selectedDate);
    const nextDate = getNextMonthDate(selectedDate);
    const monthName = getMonthName(selectedDate);
    const yearString = selectedDate.format('YYYY');
    const today = convertDateToTimezone().startOf('day');

    return (
        <div className="c-day-picker">
            <div className="c-day-picker__paginator">
                <button
                    disabled={previousDate === null}
                    onClick={() => setSelectedDate(previousDate)}
                    className="btn ibexa-btn ibexa-btn--ghost ibexa-btn--no-text ibexa-btn--small c-day-picker__paginator-decrease-btn"
                    type="button"
                >
                    <Icon name="caret-back" extraClasses="ibexa-icon--tiny-small" />
                </button>
                <div className="c-day-picker__paginator-date">
                    {monthName} {yearString}
                </div>
                <button
                    className="btn ibexa-btn ibexa-btn--no-text ibexa-btn--small c-day-picker__paginator-increase-btn"
                    onClick={() => setSelectedDate(nextDate)}
                    type="button"
                >
                    <Icon name="caret-next" extraClasses="ibexa-icon--tiny-small" />
                </button>
            </div>
            <div className="c-day-picker__week-days c-day-picker__week-days--month">
                {getWeekdaysShortNames().map((dayName) => (
                    <div key={dayName} className="c-day-picker__day-name">
                        {dayName}
                    </div>
                ))}
            </div>
            <div className="c-day-picker__days">
                {daysBefore.map(renderDayBefore)}
                {days.map(renderMonthDay)}
                {daysAfter.map(renderDayAfter)}
            </div>
        </div>
    );
};

export default SidebarDayPicker;
