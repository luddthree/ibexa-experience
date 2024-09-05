import { useEffect, useContext, useState } from 'react';

import { useFetchReducer, END_FETCHING, START_FETCHING } from './useFetchReducer';
import { RestInfoContext, CalendarFiltersContext } from '../../calendar.module';

const { convertDateToTimezone } = window.ibexa.helpers.timezone;

export const useEventsFetch = (startDate, endDate, loadEvents, processEvents, page = null) => {
    const [eventsState, dispatch] = useFetchReducer();
    const [reloadCounter, setReloadCounter] = useState(0);
    const restInfo = useContext(RestInfoContext);
    const [calendarFilters] = useContext(CalendarFiltersContext);
    const now = convertDateToTimezone();
    const fromDate = startDate.isBefore(now, 'day') ? now.startOf('day') : startDate;
    const fromTimestamp = fromDate.unix();
    const toTimestamp = endDate.unix();

    useEffect(() => {
        const forceReload = () => setReloadCounter(reloadCounter + 1);

        window.document.body.addEventListener('ibexa-calendar-reload-data', forceReload);

        return () => {
            window.document.body.removeEventListener('ibexa-calendar-reload-data', forceReload);
        };
    }, [setReloadCounter, reloadCounter]);

    useEffect(() => {
        let effectCleaned = false;

        dispatch({ type: START_FETCHING });
        loadEvents(restInfo, fromTimestamp, toTimestamp, calendarFilters, page, (response) => {
            if (effectCleaned) {
                return;
            }

            const processedEvents = processEvents(response);

            dispatch({ type: END_FETCHING, data: processedEvents });
        });

        return () => {
            effectCleaned = true;
        };
    }, [fromTimestamp, toTimestamp, loadEvents, processEvents, reloadCounter, calendarFilters, page]);

    return [eventsState.data, !eventsState.dataFetched];
};
