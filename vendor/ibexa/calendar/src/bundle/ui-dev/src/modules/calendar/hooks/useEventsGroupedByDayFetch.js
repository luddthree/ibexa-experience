import { loadEventsGroupedByDay } from '../services/calendar.service';
import { useEventsFetch } from './useEventsFetch';

export const useEventsGroupedByDayFetch = (startDate, endDate) => {
    return useEventsFetch(startDate, endDate, loadEventsGroupedByDay, createEventsMap);
};

const createEventsMap = ({ groups }) => {
    const groupedEventsMap = {};

    Object.values(groups).forEach((dayEventsGroup) => {
        groupedEventsMap[dayEventsGroup.DateRange.startDate] = {
            events: dayEventsGroup.events,
            totalCount: dayEventsGroup.totalCount,
        };
    });

    return groupedEventsMap;
};
