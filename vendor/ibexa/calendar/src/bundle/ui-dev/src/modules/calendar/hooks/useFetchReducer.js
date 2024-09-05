import { useReducer } from 'react';

const fetchInitialState = {
    dataFetched: false,
    data: {},
};

export const START_FETCHING = 'START_FETCHING';
export const END_FETCHING = 'END_FETCHING';

const fetchReducer = (state, action) => {
    switch (action.type) {
        case START_FETCHING:
            return fetchInitialState;
        case END_FETCHING:
            return { data: action.data, dataFetched: true };
        default:
            throw new Error();
    }
};

export const useFetchReducer = () => {
    const [fetchState, dispatch] = useReducer(fetchReducer, fetchInitialState);

    return [fetchState, dispatch];
};
