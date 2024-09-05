import React, { createContext, useState, useRef } from 'react';
import PropTypes from 'prop-types';

export const IntermediateActionContext = createContext();

const IntermediateActionProvider = ({ children }) => {
    const groupingItemId = useRef(null);
    const [intermediateAction, setIntermediateAction] = useState({
        isActive: false,
        listItems: [],
    });
    const clearIntermediateAction = () => {
        groupingItemId.current = null;

        setIntermediateAction({
            isActive: false,
            listItems: [],
        });
    };
    const intermediateActionDataContextValues = {
        intermediateAction,
        setIntermediateAction,
        groupingItemId,
        clearIntermediateAction,
    };

    return <IntermediateActionContext.Provider value={intermediateActionDataContextValues}>{children}</IntermediateActionContext.Provider>;
};

IntermediateActionProvider.propTypes = {
    children: PropTypes.node.isRequired,
};

export default IntermediateActionProvider;
