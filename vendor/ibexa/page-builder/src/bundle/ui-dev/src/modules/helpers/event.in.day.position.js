export const MINUTES_IN_HOUR = 60;
export const MINUTES_IN_DAY = MINUTES_IN_HOUR * 24;
const PERCENTAGE_FACTOR = 100;

const getEventInDayLeftPosition = (timestamp) => {
    const { convertDateToTimezone } = window.ibexa.helpers.timezone;
    const date = convertDateToTimezone(parseInt(timestamp, 10));
    const minutesCount = date.hour() * MINUTES_IN_HOUR + date.minutes();
    const leftPosition = (minutesCount / MINUTES_IN_DAY) * PERCENTAGE_FACTOR;

    return leftPosition;
};

export default getEventInDayLeftPosition;
