const findFirstIndex = (items, originalItem) => items.findIndex((item) => item.id === originalItem.id);

export const removeDuplicates = (items) => {
    const output = items.filter((item, index) => {
        const firstIndex = findFirstIndex(items, item);

        return firstIndex === index;
    });

    return output;
};
