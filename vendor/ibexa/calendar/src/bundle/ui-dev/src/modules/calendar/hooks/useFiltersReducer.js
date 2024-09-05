import { useReducer, useMemo } from 'react';

const getFiltersInitialState = (eventsConfig) => {
    const eventTypes = Object.keys(eventsConfig).reduce((output, eventType) => {
        output[eventType] = true;

        return output;
    }, {});

    return {
        eventTypes,
        language: '',
    };
};

export const TOGGLE_EVENT_TYPE = 'TOGGLE_EVENT_TYPE';
export const SELECT_LANGUAGE = 'SELECT_LANGUAGE';

const filtersReducer = (state, action) => {
    switch (action.type) {
        case TOGGLE_EVENT_TYPE: {
            const isEventTypeSelected = !state.eventTypes[action.eventType];

            return {
                ...state,
                eventTypes: {
                    ...state.eventTypes,
                    [action.eventType]: isEventTypeSelected,
                },
            };
        }
        case SELECT_LANGUAGE:
            return {
                ...state,
                language: action.language,
            };
        default:
            throw new Error();
    }
};

export const useFiltersReducer = (eventsConfig) => {
    const filtersInitialState = useMemo(() => getFiltersInitialState(eventsConfig), [eventsConfig]);
    const [filters, dispatch] = useReducer(filtersReducer, filtersInitialState);

    return [filters, dispatch];
};
