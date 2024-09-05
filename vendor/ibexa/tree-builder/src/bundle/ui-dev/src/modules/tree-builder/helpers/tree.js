import { getAdminUiConfig } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';

const EXCLUDED_ACTION_IDS = ['preview'];
const isActionExcluded = ({ action, item, previewExcludedItemPath }) => {
    if (!item.internalItem || !EXCLUDED_ACTION_IDS.includes(action.id)) {
        return false;
    }

    const { pathString } = item.internalItem;

    return previewExcludedItemPath.some((excludedPath) => pathString.startsWith(excludedPath));
};

export const getMenuActions = ({
    actions,
    item,
    activeActionsIds = [],
    previewExcludedItemPath = getAdminUiConfig().siteContext?.excludedPaths ?? [],
}) => {
    const filteredActions =
        previewExcludedItemPath.length && item
            ? actions.filter((action) => !isActionExcluded({ action, item, previewExcludedItemPath }))
            : actions;

    return filteredActions.map((action) => {
        const nextAction = { ...action };

        if (nextAction.component && activeActionsIds.length && !activeActionsIds.includes(nextAction.id)) {
            nextAction.forcedProps = {
                isDisabled: true,
            };
        }

        if (nextAction.subitems) {
            nextAction.subitems = getMenuActions({ actions: nextAction.subitems, item, activeActionsIds });
        }

        return nextAction;
    });
};

export const getAllChildren = ({ data, buildItem, condition }) => {
    const output = [];
    const getAllChildrenHelper = (items = []) => {
        items.forEach((originalItem) => {
            const item = buildItem ? buildItem(originalItem) : originalItem;

            if (condition === undefined || condition(item)) {
                output.push(item);
            }

            getAllChildrenHelper(item.subitems);
        });
    };

    getAllChildrenHelper([data]);

    return output;
};
