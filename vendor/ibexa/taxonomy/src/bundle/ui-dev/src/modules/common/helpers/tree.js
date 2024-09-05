export const findItem = (items, originalPath) => {
    const path = [...originalPath];
    const isLast = path.length === 1;
    const item = items.find((element) => element.id === parseInt(path[0], 10));

    if (!item) {
        return null;
    }

    if (isLast) {
        return item;
    }

    if (!Array.isArray(item.__children)) {
        return null;
    }

    path.shift();

    return findItem(item.__children, path);
};

export const isRoot = (item) => item.root === item.id;

export const getExpandedItems = (treeNode) => {
    if (!treeNode.__children.length) {
        return [];
    }

    return treeNode.__children
        .filter(({ __children }) => __children.length)
        .reduce((expandedItems, child) => [...expandedItems, ...getExpandedItems(child)], [treeNode]);
};
