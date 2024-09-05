import { isRoot } from './tree';

export const checkIsDisabledMove = (items) => {
    if (items.length !== 1) {
        return false;
    }

    const entry = items[0].internalItem;

    return isRoot(entry);
};

export const checkIsDisabledDelete = (items) => {
    if (items.length !== 1) {
        return false;
    }

    const entry = items[0].internalItem;

    return isRoot(entry);
};
