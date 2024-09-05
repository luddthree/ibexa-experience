import { useReducer } from 'react';

const initialState = [];

export const DELAYED_CHILDREN_SELECT_ADD = 'DELAYED_CHILDREN_SELECT_ADD';
export const DELAYED_CHILDREN_SELECT_REMOVE = 'DELAYED_CHILDREN_SELECT_REMOVE';

const delayedChildrenSelectReducer = (state, action) => {
    switch (action.type) {
        case DELAYED_CHILDREN_SELECT_ADD: {
            const nextState = [...state];
            const { parentId } = action;

            if (!nextState.includes(parentId)) {
                nextState.push(parentId);
            }

            return nextState;
        }
        case DELAYED_CHILDREN_SELECT_REMOVE: {
            const nextState = [...state];
            const parentIdIndex = nextState.findIndex((parentId) => parentId === action.parentId);

            if (parentIdIndex < 0) {
                return nextState;
            }

            nextState.splice(parentIdIndex, 1);

            return nextState;
        }
        default:
            throw new Error('useDelayedChildrenSelectReducer: no such action');
    }
};

export default (state = initialState) => {
    const [delayedChildrenSelectParentsIds, dispatchDelayedChildrenSelectAction] = useReducer(delayedChildrenSelectReducer, state);

    return [delayedChildrenSelectParentsIds, dispatchDelayedChildrenSelectAction];
};
