const cachedFetchMethodsMap = new Map();

const getAllFetchMethods = (actions, zoneType) =>
    actions.flatMap((action) => {
        if (action.subitems) {
            return getAllFetchMethods(action.subitems, zoneType);
        } else if (action.fetchMethods && (action.visibleIn?.includes(zoneType) ?? true)) {
            return action.fetchMethods;
        }

        return [];
    });
export const getUniqueFetchMethods = (actions, zoneType) => {
    const allFetchMethods = getAllFetchMethods(actions, zoneType);
    const uniqueFetchMethods = allFetchMethods.filter((fetchMethod, index, originalFetchArray) => {
        return originalFetchArray.indexOf(fetchMethod) === index;
    });

    return uniqueFetchMethods;
};
export const fetchData = (fetchMethods, item, callback) => {
    fetchMethods.forEach((fetchMethod) => {
        if (!cachedFetchMethodsMap.has(item.id)) {
            cachedFetchMethodsMap.set(item.id, new WeakMap());
        }

        const cachedItemMethods = cachedFetchMethodsMap.get(item.id);

        if (cachedItemMethods?.has(fetchMethod)) {
            const data = cachedItemMethods.get(fetchMethod);

            callback(data, fetchMethod, true);
        } else {
            fetchMethod(item).then((results) => {
                cachedItemMethods.set(fetchMethod, results);
                callback(results, fetchMethod, false);
            });
        }
    });
};
export const generateFetchActionsState = (fetchMethods, item) => {
    return fetchMethods.map((fetchMethod) => {
        const data = cachedFetchMethodsMap.get(item.id)?.get(fetchMethod);

        return {
            fetchMethod,
            isLoaded: !!data,
            data,
        };
    });
};
