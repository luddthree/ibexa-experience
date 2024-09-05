import { useReducer } from 'react';

export const FILTERS_ACTION_FILTER_SET = 'FILTERS_ACTION_FILTER_SET';
export const FILTERS_ACTION_FILTERS_CLEAR = 'FILTERS_ACTION_FILTERS_CLEAR';
export const FILTERS_ACTION_FILTER_ERROR_TOGGLE = 'FILTERS_ACTION_FILTER_ERROR_TOGGLE';

export const FILTERS_DATA_SEARCH_TEXT = 'FILTERS_DATA_SEARCH_TEXT';
export const FILTERS_DATA_LANGUAGES = 'FILTERS_DATA_LANGUAGES';
export const FILTERS_DATA_MIME_TYPES = 'FILTERS_DATA_MIME_TYPES';
export const FILTERS_DATA_SIZE_MIN = 'FILTERS_DATA_SIZE_MIN';
export const FILTERS_DATA_SIZE_MAX = 'FILTERS_DATA_SIZE_MAX';
export const FILTERS_DATA_ORIENTATIONS = 'FILTERS_DATA_ORIENTATIONS';
export const FILTERS_DATA_WIDTH_MIN = 'FILTERS_DATA_WIDTH_MIN';
export const FILTERS_DATA_WIDTH_MAX = 'FILTERS_DATA_WIDTH_MAX';
export const FILTERS_DATA_HEIGHT_MIN = 'FILTERS_DATA_HEIGHT_MIN';
export const FILTERS_DATA_HEIGHT_MAX = 'FILTERS_DATA_HEIGHT_MAX';
export const FILTERS_DATA_TAGS = 'FILTERS_DATA_TAGS';
export const FILTERS_DATA_CREATED_FROM = 'FILTERS_DATA_CREATED_FROM';
export const FILTERS_DATA_CREATED_TO = 'FILTERS_DATA_CREATED_TO';

const initialState = {
    data: {
        [FILTERS_DATA_SEARCH_TEXT]: '',
    },
    errors: [],
};

const filtersReducer = (state, action) => {
    switch (action.type) {
        case FILTERS_ACTION_FILTER_SET: {
            const { updateFunction, value, filterName } = action;

            return {
                ...state,
                data: {
                    ...state.data,
                    [filterName]: updateFunction instanceof Function ? updateFunction(state.data[filterName]) : value,
                },
            };
        }
        case FILTERS_ACTION_FILTERS_CLEAR:
            return {
                ...initialState,
                data: {
                    ...initialState.data,
                    [FILTERS_DATA_SEARCH_TEXT]: state.data[FILTERS_DATA_SEARCH_TEXT],
                },
            };
        case FILTERS_ACTION_FILTER_ERROR_TOGGLE: {
            const { errorName: currentErrorName, isPresent } = action;

            return {
                ...state,
                errors: isPresent
                    ? [...state.errors, currentErrorName]
                    : state.errors.filter((errorName) => errorName !== currentErrorName),
            };
        }
        default:
            throw new Error('useFilters: no such action type');
    }
};

export const useFilters = () => {
    const [{ data, errors }, dispatchFiltersAction] = useReducer(filtersReducer, initialState);

    return { filters: data, filtersErrors: errors, dispatchFiltersAction };
};
