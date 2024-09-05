const { convertDateToTimezone } = window.ibexa.helpers.timezone;

export const groupEventsBy = (data, groupBy) =>
    data.events.reduce((groupedEvents, event) => {
        const eventDate = convertDateToTimezone(parseInt(event.dateTime, 10) * 1000);
        const eventTimestamp = eventDate.startOf(groupBy).unix();
        const group = groupedEvents[eventTimestamp] ? groupedEvents[eventTimestamp] : [];

        group.push(event);

        groupedEvents[eventTimestamp] = group;

        return groupedEvents;
    }, {});
