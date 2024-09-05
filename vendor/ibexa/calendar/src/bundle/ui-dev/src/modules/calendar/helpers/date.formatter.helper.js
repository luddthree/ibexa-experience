import { getWeekDays } from './date.helper';

const { convertDateToTimezone } = window.ibexa.helpers.timezone;

export const forceBrowserTimezone = (date) => {
    const browserTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

    return convertDateToTimezone(date, browserTimezone, true);
};

export const getAdminPanelLanguageCode = () => document.querySelector('html').getAttribute('lang').replace(new RegExp('_', 'g'), '-');

export const getMonthName = (dateWithUserTimezone) => {
    const browserTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    const dateWithForcedBrowserTimezone = new Date(convertDateToTimezone(dateWithUserTimezone, browserTimezone, true));

    return dateWithForcedBrowserTimezone.toLocaleDateString(getAdminPanelLanguageCode(), { month: 'long' });
};

export const getMonthShortName = (dateWithUserTimezone) => {
    const browserTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    const dateWithForcedBrowserTimezone = new Date(convertDateToTimezone(dateWithUserTimezone, browserTimezone, true));

    return dateWithForcedBrowserTimezone.toLocaleDateString(getAdminPanelLanguageCode(), { month: 'short' });
};

let weekdaysShortNames = null;

export const getWeekdaysShortNames = () => {
    if (!weekdaysShortNames) {
        const weekdays = getWeekDays(convertDateToTimezone());
        const languageCode = getAdminPanelLanguageCode();

        weekdaysShortNames = weekdays.map((day) => day.toDate().toLocaleDateString(languageCode, { weekday: 'short' }));
    }

    return weekdaysShortNames;
};

let weekdaysLongNames = null;

export const getWeekdaysLongNames = () => {
    if (!weekdaysLongNames) {
        const weekdays = getWeekDays(convertDateToTimezone());
        const languageCode = getAdminPanelLanguageCode();

        weekdaysLongNames = weekdays.map((day) => day.toDate().toLocaleDateString(languageCode, { weekday: 'long' }));
    }

    return weekdaysLongNames;
};

export const getAmPmTimeLabel = (date) =>
    date.toLocaleTimeString(getAdminPanelLanguageCode(), { hour: 'numeric', hour12: true, minute: 'numeric' });

export const getTimeLabel = (date) =>
    date.toLocaleTimeString(getAdminPanelLanguageCode(), { hour: 'numeric', hour12: false, minute: 'numeric' });
