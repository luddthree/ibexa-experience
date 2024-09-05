import { useReducer } from 'react';

import { isItemStored } from '../helpers/item';
import { removeDuplicates } from '../helpers/array';

const initialState = [];

export const STORED_ITEMS_SET = 'STORED_ITEMS_SET';
export const STORED_ITEMS_ADD = 'STORED_ITEMS_ADD';
export const STORED_ITEMS_REMOVE = 'STORED_ITEMS_REMOVE';
export const STORED_ITEMS_TOGGLE = 'STORED_ITEMS_TOGGLE';
export const STORED_ITEMS_REPLACE = 'STORED_ITEMS_REPLACE';
export const STORED_ITEMS_CLEAR = 'STORED_ITEMS_CLEAR';

const storedItemsReducer = (state, action) => {
    switch (action.type) {
        case STORED_ITEMS_SET: {
            const { items } = action;
            const nextState = removeDuplicates(items);

            return nextState;
        }
        case STORED_ITEMS_ADD: {
            const { items } = action;
            const itemsToAdd = items.filter((item) => !isItemStored(item, state));
            const nextState = [...state, ...itemsToAdd];

            return nextState;
        }
        case STORED_ITEMS_REMOVE: {
            const { items } = action;
            const nextState = state.filter((item) => !isItemStored(item, items));

            return nextState;
        }
        case STORED_ITEMS_REPLACE: {
            const { items } = action;
            const nextState = [...state];
            let isModified = false;

            items.forEach((item) => {
                const stateItemIndex = nextState.findIndex((stateItem) => stateItem.id === item.id);

                if (stateItemIndex >= 0) {
                    nextState[stateItemIndex] = item;
                    isModified = true;
                }
            });

            return isModified ? nextState : state;
        }
        case STORED_ITEMS_TOGGLE: {
            const { items } = action;
            const oldItems = state.filter((item) => !isItemStored(item, items));
            const newItems = items.filter((item) => !isItemStored(item, state));
            const nextState = [...oldItems, ...newItems];

            return nextState;
        }
        case STORED_ITEMS_CLEAR: {
            return [];
        }
        default:
            throw new Error();
    }
};

export default (state = initialState) => {
    const [storedItems, dispatchStoredItemsAction] = useReducer(storedItemsReducer, state);

    return [storedItems, dispatchStoredItemsAction];
};
