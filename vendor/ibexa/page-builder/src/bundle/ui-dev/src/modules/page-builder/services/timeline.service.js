import serialize from '../../helpers/serialize';

/**
 * Gets the timeline events
 *
 * @function getTimelineEvents
 * @param {Object} body request data
 * @returns {Promise} Fetch promise
 */
export const getTimelineEvents = (body) => {
    const url = window.Routing.generate('ibexa.page_builder.timeline.events.get');
    const request = new Request(url, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
        },
        body: serialize(body),
        mode: 'same-origin',
        credentials: 'same-origin',
    });

    return fetch(request);
};
