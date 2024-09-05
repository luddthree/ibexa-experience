export const findItem = (items, originalPath) => {
    const path = [...originalPath];
    const isLeaf = path.length === 1;
    const item = items.find((element) => element.locationId === parseInt(path[0], 10));

    if (!item) {
        return null;
    }

    if (isLeaf) {
        return item;
    }

    if (!Array.isArray(item.subitems)) {
        return null;
    }

    path.shift();

    return findItem(item.subitems, path);
};

const generateSubtreeNode = ({ locationId, limit, offset, children }) => ({
    '_media-type': 'application/vnd.ibexa.api.ContentTreeLoadSubtreeRequestNode',
    locationId,
    limit,
    offset: offset ?? 0,
    children: children ?? [],
});

const expandPathInSubtree = ({ subtree, path: originalPath, subitemsLimit }) => {
    const path = [...originalPath];

    if (!path.length) {
        return;
    }

    const locationId = parseInt(path[0], 10);
    let nextSubtree = subtree.children.find((subtreeChild) => subtreeChild.locationId === locationId);

    if (!nextSubtree) {
        nextSubtree = generateSubtreeNode({
            locationId: locationId,
            limit: subitemsLimit,
        });
        subtree.children.push(nextSubtree);
    }

    path.shift();
    expandPathInSubtree({ subtree: nextSubtree, path, subitemsLimit });
};

const findParentSubtree = (subtree, originalPath) => {
    const path = [...originalPath];

    if (path.length < 2) {
        return;
    }

    path.shift();
    path.pop();

    return path.reduce(
        (subtreeChild, locationId) => subtreeChild.children.find((element) => element.locationId === parseInt(locationId, 10)),
        subtree,
    );
};

export const generateInitialSubtree = ({ rootLocationId, subitemsLoadLimit }) => [
    generateSubtreeNode({
        locationId: rootLocationId,
        limit: subitemsLoadLimit,
    }),
];

export const getLoadSubtreeParams = ({ subtree, restInfo, sort }) => ({
    token: restInfo.token,
    siteaccess: restInfo.siteaccess,
    accessToken: restInfo.accessToken,
    instanceUrl: restInfo.instanceUrl,
    subtree: subtree.current,
    sortClause: sort.sortClause,
    sortOrder: sort.sortOrder,
});

export const expandCurrentLocationInSubtree = ({ subtree, rootLocationId, currentLocationPath, subitemsLimit }) => {
    const path = currentLocationPath.split('/').filter((id) => !!id);
    const rootLocationIdIndex = path.findIndex((element) => parseInt(element, 10) === rootLocationId);

    if (rootLocationIdIndex === -1) {
        return;
    }

    const pathStartingAfterRootLocation = path.slice(rootLocationIdIndex - path.length + 1);
    const pathWithoutLeaf = pathStartingAfterRootLocation.slice(0, pathStartingAfterRootLocation.length - 1);

    expandPathInSubtree({ subtree: subtree[0], path: pathWithoutLeaf, subitemsLimit });
};

export const clipTooDeepSubtreeBranches = ({ subtree, maxDepth }) => {
    if (maxDepth <= 0) {
        subtree.children = [];

        return;
    }

    subtree.children.forEach((subtreeChild) => clipTooDeepSubtreeBranches({ subtree: subtreeChild, maxDepth: maxDepth - 1 }));
};

export const limitSubitemsInSubtree = ({ subtree, subitemsLimit }) => {
    subtree.limit = Math.min(subitemsLimit, subtree.limit);
    subtree.children.forEach((subtreeChild) => limitSubitemsInSubtree({ subtree: subtreeChild, subitemsLimit }));
};

export const generateSubtree = ({ items, isRoot, subitemsLoadLimit, subitemsLimit }) => {
    const itemsWithoutLeafs = [];

    for (const item of items) {
        const subitemsCount = item.subitems.length;
        const isLeaf = !subitemsCount;

        if (!isLeaf || isRoot) {
            const limit = subitemsCount ? Math.ceil(subitemsCount / subitemsLoadLimit) * subitemsLoadLimit : subitemsLoadLimit;

            itemsWithoutLeafs.push(
                generateSubtreeNode({
                    locationId: item.locationId,
                    limit: Math.min(subitemsLimit, limit),
                    children: generateSubtree({ items: item.subitems, isRoot: false, subitemsLoadLimit, subitemsLimit }),
                }),
            );
        }
    }

    return itemsWithoutLeafs;
};

export const addItemToSubtree = (subtree, item, path, { subitemsLoadLimit, subitemsLimit }) => {
    const parentSubtree = findParentSubtree(subtree, path);

    if (!parentSubtree) {
        return;
    }

    const limit = Math.ceil(item.subitems.length / subitemsLoadLimit) * subitemsLoadLimit;

    parentSubtree.children.push(
        generateSubtreeNode({
            locationId: item.locationId,
            limit: Math.min(subitemsLimit, limit),
        }),
    );
};

export const removeItemFromSubtree = (subtree, item, path) => {
    const parentSubtree = findParentSubtree(subtree, path);

    if (!parentSubtree) {
        return;
    }

    const index = parentSubtree.children.findIndex((element) => element.locationId === item.locationId);

    if (index > -1) {
        parentSubtree.children.splice(index, 1);
    }
};

export const updateItemInSubtree = (subtree, item, path) => {
    const parentSubtree = findParentSubtree(subtree, path);

    if (!parentSubtree) {
        return;
    }

    const index = parentSubtree.children.findIndex((element) => element.locationId === item.locationId);

    if (index > -1) {
        parentSubtree.children[index].limit = item.subitems.length;
    }
};
