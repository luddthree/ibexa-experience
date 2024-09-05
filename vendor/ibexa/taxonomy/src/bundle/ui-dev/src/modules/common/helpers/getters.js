import { getLastElement } from './array';

/* we don't have totalSubitems because of performance, we use nested set tree properties */
const hasSubitems = (item) => item.right - item.left > 1;

export const getContentLink = (item) => {
    const locationHref = window.Routing.generate('ibexa.content.view', {
        contentId: item.contentId,
    });

    return locationHref;
};

export const getTotal = (item) => {
    let totalChildrenCount = 0;

    if (hasSubitems(item)) {
        if (item.__children.length) {
            totalChildrenCount = item.__children.length;
        } else {
            totalChildrenCount = 1;
        }
    }

    return totalChildrenCount;
};

const getParentPath = (parents) => {
    if (!parents || parents.length === 0) {
        return '';
    }

    const parent = getLastElement(parents);

    return `${parent.path}/`;
};

export const getPath = (item, { parents } = {}) => {
    const parentPath = getParentPath(parents);

    return `${parentPath}${item.id}`;
};
