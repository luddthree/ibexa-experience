import { useCallback } from 'react';

import { loadEventsUngrouped } from '../services/calendar.service';
import { useEventsFetch } from './useEventsFetch';
import { groupEventsBy } from '../helpers/group.events.by';

export const useEventsGroupedByFetch = (startDate, endDate, groupBy, nextPage = null) => {
    const processEvents = useCallback(
        (data) => {
            const groupedEvents = groupEventsBy(data, groupBy);

            return { events: groupedEvents, totalCount: data.totalCount, nextPage: data._nextPage };
        },
        [groupBy],
    );

    return useEventsFetch(startDate, endDate, loadEventsUngrouped, processEvents, nextPage);
};
