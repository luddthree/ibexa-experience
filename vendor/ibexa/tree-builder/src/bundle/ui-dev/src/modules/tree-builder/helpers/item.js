export const isItemDisabled = (originalItem, { parents, selectedData }) => {
    const isDescendant = parents.some((parent) => selectedData.some((item) => item.id === parent.id));

    return isDescendant;
};

export const isItemEmpty = (item) => item === null || item === undefined || Object.keys(item).length === 0;

export const isItemStored = (originalItem, items) => items.some((item) => item.id === originalItem.id);
