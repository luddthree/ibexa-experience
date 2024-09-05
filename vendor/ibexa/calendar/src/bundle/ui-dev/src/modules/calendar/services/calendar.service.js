import { handleRequestResponse } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/request.helper.js';

const { Translator } = window;

const ENDPOINT_EVENTS = '/api/ibexa/v2/calendar/event';
const ENDPOINT_EVENTS_GROUPED_BY_DAY = '/api/ibexa/v2/calendar/event/grouped-by-day';

const getUrlFilters = (calendarFilters) => {
    const filters = [];
    const selectedEventTypes = Object.entries(calendarFilters.eventTypes)
        .filter(([, isEventTypeSelected]) => isEventTypeSelected)
        .map(([eventType]) => eventType);

    if (selectedEventTypes.length) {
        filters.push(`types=${selectedEventTypes.join(',')}`);
    }

    if (calendarFilters.language) {
        filters.push(`languages=${calendarFilters.language}`);
    }

    return filters.join('&');
};

export const loadEventsUngrouped = (restInfo, timestampStart, timestampEnd, calendarFilters, page, callback) => {
    const urlFilters = getUrlFilters(calendarFilters);
    const url = page ? page : `${ENDPOINT_EVENTS}?start=${timestampStart}&end=${timestampEnd}&${urlFilters}`;

    loadEvents(restInfo, url, (response) => {
        callback(response.EventList);
    });
};

export const loadEventsGroupedByDay = (restInfo, timestampStart, timestampEnd, calendarFilters, page, callback) => {
    const urlFilters = getUrlFilters(calendarFilters);
    const url = page ? page : `${ENDPOINT_EVENTS_GROUPED_BY_DAY}?start=${timestampStart}&end=${timestampEnd}&${urlFilters}`;

    loadEvents(restInfo, url, (response) => {
        callback(response.EventGroupList);
    });
};

export const loadEvents = ({ siteaccess }, url, callback) => {
    const request = new Request(url, {
        method: 'GET',
        headers: {
            Accept: 'application/json',
            'X-Siteaccess': siteaccess,
        },
        mode: 'same-origin',
        credentials: 'same-origin',
    });
    const errorMessage = Translator.trans(
        /*@Desc("Cannot load calendar data")*/ 'calendar.load_events.error.message',
        {},
        'ibexa_calendar_widget',
    );

    fetch(request)
        .then(handleRequestResponse)
        .then(callback)
        .catch(() => window.ibexa.helpers.notification.showErrorNotification(errorMessage));
};
