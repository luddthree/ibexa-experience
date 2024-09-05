const { convertDateToTimezone } = window.ibexa.helpers.timezone;

export const getNextMonthDate = (currentDate) => {
    const nextMonthDate = currentDate.clone().add(1, 'month').startOf('month');

    return nextMonthDate;
};

export const getNextWeekDate = (currentDate) => {
    const nextWeekDate = currentDate.clone().add(1, 'week').startOf('week');

    return nextWeekDate;
};

export const getNextDayDate = (currentDate) => {
    const nextDayDate = currentDate.clone().add(1, 'day');

    return nextDayDate;
};

/**
 * Returns first date from previous month, which is in present time.
 * If all month's days are in the past null is returned.
 *
 * @param {Object} currentDate
 */
export const getPreviousMonthDate = (currentDate) => {
    const previousMonthDate = currentDate.clone().subtract(1, 'month').startOf('month');
    const today = convertDateToTimezone(new Date()).startOf('day');

    if (previousMonthDate.isBefore(today, 'day')) {
        return today.isSame(previousMonthDate, 'month') ? today : null;
    }

    return previousMonthDate;
};

/**
 * Returns first date from previous week, which is in present time.
 * If all week's days are in the past null is returned.
 *
 * @param {Object} currentDate
 */
export const getPreviousWeekDate = (currentDate) => {
    const previousWeekDate = currentDate.clone().subtract(1, 'week').startOf('week');
    const today = convertDateToTimezone(new Date()).startOf('day');

    if (previousWeekDate.isBefore(today, 'day')) {
        return today.isSame(previousWeekDate, 'week') ? today : null;
    }

    return previousWeekDate;
};

export const getPreviousDayDate = (currentDate) => {
    const previousDayDate = currentDate.clone().subtract(1, 'day');
    const today = convertDateToTimezone(new Date()).startOf('day');

    if (previousDayDate.isBefore(today, 'day')) {
        return null;
    }

    return previousDayDate;
};

export function getMonthDays(date) {
    const monthFirstDay = date.clone().startOf('month');
    const monthLastDay = date.clone().endOf('month');
    const countOfDaysBefore = monthFirstDay.weekday();
    const daysCount = monthLastDay.daysInMonth();
    const countOfDaysAfter = 7 * 6 - countOfDaysBefore - daysCount;
    const daysBefore = [];
    const days = [];
    const daysAfter = [];

    for (let i = countOfDaysBefore; i > 0; i--) {
        daysBefore.push(monthFirstDay.clone().add(-i, 'day').startOf('day'));
    }

    for (let i = 0; i < daysCount; i++) {
        days.push(monthFirstDay.clone().add(i, 'day').startOf('day'));
    }

    for (let i = 0; i < countOfDaysAfter; i++) {
        daysAfter.push(
            monthLastDay
                .clone()
                .add(i + 1, 'day')
                .startOf('day'),
        );
    }

    return { daysBefore, days, daysAfter };
}

export const getWeekDays = (date) => {
    const weekFirstDay = date.clone().startOf('week');
    const weekDays = [];

    for (let i = 0; i < 7; i++) {
        weekDays.push(weekFirstDay.clone().add(i, 'day'));
    }

    return weekDays;
};
