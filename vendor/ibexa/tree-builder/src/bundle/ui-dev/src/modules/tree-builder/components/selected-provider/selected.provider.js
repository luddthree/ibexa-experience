import React, { createContext, useContext, useEffect, useRef } from 'react';
import PropTypes from 'prop-types';

import { getRootDOMElement } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';

import { ModuleIdContext } from '../../tree.builder.module';
import useStoredItemsReducer, { STORED_ITEMS_ADD } from '../../hooks/useStoredItemsReducer';

export const SelectedContext = createContext();

const getStateHash = (state) => state.map((item) => item.id).join('_');

const SelectedProvider = ({ children, initiallySelectedItemsIds }) => {
    const rootDOMElement = getRootDOMElement();
    const prevSelectedDataHashRef = useRef(getStateHash([]));
    const prevInitialItemsIds = useRef('');
    const moduleId = useContext(ModuleIdContext);
    const [selectedData, dispatchSelectedData] = useStoredItemsReducer();
    const selectedDataContextValues = {
        selectedData,
        dispatchSelectedData,
    };

    useEffect(() => {
        const initialIds = initiallySelectedItemsIds.join(',');

        if (prevInitialItemsIds.current !== initialIds) {
            prevInitialItemsIds.current = initialIds;

            const initialValues = initiallySelectedItemsIds.map((id) => ({ id }));

            dispatchSelectedData({ items: initialValues, type: STORED_ITEMS_ADD });
        }
    }, [initiallySelectedItemsIds]);

    useEffect(() => {
        const currentSelectedDataHash = getStateHash(selectedData);
        const areSetsEqual = prevSelectedDataHashRef.current === currentSelectedDataHash;

        if (!areSetsEqual) {
            rootDOMElement.dispatchEvent(
                new CustomEvent('ibexa-tb-update-selected', {
                    detail: {
                        id: moduleId,
                        items: selectedData,
                    },
                }),
            );

            prevSelectedDataHashRef.current = currentSelectedDataHash;
        }
    });

    return <SelectedContext.Provider value={selectedDataContextValues}>{children}</SelectedContext.Provider>;
};

SelectedProvider.propTypes = {
    children: PropTypes.node.isRequired,
    initiallySelectedItemsIds: PropTypes.array,
};

SelectedProvider.defaultProps = {
    initiallySelectedItemsIds: [],
};

export default SelectedProvider;
