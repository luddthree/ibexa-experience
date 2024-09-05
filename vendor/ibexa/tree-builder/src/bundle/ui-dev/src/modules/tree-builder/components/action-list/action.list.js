import React, { useState, useEffect, useContext, useMemo, createContext, forwardRef } from 'react';
import PropTypes from 'prop-types';

import { MenuActionsContext } from '../../tree.builder.module';
import { getUniqueFetchMethods, generateFetchActionsState, fetchData } from './action.list.helpers';

export const ForcedPropsContext = createContext();

const ActionList = forwardRef(({ item, extraClasses, useIconAsLabel, parent }, ref) => {
    const { getMenuActions, actions: allActions } = useContext(MenuActionsContext);
    const actions = getMenuActions({ actions: allActions, item });
    const uniqueFetchMethods = getUniqueFetchMethods(actions, parent);
    const [allFetchedData, setAllFetchedData] = useState(generateFetchActionsState(uniqueFetchMethods, item));
    const getSortedActions = (menu) => [...menu].sort((actionA, actionB) => actionA.priority - actionB.priority);
    const renderSubmenu = (menu) =>
        getSortedActions(menu)
            .filter((menuItem) => menuItem.subitems || menuItem.visibleIn?.includes(parent))
            .map((menuItem) => {
                const { subitems, component, id, forcedProps, fetchMethods } = menuItem;

                if (subitems) {
                    return (
                        <ul className="c-tb-action-list__list" key={id}>
                            {renderSubmenu(subitems)}
                        </ul>
                    );
                }

                const Component = component;
                const fetchedDataMap = allFetchedData.filter(({ fetchMethod }) => fetchMethods?.includes(fetchMethod));
                const isLoading = fetchedDataMap.some(({ isLoaded }) => !isLoaded);
                const fetchedData = fetchedDataMap.map(({ data }) => data);

                return (
                    <ForcedPropsContext.Provider key={id} value={forcedProps}>
                        <Component
                            item={item}
                            useIconAsLabel={useIconAsLabel}
                            isLoading={isLoading}
                            fetchedData={fetchedData}
                            {...menuItem}
                        />
                    </ForcedPropsContext.Provider>
                );
            });
    const menu = useMemo(() => renderSubmenu(actions), [actions, renderSubmenu]);
    const onFetchLoaded = (data, fetchMethodKey) => {
        setAllFetchedData((state) => {
            const newState = [...state];
            const fetchDataEntryKey = newState.findIndex(({ fetchMethod }) => fetchMethod === fetchMethodKey);

            newState[fetchDataEntryKey] = {
                ...newState[fetchDataEntryKey],
                isLoaded: true,
                data,
            };

            return newState;
        });
    };

    useEffect(() => {
        fetchData(uniqueFetchMethods, item, onFetchLoaded);
    }, []);

    return (
        <div ref={ref} className={`c-tb-action-list ${extraClasses}`}>
            <ul className="c-tb-action-list__list">{menu}</ul>
        </div>
    );
});

ActionList.propTypes = {
    extraClasses: PropTypes.string,
    item: PropTypes.object,
    useIconAsLabel: PropTypes.bool,
    parent: PropTypes.string,
};

ActionList.defaultProps = {
    extraClasses: '',
    item: {},
    useIconAsLabel: false,
    parent: '',
};

ActionList.displayName = 'ActionList';

export default ActionList;
