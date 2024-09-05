import { useReducer } from 'react';

const initialState = [];
const add = (state, action) => {
    return [...state, { image: action.image, additionalData: action.additionalData }];
};
const removeLastItem = (state) => {
    const newImageHistory = [...state];

    newImageHistory.pop();

    return newImageHistory;
};
const setHistory = (state, action) => {
    return action.history;
};
const clearHistory = () => {
    return [];
};

const imageHistoryReducer = (state, action) => {
    switch (action.type) {
        case 'ADD_TO_HISTORY':
            return add(state, action);
        case 'REMOVE_LAST_ITEM_FROM_HISTORY':
            return removeLastItem(state, action);
        case 'SET_HISTORY':
            return setHistory(state, action);
        case 'CLEAR_HISTORY':
            return clearHistory();
        default:
            throw new Error();
    }
};

export const useImageHistoryReducer = (state = initialState) => {
    const [imageHistory, dispatchImageHistoryAction] = useReducer(imageHistoryReducer, state);

    return [imageHistory, dispatchImageHistoryAction];
};
