import React, { useState, useRef, useEffect, createContext } from 'react';

import {
    getTranslator,
    getRouting,
    SYSTEM_ROOT_LOCATION_ID,
} from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';

import deepClone from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/deep.clone.helper.js';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';
import { getData, saveData } from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/helpers/localStorage';
import { ACTION_TYPE, ACTION_PARENT } from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/tree.builder.module';

import SelectAll from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/actions/select-all/select.all';
import UnselectAll from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/actions/unselect-all/unselect.all';
import CollapseAll from './components/collapse-all/collapse.all.js';
import AddRemoveBookmarks from './components/add-remove-bookmarks/add.remove.bookmarks';
import AddTranslation from './components/add-translation/add.translation';
import CreateContent from './components/create-content/create.content';
import EditContent from './components/edit-content/edit.content';
import HideContent from './components/hide-content/hide.content';
import DeleteContent from './components/delete-content/delete.content';

import {
    loadContentTypes,
    loadLocationItems,
    loadSubtree,
    findLocationsById,
    moveElements,
    copyElements,
    deleteElements,
    loadLocationItemExtendedInfo,
} from '../common/services/content.tree.service';
import { getContentLink, isUser } from '../common/helpers/getters';
import {
    findItem,
    generateInitialSubtree,
    expandCurrentLocationInSubtree,
    clipTooDeepSubtreeBranches,
    limitSubitemsInSubtree,
    getLoadSubtreeParams,
    generateSubtree,
    addItemToSubtree,
    removeItemFromSubtree,
    updateItemInSubtree,
} from '../common/helpers/tree';
import { getNotDeletedItemsData } from '../common/helpers/notifications';
import PreviewContent from './components/preview-content/preview.content.js';
import RemoveFromBookmarks from './components/remove-from-bookmarks/remove.from.bookmarks.js';
import AddToBookmarks from './components/add-to-bookmarks/add.to.bookmarks.js';

export const RestInfoContext = createContext();
export const ContentTypesInfoMapContext = createContext();

const { ibexa, document } = window;

const MODULE_ID = 'ibexa-content-tree';
const SUBTREE_ID = 'subtree';

const ContentTreeModule = (props) => {
    const Translator = getTranslator();
    const Routing = getRouting();
    const { userId, currentLocationPath, rootLocationId, subitemsLimit, subitemsLoadLimit, treeMaxDepth } = props;
    const treeBuilderModuleRef = useRef(null);
    const readSubtree = () => {
        const { readSubtree: customReadSubtree } = props;

        if (typeof customReadSubtree === 'function') {
            return customReadSubtree();
        }

        return getData({ moduleId: MODULE_ID, userId, subId: rootLocationId, dataId: SUBTREE_ID });
    };
    const saveSubtree = () => {
        const { saveSubtree: customSaveSubtree } = props;

        if (typeof customSaveSubtree === 'function') {
            customSaveSubtree();

            return;
        }

        saveData({ moduleId: MODULE_ID, userId, subId: rootLocationId, dataId: SUBTREE_ID, data: subtree.current });
    };
    const [isLoaded, setIsLoaded] = useState(false);
    const [contentTypesInfoMap, setContentTypesInfoMap] = useState({});
    const [tree, setTree] = useState(props.preloadedLocations);
    const subtree = useRef(generateInitialSubtree({ rootLocationId, subitemsLoadLimit })); // subtree is actually tree request data
    const getCurrentLocationId = () => {
        const currentLocationIdString = props.currentLocationPath
            .split('/')
            .filter((id) => !!id)
            .pop();

        return parseInt(currentLocationIdString, 10);
    };
    const setInitialItemsState = (location) => {
        subtree.current = generateSubtree({ items: [location], isRoot: true, subitemsLoadLimit, subitemsLimit });

        setIsLoaded(true);
        setTree(location);
        saveSubtree();
    };
    const loadTreeToState = () => {
        const { restInfo, sort } = props;

        setIsLoaded(false);
        loadSubtree(getLoadSubtreeParams({ subtree, restInfo, sort }))
            .then((loadedSubtree) => {
                setInitialItemsState(loadedSubtree[0]);

                const path = currentLocationPath.split('/').filter((id) => !!id);
                const rootLocationIdIndex = path.findIndex((element) => parseInt(element, 10) === rootLocationId);

                if (rootLocationIdIndex !== -1) {
                    const pathStartingOnRootLocation = path.slice(rootLocationIdIndex - path.length);
                    const itemsToExpand = pathStartingOnRootLocation.map((locationId) => ({ id: parseInt(locationId, 10) }));

                    treeBuilderModuleRef.current?.expandItems(itemsToExpand);
                }
            })
            .catch(window.ibexa.helpers.notification.showErrorNotification);
    };
    const findLocationsByIdWrapper = (args) =>
        findLocationsById({
            ...props.restInfo,
            limit: props.subitemsLoadLimit,
            ...args,
        });
    const renderIcon = (item, otherProps) => {
        const { contentTypeIdentifier, locationId } = item.internalItem;
        const { isLoading } = otherProps;
        const iconAttrs = {
            extraClasses: 'ibexa-icon--small',
        };

        if (!isLoading || item.subitems.length) {
            if (locationId === SYSTEM_ROOT_LOCATION_ID) {
                iconAttrs.customPath = ibexa.helpers.contentType.getContentTypeIconUrl('folder');
            } else {
                iconAttrs.customPath =
                    ibexa.helpers.contentType.getContentTypeIconUrl(contentTypeIdentifier) ||
                    ibexa.helpers.contentType.getContentTypeIconUrl('file');
            }
        } else {
            iconAttrs.name = 'spinner';
            iconAttrs.extraClasses = `${iconAttrs.extraClasses} ibexa-spin`;
        }

        return (
            <span className="c-ct-list-item__icon">
                <Icon {...iconAttrs} />
            </span>
        );
    };
    const renderLabel = (item, otherProps) => {
        const { name } = item.internalItem;
        const { labelTruncatedCallbackRef } = otherProps;

        return (
            <span className="c-tb-list-item-single__label-truncated" title={name} ref={labelTruncatedCallbackRef}>
                {name}
            </span>
        );
    };
    const callbackToggleExpanded = (item, { isExpanded, loadMore }) => {
        if (isExpanded) {
            addItemToSubtree(subtree.current[0], item.internalItem, item.internalItem.path.split('/'), {
                subitemsLoadLimit,
                subitemsLimit,
            });
        } else {
            removeItemFromSubtree(subtree.current[0], item.internalItem, item.internalItem.path.split('/'));
        }

        saveSubtree();

        const { subitems } = item;
        const shouldLoadInitialItems = isExpanded && subitems && !subitems.length;

        if (shouldLoadInitialItems) {
            loadMore();
        }
    };
    const callbackMoveElements = (item, { selectedData }) => {
        const sourceIds = selectedData.map((selectedItem) => selectedItem.id).join(',');

        return findLocationsByIdWrapper({ id: sourceIds })
            .then((response) => {
                const destination = `/${item.nextParent.path}`;

                return moveElements(response, destination, { ...props.restInfo });
            })
            .then((response) => {
                const { success: movedItems, fail: notMovedItems } = response;

                if (notMovedItems.length) {
                    const message = Translator.trans(
                        /*@Desc("%notMovedCount% of the %totalCount% selected item(s) could not be moved because you do not have proper user permissions. Contact your Administrator.")*/ 'bulk_move.error.message',
                        {
                            notMovedCount: notMovedItems.length,
                            totalCount: movedItems.length + notMovedItems.length,
                        },
                        'ibexa_content_tree_ui',
                    );

                    window.ibexa.helpers.notification.showWarningNotification(message);
                }

                if (movedItems.length) {
                    const { name, contentId, locationId } = item.nextParent.internalItem;
                    const message = Translator.trans(
                        /*@Desc("Content item(s) sent to {{ locationLink }}")*/
                        'bulk_move.success.message',
                        {},
                        'ibexa_content_tree_ui',
                    );
                    const rawPlaceholdersMap = {
                        locationLink: Translator.trans(
                            /*@Desc("<u><a href='%locationHref%'>%locationName%</a></u>")*/
                            'bulk_action.success.link_to_location',
                            {
                                locationName: ibexa.helpers.text.escapeHTML(name),
                                locationHref: Routing.generate('ibexa.content.view', { locationId, contentId }),
                            },
                            'ibexa_content_tree_ui',
                        ),
                    };

                    window.ibexa.helpers.notification.showSuccessNotification(message, () => {}, rawPlaceholdersMap);
                }

                return Promise.resolve();
            })
            .then(loadTreeToState)
            .catch(window.ibexa.helpers.notification.showErrorNotification);
    };
    const callbackCopyElements = (item, { selectedData }) => {
        const sourceIds = selectedData.map((selectedItem) => selectedItem.id).join(',');

        return findLocationsByIdWrapper({ id: sourceIds })
            .then((response) => {
                const destination = `/${item.path}`;

                return copyElements(response, destination, { ...props.restInfo });
            })
            .then((response) => {
                const { success: copiedItems, fail: notCopiedItems } = response;

                if (notCopiedItems.length) {
                    const message = Translator.trans(
                        /*@Desc("%notCopiedCount% of the %totalCount% selected item(s) could not be copied because you do not have proper user permissions. Contact your Administrator.")*/ 'bulk_copy.error.message',
                        {
                            notCopiedCount: notCopiedItems.length,
                            totalCount: copiedItems.length + notCopiedItems.length,
                        },
                        'ibexa_content_tree_ui',
                    );

                    window.ibexa.helpers.notification.showWarningNotification(message);
                }

                if (copiedItems.length) {
                    const { name, contentId, locationId } = item.internalItem;
                    const message = Translator.trans(
                        /*@Desc("Content item(s) copied to {{ locationLink }}")*/
                        'bulk_copy.success.message',
                        {},
                        'ibexa_content_tree_ui',
                    );
                    const rawPlaceholdersMap = {
                        locationLink: Translator.trans(
                            /*@Desc("<u><a href='%locationHref%'>%locationName%</a></u>")*/
                            'bulk_action.success.link_to_location',
                            {
                                locationName: ibexa.helpers.text.escapeHTML(name),
                                locationHref: Routing.generate('ibexa.content.view', { locationId, contentId }),
                            },
                            'ibexa_content_tree_ui',
                        ),
                    };

                    window.ibexa.helpers.notification.showSuccessNotification(message, () => {}, rawPlaceholdersMap);
                }

                return Promise.resolve();
            })
            .then(loadTreeToState)
            .catch(window.ibexa.helpers.notification.showErrorNotification);
    };
    const findRedirectLocationId = (entries) => {
        let pathIds = ibexa.helpers.location.removeRootFromPathString(currentLocationPath).map((id) => parseInt(id, 10));

        entries.forEach((entryId) => {
            const entryIdIndex = pathIds.findIndex((id) => id === entryId);

            if (entryIdIndex !== -1) {
                pathIds = pathIds.slice(0, entryIdIndex);
            }
        });

        return pathIds[pathIds.length - 1];
    };
    const callbackDeleteElements = ({ selectedData }) => {
        const entries = selectedData.map((item) => item.id);
        const sourceIds = entries.join(',');

        return findLocationsByIdWrapper({ id: sourceIds })
            .then((response) => {
                return deleteElements(response, { ...props.restInfo, contentTypes: contentTypesInfoMap });
            })
            .then((response) => {
                const { success: deletedItems, fail: notDeletedItems } = response;

                if (notDeletedItems.length) {
                    const { message } = getNotDeletedItemsData(notDeletedItems, deletedItems, isUser.bind(null, contentTypesInfoMap));

                    window.ibexa.helpers.notification.showWarningNotification(message);
                } else {
                    const anyUserContentItemDeleted = deletedItems.some(isUser.bind(null, contentTypesInfoMap));
                    const anyNonUserContentItemDeleted = deletedItems.some((location) => !isUser(contentTypesInfoMap, location));
                    let message = null;

                    if (anyUserContentItemDeleted && anyNonUserContentItemDeleted) {
                        message = Translator.trans(
                            /*@Desc("Content item(s) sent to Trash. User(s) deleted.")*/ 'bulk_delete.success.message.users_with_nonusers',
                            {},
                            'ibexa_content_tree_ui',
                        );
                    } else if (anyUserContentItemDeleted) {
                        message = Translator.trans(
                            /*@Desc("User(s) deleted.")*/ 'bulk_delete.success.message.users',
                            {},
                            'ibexa_content_tree_ui',
                        );
                    } else {
                        message = Translator.trans(
                            /*@Desc("Content item(s) sent to Trash.")*/ 'bulk_delete.success.message.nonusers',
                            {},
                            'ibexa_content_tree_ui',
                        );
                    }

                    window.ibexa.helpers.notification.showSuccessNotification(message);
                }

                return Promise.resolve();
            })
            .then(() => {
                const pathIds = ibexa.helpers.location.removeRootFromPathString(currentLocationPath);
                const currentLocationId = parseInt(pathIds[pathIds.length - 1], 10);
                const redirectLocationId = findRedirectLocationId(entries);

                if (redirectLocationId !== currentLocationId) {
                    findLocationsByIdWrapper({ id: redirectLocationId }).then(([nodeResponse]) => {
                        window.location.href = getContentLink({
                            contentId: nodeResponse.ContentInfo.Content._id,
                            locationId: nodeResponse.id,
                        });
                    });
                } else {
                    window.location.reload(true);
                }
            })
            .catch(window.ibexa.helpers.notification.showErrorNotification);
    };
    const isActive = (item) => {
        return item.internalItem.locationId === getCurrentLocationId();
    };
    const loadMoreSubitems = (item) =>
        loadLocationItems({
            ...props.restInfo,
            parentLocationId: item.internalItem.locationId,
            limit: props.subitemsLoadLimit,
            offset: item.internalItem.subitems.length,
        })
            .then((location) => {
                setTree((prevTree) => {
                    const prevTreeParentItem = findItem([prevTree], item.internalItem.path.split('/'));

                    if (prevTreeParentItem) {
                        const nextTree = deepClone(prevTree);
                        const nextTreeParentItem = findItem([nextTree], item.internalItem.path.split('/'));

                        nextTreeParentItem.subitems = [...nextTreeParentItem.subitems, ...location.subitems].map((subitem) => ({
                            ...subitem,
                            path: `${nextTreeParentItem.path}/${subitem.locationId}`,
                        }));

                        updateItemInSubtree(subtree.current[0], nextTreeParentItem, item.internalItem.path.split('/'));
                        saveSubtree();

                        return nextTree;
                    }

                    return prevTree;
                });
            })
            .catch(window.ibexa.helpers.notification.showErrorNotification);
    const getCustomItemClass = (item) => {
        const { children, total, isRootItem } = item;
        const className = createCssClassNames({
            'c-ct-list-item': true,
            'c-ct-list-item--can-load-more': children && children.length < total,
            'c-ct-list-item--is-root-item': isRootItem,
        });

        return className;
    };
    const renderEmpty = () => {
        if (!isLoaded || tree?.locationId !== undefined) {
            return null;
        }

        const emptyBadge = Translator.trans(/*@Desc("1")*/ 'content.1', {}, 'ibexa_content_tree_ui');
        const emptyContent = Translator.trans(
            /*@Desc("Your tree is empty. Start creating your structure")*/ 'content.empty',
            {},
            'ibexa_content_tree_ui',
        );

        return (
            <div className="c-ct-empty">
                <div className="c-ct-empty__badge">
                    <div className="c-ct-badge">
                        <div className="c-ct-badge__content">{emptyBadge}</div>
                    </div>
                </div>
                <div className="c-ct-empty__content">{emptyContent}</div>
            </div>
        );
    };
    const buildItem = (item) =>
        item.internalItem
            ? item
            : {
                  internalItem: item,
                  name: item.name,
                  id: item.locationId,
                  href: getContentLink(item),
                  path: item.path,
                  subitems: item.subitems,
                  total: item.totalSubitemsCount,
                  hidden: item.isInvisible,
                  renderIcon,
                  renderLabel,
                  customItemClass: getCustomItemClass(item),
              };
    const moduleName = Translator.trans(/*@Desc("Content tree")*/ 'content.tree_name', {}, 'ibexa_content_tree_ui');

    useEffect(() => {
        loadContentTypes(props.restInfo.siteaccess)
            .then((response) => {
                const contentTypesMap = response.ContentTypeInfoList.ContentType.reduce((contentTypesList, item) => {
                    contentTypesList[item._href] = item;

                    return contentTypesList;
                }, {});

                setContentTypesInfoMap(contentTypesMap);
            })
            .catch(() => {
                const errorMessage = Translator.trans(
                    /*@Desc("Cannot load content types")*/ 'load_content_types.error',
                    {},
                    'ibexa_content_tree_ui',
                );

                ibexa.helpers.notification.showErrorNotification(errorMessage);
            });
    }, []);

    useEffect(() => {
        const subtreeData = readSubtree();

        if (subtreeData) {
            subtree.current = subtreeData;
        }

        expandCurrentLocationInSubtree({ subtree: subtree.current, rootLocationId, currentLocationPath, subitemsLimit });
        clipTooDeepSubtreeBranches({ subtree: subtree.current[0], maxDepth: treeMaxDepth - 1 });
        subtree.current[0].children.forEach((subtreeChild) => limitSubitemsInSubtree({ subtree: subtreeChild, subitemsLimit }));
        saveSubtree();
        loadTreeToState();
    }, []);

    useEffect(() => {
        document.body.addEventListener('ibexa-content-tree-refresh', loadTreeToState);
        document.body.addEventListener('ibexa-bookmark-change', loadTreeToState);

        return () => {
            document.body.removeEventListener('ibexa-content-tree-refresh', loadTreeToState);
            document.body.removeEventListener('ibexa-bookmark-change', loadTreeToState);
        };
    }, []);

    return (
        <RestInfoContext.Provider value={props.restInfo}>
            <ContentTypesInfoMapContext.Provider value={contentTypesInfoMap}>
                <ibexa.modules.TreeBuilder
                    ref={treeBuilderModuleRef}
                    moduleId={MODULE_ID}
                    moduleName={moduleName}
                    userId={userId}
                    subId={rootLocationId}
                    tree={tree}
                    buildItem={buildItem}
                    isActive={isActive}
                    loadMoreSubitems={loadMoreSubitems}
                    callbackToggleExpanded={callbackToggleExpanded}
                    callbackMoveElements={callbackMoveElements}
                    callbackCopyElements={callbackCopyElements}
                    callbackDeleteElements={callbackDeleteElements}
                    subitemsLimit={subitemsLimit}
                    treeDepthLimit={treeMaxDepth}
                    actionsType={ACTION_TYPE.CONTEXTUAL_MENU}
                    dragDisabled={true}
                    isLoading={!isLoaded}
                    useTheme={true}
                >
                    {renderEmpty()}
                </ibexa.modules.TreeBuilder>
            </ContentTypesInfoMapContext.Provider>
        </RestInfoContext.Provider>
    );
};

ContentTreeModule.propTypes = {
    currentLocationPath: PropTypes.string.isRequired,
    userId: PropTypes.number.isRequired,
    restInfo: PropTypes.shape({
        token: PropTypes.string.isRequired,
        siteaccess: PropTypes.string.isRequired,
    }).isRequired,
    preloadedLocations: PropTypes.object,
    subitemsLimit: PropTypes.number,
    subitemsLoadLimit: PropTypes.number,
    rootLocationId: PropTypes.number,
    treeMaxDepth: PropTypes.number,
    readSubtree: PropTypes.func,
    saveSubtree: PropTypes.func,
    sort: PropTypes.shape({
        sortClause: PropTypes.string,
        sortOrder: PropTypes.string,
    }),
};

ContentTreeModule.defaultProps = {
    preloadedLocations: {},
    rootLocationId: ibexa?.adminUiConfig.contentTree.treeRootLocationId,
    subitemsLimit: ibexa?.adminUiConfig.contentTree.childrenLoadMaxLimit,
    subitemsLoadLimit: ibexa?.adminUiConfig.contentTree.loadMoreLimit,
    treeMaxDepth: ibexa?.adminUiConfig.contentTree.treeMaxDepth,
    readSubtree: undefined,
    saveSubtree: undefined,
    sort: {},
};

export const treeBuilderConfig = {
    [MODULE_ID]: {
        menuActions: [
            {
                priority: 20,
                id: 'modifying',
                subitems: [
                    {
                        priority: 10,
                        id: 'create',
                        component: CreateContent,
                        visibleIn: [ACTION_PARENT.SINGLE_ITEM],
                        fetchMethods: [loadLocationItemExtendedInfo],
                    },
                    {
                        priority: 20,
                        id: 'edit',
                        component: EditContent,
                        visibleIn: [ACTION_PARENT.SINGLE_ITEM],
                        fetchMethods: [loadLocationItemExtendedInfo],
                    },
                    {
                        priority: 20,
                        id: 'preview',
                        component: PreviewContent,
                        visibleIn: [ACTION_PARENT.SINGLE_ITEM],
                        fetchMethods: [loadLocationItemExtendedInfo],
                    },
                    {
                        priority: 30,
                        id: 'delete',
                        component: DeleteContent,
                        visibleIn: [ACTION_PARENT.SINGLE_ITEM],
                        fetchMethods: [loadLocationItemExtendedInfo],
                    },
                    {
                        priority: 40,
                        id: 'addTranslation',
                        component: AddTranslation,
                        visibleIn: [ACTION_PARENT.SINGLE_ITEM],
                        fetchMethods: [loadLocationItemExtendedInfo],
                    },
                    {
                        priority: 50,
                        id: 'hide',
                        component: HideContent,
                        visibleIn: [ACTION_PARENT.SINGLE_ITEM],
                        fetchMethods: [loadLocationItemExtendedInfo],
                    },
                ],
            },
            {
                priority: 25,
                id: 'bookmark',
                subitems: [
                    {
                        priority: 10,
                        id: 'addRemoveBookmarks',
                        component: AddRemoveBookmarks,
                        visibleIn: [ACTION_PARENT.SINGLE_ITEM],
                    },
                    {
                        priority: 100,
                        id: 'addBookmarks',
                        component: AddToBookmarks,
                        visibleIn: [ACTION_PARENT.TOP_MENU],
                    },
                    {
                        priority: 200,
                        id: 'removeBookmarks',
                        component: RemoveFromBookmarks,
                        visibleIn: [ACTION_PARENT.TOP_MENU],
                    },
                ],
            },
            {
                priority: 30,
                id: 'collapsible',
                subitems: [
                    {
                        priority: 20,
                        id: 'collapse',
                        component: CollapseAll,
                        visibleIn: [ACTION_PARENT.TOP_MENU, ACTION_PARENT.SINGLE_ITEM],
                    },
                ],
            },
            {
                priority: 40,
                id: 'other',
                subitems: [
                    {
                        priority: 10,
                        id: 'select',
                        component: SelectAll,
                        visibleIn: [ACTION_PARENT.TOP_MENU, ACTION_PARENT.SINGLE_ITEM],
                    },
                    {
                        priority: 20,
                        id: 'unselect',
                        component: UnselectAll,
                        visibleIn: [ACTION_PARENT.TOP_MENU, ACTION_PARENT.SINGLE_ITEM],
                    },
                ],
            },
        ],
    },
};

export default ContentTreeModule;
