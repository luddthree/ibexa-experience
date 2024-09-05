import React, { createContext, useEffect, useImperativeHandle, forwardRef } from 'react';
import PropTypes from 'prop-types';

import useStoredItemsReducer, { STORED_ITEMS_ADD } from '../../hooks/useStoredItemsReducer';
import useLocalStorage from '../../hooks/useLocalStorage';

export const ExpandContext = createContext();

const EXPAND_DATA_STORAGE_ID = 'expanded-nodes';

const LocalStorageExpandConnector = forwardRef(({ children, initiallyExpandedItems, isLocalStorageActive }, ref) => {
    const { getLocalStorageData, saveLocalStorageData } = useLocalStorage(EXPAND_DATA_STORAGE_ID);
    const getInitialValues = () => {
        if (initiallyExpandedItems) {
            return initiallyExpandedItems;
        }

        return (isLocalStorageActive && getLocalStorageData()) || [];
    };
    const initialValues = getInitialValues();
    const [expandedData, dispatchExpandedData] = useStoredItemsReducer(initialValues);
    const expandDataContextValues = {
        expandedData,
        dispatchExpandedData,
    };

    useImperativeHandle(ref, () => ({
        expandItems: (items) => {
            dispatchExpandedData({ items, type: STORED_ITEMS_ADD });
        },
    }));

    useEffect(() => {
        if (isLocalStorageActive) {
            saveLocalStorageData(expandedData, false);
        }
    }, [expandedData]);

    return <ExpandContext.Provider value={expandDataContextValues}>{children}</ExpandContext.Provider>;
});

LocalStorageExpandConnector.propTypes = {
    children: PropTypes.node.isRequired,
    initiallyExpandedItems: PropTypes.func,
    isLocalStorageActive: PropTypes.bool.isRequired,
};

LocalStorageExpandConnector.defaultProps = {
    initiallyExpandedItems: null,
};

LocalStorageExpandConnector.displayName = 'LocalStorageExpandConnector';

export default LocalStorageExpandConnector;
