import { useEffect, useContext, useReducer, useCallback } from 'react';

import { RestInfoContext, CalendarFiltersContext } from '../../calendar.module';
import { loadEventsUngrouped } from '../services/calendar.service';
import { groupEventsBy } from '../helpers/group.events.by';

const { convertDateToTimezone } = window.ibexa.helpers.timezone;

const loadMoreEventsInitialState = {
    isLoading: true,
    data: null,
    nextPage: null,
    remainingEventsCount: null,
    reloadCounter: 0,
};

const INITIAL_LOAD_START = 'INITIAL_LOAD_START';
const INITIAL_LOAD_END = 'INITIAL_LOAD_END';
const LOAD_MORE_START = 'LOAD_MORE_START';
const LOAD_MORE_END = 'LOAD_MORE_END';
const RELOAD = 'RELOAD';

const loadMoreEventsReducer = (state, action) => {
    switch (action.type) {
        case INITIAL_LOAD_START:
            return startInitialLoad();
        case INITIAL_LOAD_END:
            return endInitialLoad(state, action);
        case LOAD_MORE_START:
            return startLoadMore(state);
        case LOAD_MORE_END:
            return endLoadMore(state, action);
        case RELOAD:
            return reload(state);
        default:
            throw new Error();
    }
};

const startInitialLoad = () => {
    return loadMoreEventsInitialState;
};

const endInitialLoad = (state, action) => {
    const { groupedEvents: loadedEvents, loadedEventsCount, nextPage, totalCount } = action.data;

    return { ...state, isLoading: false, data: loadedEvents, nextPage, remainingEventsCount: totalCount - loadedEventsCount };
};

const startLoadMore = (state) => {
    return { ...state, isLoading: true };
};

const endLoadMore = (state, action) => {
    const { groupedEvents: loadedEvents, loadedEventsCount, nextPage, totalCount } = action.data;
    const events = Object.entries(loadedEvents).reduce(
        (allEvents, [timestamp, eventsData]) => {
            if (allEvents[timestamp]) {
                allEvents[timestamp] = [...allEvents[timestamp], ...eventsData];
            } else {
                allEvents[timestamp] = eventsData;
            }

            return allEvents;
        },
        { ...state.data },
    );

    return { ...state, isLoading: false, data: events, nextPage, remainingEventsCount: totalCount - loadedEventsCount };
};

const reload = (state) => {
    return { ...state, reloadCounter: state.reloadCounter + 1 };
};

export const useEventsLoadMore = (startDate, endDate, groupBy) => {
    const [{ isLoading, data, nextPage, remainingEventsCount, reloadCounter }, dispatch] = useReducer(
        loadMoreEventsReducer,
        loadMoreEventsInitialState,
    );
    const restInfo = useContext(RestInfoContext);
    const [calendarFilters] = useContext(CalendarFiltersContext);
    const now = convertDateToTimezone();
    const fromDate = startDate.isBefore(now, 'day') ? now.startOf('day') : startDate;
    const fromTimestamp = fromDate.unix();
    const toTimestamp = endDate.unix();
    const processEvents = useCallback(
        (eventsData) => {
            const groupedEvents = groupEventsBy(eventsData, groupBy);

            return {
                groupedEvents,
                loadedEventsCount: eventsData.events.length,
                totalCount: eventsData.totalCount,
                nextPage: eventsData._nextPage,
            };
        },
        [groupBy],
    );
    const loadMore = useCallback(() => {
        if (!isLoading && remainingEventsCount <= 0) {
            return;
        }

        dispatch({ type: LOAD_MORE_START });
        loadEventsUngrouped(restInfo, fromTimestamp, toTimestamp, calendarFilters, nextPage, (response) => {
            const processedEvents = processEvents(response);

            dispatch({ type: LOAD_MORE_END, data: processedEvents });
        });
    }, [fromTimestamp, toTimestamp, loadEventsUngrouped, processEvents, calendarFilters, nextPage, isLoading]);

    useEffect(() => {
        const forceReload = () => dispatch({ type: RELOAD });

        window.document.body.addEventListener('ibexa-calendar-reload-data', forceReload);

        return () => {
            window.document.body.removeEventListener('ibexa-calendar-reload-data', forceReload);
        };
    }, [dispatch]);

    useEffect(() => {
        let effectCleaned = false;

        dispatch({ type: INITIAL_LOAD_START });
        loadEventsUngrouped(restInfo, fromTimestamp, toTimestamp, calendarFilters, null, (response) => {
            if (effectCleaned) {
                return;
            }

            const processedEvents = processEvents(response);

            dispatch({ type: INITIAL_LOAD_END, data: processedEvents });
        });

        return () => {
            effectCleaned = true;
        };
    }, [dispatch, restInfo, fromTimestamp, toTimestamp, calendarFilters, loadEventsUngrouped, processEvents, reloadCounter]);

    return [data, isLoading, remainingEventsCount, loadMore];
};
