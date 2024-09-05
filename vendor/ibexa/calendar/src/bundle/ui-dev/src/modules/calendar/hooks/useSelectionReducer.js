import { useReducer, useEffect, useCallback } from 'react';

const eventsSelectionInitialState = {
    eventsType: null,
    selectedEvents: {},
    selectionToBeConfirmed: null,
};
const eventsSelectionReducer = (state, action) => {
    switch (action.type) {
        case 'SELECT_EVENT':
            return selectEvent(state, action);
        case 'DESELECT_EVENT':
            return deselectEvent(state, action);
        case 'DESELECT_ALL_EVENTS':
            return deselectAllEvents(state, action);
        case 'CONFIRM_SELECTION':
            return confirmSelection(state, action);
        case 'REJECT_SELECTION':
            return rejectSelection(state, action);
        case 'CLEAR_SELECTION':
            return eventsSelectionInitialState;
        default:
            throw new Error();
    }
};
const selectEvent = (state, action) => {
    const { event } = action;
    const isDifferentType = state.eventsType !== null && event.type !== state.eventsType;

    if (isDifferentType) {
        return {
            ...state,
            selectionToBeConfirmed: event,
        };
    }

    return {
        ...state,
        eventsType: event.type,
        selectedEvents: {
            ...state.selectedEvents,
            [event.id]: event,
        },
    };
};
const deselectEvent = (state, action) => {
    const newSelectedEvents = { ...state.selectedEvents };

    delete newSelectedEvents[action.eventId];

    return {
        ...state,
        eventsType: Object.keys(newSelectedEvents).length > 0 ? state.eventsType : null,
        selectedEvents: newSelectedEvents,
    };
};
const deselectAllEvents = (state) => {
    return {
        ...state,
        eventsType: null,
        selectedEvents: {},
    };
};
const confirmSelection = (state) => {
    const event = state.selectionToBeConfirmed;

    if (!event) {
        return state;
    }

    return {
        ...state,
        eventsType: event.type,
        selectedEvents: {
            [event.id]: event,
        },
        selectionToBeConfirmed: null,
    };
};
const rejectSelection = (state) => {
    return {
        ...state,
        selectionToBeConfirmed: null,
    };
};

export const useSelectionReducer = () => {
    const [eventsSelection, dispatchSelectEventAction] = useReducer(eventsSelectionReducer, eventsSelectionInitialState);
    const clearSelection = useCallback(() => {
        dispatchSelectEventAction({ type: 'CLEAR_SELECTION' });
    }, [dispatchSelectEventAction]);

    useEffect(() => {
        window.document.body.addEventListener('ibexa-calendar-clear-selection', clearSelection);

        return () => {
            window.document.body.removeEventListener('ibexa-calendar-clear-selection', clearSelection);
        };
    }, [clearSelection]);

    return [eventsSelection, dispatchSelectEventAction];
};
