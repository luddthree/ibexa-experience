import React, { useState, useEffect, useRef, useContext } from 'react';
import PropTypes from 'prop-types';

import Icon from '@ibexa-admin-ui-modules/common/icon/icon';
import deepClone from '@ibexa-admin-ui-modules/common/helpers/deep.clone.helper';
import { createCssClassNames } from '@ibexa-admin-ui-modules/common/helpers/css.class.names';
import { RootLocationIdContext } from '@ibexa-admin-ui-modules/universal-discovery/universal.discovery.module';

import {
    getTranslator,
    getAdminUiConfig,
    getRestInfo,
    getRootDOMElement,
    SYSTEM_ROOT_LOCATION_ID,
} from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';
import { getId as getUserId } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/user.helper';
import {
    showErrorNotification,
    showWarningNotification,
} from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/notification.helper';
import {
    getContentTypeIconUrl,
    createContentTypeDataMapByHref,
} from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/content.type.helper';

import { getData, saveData } from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/helpers/localStorage';
import TreeBuilderModule, {
    ACTION_TYPE,
    ACTION_PARENT,
} from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/tree.builder.module';

import DeleteContent from '@ibexa-content-tree/src/bundle/ui-dev/src/modules/content-tree/components/delete-content/delete.content';
import {
    loadLocationItems,
    loadSubtree,
    loadLocationItemExtendedInfo,
} from '@ibexa-content-tree/src/bundle/ui-dev/src/modules/common/services/content.tree.service';
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
} from '@ibexa-content-tree/src/bundle/ui-dev/src/modules/common/helpers/tree';
import { getNotDeletedItemsData } from '@ibexa-content-tree/src/bundle/ui-dev/src/modules/common/helpers/notifications';
import { isUser } from '@ibexa-content-tree/src/bundle/ui-dev/src/modules/common//helpers/getters';

import {
    FiltersContext,
    RootLocationContext,
    rootLocationFakeContentTypeIdentifier,
    rootLocationFakeName,
} from '../../image.picker.tab.module';
import {
    createDraft,
    updateDraft,
    publishDraft,
    deleteElements,
    moveElements,
    createDraftFromCurrentVersion,
    getContentNameFieldIdentifierForEdit,
    getContentNameFieldIdentifierForCreate,
} from '../../services/image.picker.service';
import QuickCreateContent from '../action-menu-item/quick.create.content';
import QuickEditContent from '../action-menu-item/quick.edit.content';

const { ibexa } = window;

const MODULE_ID = 'ibexa-image-picker-tree-browser';
const SUBTREE_ID = 'subtree';

const TreeBrowser = (props) => {
    const Translator = getTranslator();
    const restInfo = getRestInfo();
    const adminUiConfig = getAdminUiConfig();
    const rootDOMElement = getRootDOMElement();
    const contentTypesInfoMap = createContentTypeDataMapByHref();
    const subitemsLimit = props.subitemsLimit ?? adminUiConfig.contentTree.childrenLoadMaxLimit;
    const subitemsLoadLimit = props.subitemsLoadLimit ?? adminUiConfig.contentTree.loadMoreLimit;
    const treeMaxDepth = props.treeMaxDepth ?? adminUiConfig.contentTree.treeMaxDepth;
    const rootLocationId = useContext(RootLocationIdContext);
    const { rootLocation } = useContext(RootLocationContext);
    const { selectedLocationData, setSelectedLocationData } = useContext(FiltersContext);
    const currentLocationPath = rootLocation?.pathString.match(/(.*\/)[1-9]+\//)[1] ?? null;
    const treeBuilderModuleRef = useRef(null);
    const userId = getUserId();
    const readSubtree = () => {
        return getData({ moduleId: MODULE_ID, userId, subId: rootLocationId, dataId: SUBTREE_ID });
    };
    const saveSubtree = () => {
        saveData({ moduleId: MODULE_ID, userId, subId: rootLocationId, dataId: SUBTREE_ID, data: subtree.current });
    };
    const [isLoaded, setIsLoaded] = useState(false);
    const [tree, setTree] = useState({});
    const subtree = useRef(generateInitialSubtree({ rootLocationId, subitemsLoadLimit })); // subtree is actually tree request data

    const actionErrorCallback = (msg) => {
        setIsLoaded(true);

        showErrorNotification(msg);
    };
    const setInitialItemsState = (location) => {
        subtree.current = generateSubtree({ items: [location], isRoot: true, subitemsLoadLimit, subitemsLimit });
        setIsLoaded(true);
        setTree(location);
        saveSubtree();
    };
    const loadTreeToState = () => {
        if (!currentLocationPath) {
            return;
        }

        const { sort } = props;

        loadSubtree({
            ...getLoadSubtreeParams({ subtree, restInfo, sort }),
            filter: {
                ContentTypeIdentifierCriterion: adminUiConfig.damWidget.folder.contentTypeIdentifier,
            },
        })
            .then((loadedSubtree) => {
                const rootElement = loadedSubtree[0];

                if (rootElement.path === SYSTEM_ROOT_LOCATION_ID.toString()) {
                    rootElement.totalSubitemsCount = rootElement.subitems.length;
                    rootElement.contentTypeIdentifier = rootLocationFakeContentTypeIdentifier;
                    rootElement.name = rootLocationFakeName();
                }

                setInitialItemsState(loadedSubtree[0]);

                const path = currentLocationPath.split('/').filter((id) => !!id);
                const rootLocationIdIndex = path.findIndex((element) => parseInt(element, 10) === rootLocationId);

                if (rootLocationIdIndex !== -1) {
                    const pathStartingOnRootLocation = path.slice(rootLocationIdIndex - path.length);
                    const itemsToExpand = pathStartingOnRootLocation.map((locationId) => ({ id: parseInt(locationId, 10) }));

                    treeBuilderModuleRef.current?.expandItems(itemsToExpand);
                }

                setIsLoaded(true);
            })
            .catch(showErrorNotification);
    };
    const renderLabel = (item, otherProps) => {
        const { name, contentTypeIdentifier, locationId } = item.internalItem;
        const { isLoading, labelTruncatedCallbackRef } = otherProps;
        const iconAttrs = {
            extraClasses: 'ibexa-icon--small ibexa-icon--dark',
        };

        if (!isLoading || item.subitems.length) {
            if (locationId === SYSTEM_ROOT_LOCATION_ID) {
                iconAttrs.customPath = getContentTypeIconUrl('folder');
            } else {
                iconAttrs.customPath = getContentTypeIconUrl(contentTypeIdentifier) || getContentTypeIconUrl('file');
            }

            const [, iconName] = iconAttrs.customPath.split('#');

            if (iconName) {
                iconAttrs.name = iconName;
            }
        } else {
            iconAttrs.name = 'spinner';
            iconAttrs.extraClasses = `${iconAttrs.extraClasses} ibexa-spin`;
        }

        return (
            <>
                <span className="c-ct-list-item__icon">
                    <Icon {...iconAttrs} />
                </span>
                <span className="c-tb-list-item-single__label-truncated" title={name} ref={labelTruncatedCallbackRef}>
                    {name}
                </span>
            </>
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
    const callbackQuickEditElement = async (item, updatedName) => {
        const { name: originalItemName, contentId, locationId } = item.internalItem;

        if (originalItemName === updatedName) {
            return;
        }

        setIsLoaded(false);

        const errorMsg = Translator.trans(
            /*@Desc("Can’t rename %name%. Try again later.")*/ 'quick_actions.validation_msgs.rename_error',
            {
                name: updatedName,
            },
            'ibexa_image_picker',
        );
        const nameFieldIdentifier = await getContentNameFieldIdentifierForEdit(locationId);
        const contentDraft = await createDraftFromCurrentVersion({ contentId }, () => actionErrorCallback(errorMsg));
        const versionHref = contentDraft.Version._href;
        const languageCode = contentDraft.Version.VersionInfo.initialLanguageCode;
        await updateDraft(
            {
                versionHref,
                nameFieldIdentifier,
                languageCode,
                updatedName,
            },
            () => actionErrorCallback(errorMsg),
        );
        await publishDraft({ versionHref }, () => actionErrorCallback(errorMsg));
        loadTreeToState();
    };
    const callbackQuickCreateElement = async (parentItem, createdName) => {
        if (!createdName) {
            return;
        }

        setIsLoaded(false);

        const errorMsg = Translator.trans(
            /*@Desc("Can’t create %name%. Try again later.")*/ 'quick_actions.validation_msgs.create_error',
            {
                name: createdName,
            },
            'ibexa_image_picker',
        );
        const { contentTypeIdentifier } = adminUiConfig.damWidget.folder;
        const nameFieldIdentifier = await getContentNameFieldIdentifierForCreate(contentTypeIdentifier);
        const languageCode = parentItem.mainLanguageCode;
        const parentLocationHref = `/api/ibexa/v2/content/locations${parentItem.pathString}`.slice(0, -1);
        const createDraftResponse = await createDraft(
            {
                contentTypeIdentifier,
                parentLocationHref,
                nameFieldIdentifier,
                languageCode,
                createdName,
            },
            () => actionErrorCallback(errorMsg),
        );

        if (!createDraftResponse) {
            return;
        }

        const versionHref = createDraftResponse.Content.CurrentVersion.Version._href;

        await publishDraft({ versionHref }, () => actionErrorCallback(errorMsg));

        loadTreeToState();
    };
    const callbackDeleteElements = async ({ selectedData }) => {
        const entriesName = selectedData.map((entry) => entry.name);
        const errorMsg = Translator.trans(
            /*@Desc("Can’t delete %names%. Try again later.")*/ 'quick_actions.validation_msgs.delete_error',
            {
                names: entriesName.join(', '),
            },
            'ibexa_image_picker',
        );

        setIsLoaded(false);

        const deleteElementsResponse = await deleteElements({ selectedData, subitemsLoadLimit }, () => actionErrorCallback(errorMsg));
        const { success: deletedItems, fail: notDeletedItems } = deleteElementsResponse;
        const isSelectedLocationDeleted = deletedItems.find((deletedItem) => deletedItem.pathString === selectedLocationData.path);

        if (notDeletedItems.length) {
            const { message } = getNotDeletedItemsData(notDeletedItems, deletedItems, isUser.bind(null, contentTypesInfoMap));

            showWarningNotification(message);
        } else {
            loadTreeToState();

            if (isSelectedLocationDeleted) {
                const { path, locationId, name, contentTypeIdentifier, previewableTranslations } = tree;
                const fullPath = getFullPath(path);

                setSelectedLocationData({ path: fullPath, locationId, name, contentTypeIdentifier, language: previewableTranslations });
            }

            rootDOMElement.dispatchEvent(new CustomEvent('ibexa-content-items-view-refresh'));
        }
    };
    const callbackMoveElements = async (item, { selectedData }) => {
        const parsedEntires = selectedData.reduce(
            (data, entry) => {
                const { path: entryPath } = entry;
                const explodedPath = entryPath.split('/');
                const entryParentPath = explodedPath.slice(0, explodedPath.length - 1).join('/');

                if (item.nextParent.path !== entryParentPath) {
                    data.valid = [...data.valid, entry];
                } else {
                    data.invalid = [...data.invalid, entry];
                }

                return data;
            },
            { valid: [], invalid: [] },
        );

        if (parsedEntires.invalid.length !== 0) {
            const destinationErrorMsg = Translator.trans(
                /*@Desc("Can’t move %names%. Drop into a different location.")*/ 'quick_actions.validation_msgs.move_destination_error',
                {
                    names: parsedEntires.invalid.map((entry) => entry.name).join(', '),
                },
                'ibexa_image_picker',
            );

            showWarningNotification(destinationErrorMsg);

            return;
        }

        if (parsedEntires.valid.length === 0) {
            return;
        }

        const destination = `/${item.nextParent.path}`;
        const errorMsg = Translator.trans(
            /*@Desc("Can’t move %names%. Try again later.")*/ 'quick_actions.validation_msgs.move_error',
            {
                names: parsedEntires.valid.map((entry) => entry.name).join(', '),
            },
            'ibexa_image_picker',
        );

        setIsLoaded(false);

        const { success: movedItems, fail: notMovedItems } = await moveElements({ selectedData, destination, subitemsLoadLimit }, () =>
            actionErrorCallback(errorMsg),
        );

        if (notMovedItems.length) {
            showWarningNotification(errorMsg);
            setIsLoaded(true);
        }

        if (movedItems.length) {
            loadTreeToState();
        }
    };
    const loadMoreSubitems = (item) =>
        loadLocationItems({
            ...restInfo,
            parentLocationId: item.internalItem.locationId,
            ContentTypeIdentifier: adminUiConfig.damWidget.folder.contentTypeIdentifier,
            limit: subitemsLoadLimit,
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
            .catch(showErrorNotification);
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

        const emptyBadge = Translator.trans(/*@Desc("1")*/ 'content.1', {}, 'ibexa_image_picker');
        const emptyContent = Translator.trans(/*@Desc("Your tree is empty.")*/ 'tree.empty', {}, 'ibexa_image_picker');

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
    const getFullPath = (path) => {
        return rootLocationId === SYSTEM_ROOT_LOCATION_ID ? `/${path}/` : `${currentLocationPath}${path}/`;
    };
    const isActive = (item) => {
        if (!selectedLocationData.path) {
            return false;
        }

        const fullPath = getFullPath(item.path);

        return selectedLocationData.path === fullPath;
    };
    const onItemClick = (item) => {
        const { name, locationId, path, contentTypeIdentifier, mainLanguageCode } = item;
        const fullPath = getFullPath(path);

        setSelectedLocationData({ path: fullPath, locationId, name, contentTypeIdentifier, language: mainLanguageCode });
    };
    const buildItem = (item) =>
        item.internalItem
            ? item
            : {
                  internalItem: item,
                  id: item.locationId,
                  name: item.name,
                  path: item.path,
                  subitems: item.subitems,
                  total: item.totalSubitemsCount,
                  hidden: item.isInvisible,
                  renderLabel,
                  onItemClick: () => onItemClick(item),
                  customItemClass: getCustomItemClass(item),
                  isContainer: true,
                  actionsDisabled: item.locationId === SYSTEM_ROOT_LOCATION_ID,
              };
    const moduleName = Translator.trans(/*@Desc("Image picker folders browser")*/ 'tree_browser.module_name', {}, 'ibexa_image_picker');

    useEffect(() => {
        if (!currentLocationPath) {
            return;
        }

        const subtreeData = readSubtree();

        if (subtreeData) {
            subtree.current = subtreeData;
        }

        expandCurrentLocationInSubtree({ subtree: subtree.current, rootLocationId, currentLocationPath, subitemsLimit });
        clipTooDeepSubtreeBranches({ subtree: subtree.current[0], maxDepth: treeMaxDepth - 1 });

        subtree.current[0].children.forEach((subtreeChild) => limitSubitemsInSubtree({ subtree: subtreeChild, subitemsLimit }));
        saveSubtree();
        loadTreeToState();
    }, [currentLocationPath]);

    useEffect(() => {
        rootDOMElement.addEventListener('ibexa-content-tree-refresh', loadTreeToState);

        return () => {
            rootDOMElement.removeEventListener('ibexa-content-tree-refresh', loadTreeToState);
        };
    }, [currentLocationPath, rootLocationId]);

    return (
        <TreeBuilderModule
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
            callbackQuickEditElement={callbackQuickEditElement}
            callbackQuickCreateElement={callbackQuickCreateElement}
            callbackDeleteElements={callbackDeleteElements}
            callbackMoveElements={callbackMoveElements}
            subitemsLimit={subitemsLimit}
            treeDepthLimit={treeMaxDepth}
            dragDisabled={false}
            headerVisible={false}
            selectionDisabled={true}
            isLoading={!isLoaded}
            actionsType={ACTION_TYPE.CONTEXTUAL_MENU}
            moduleMenuActions={[
                {
                    priority: 20,
                    id: 'modifying',
                    subitems: [
                        {
                            priority: 10,
                            id: 'create',
                            component: QuickCreateContent,
                            fetchMethods: [loadLocationItemExtendedInfo],
                            visibleIn: [ACTION_PARENT.SINGLE_ITEM],
                        },
                        {
                            priority: 20,
                            id: 'rename',
                            component: QuickEditContent,
                            fetchMethods: [loadLocationItemExtendedInfo],
                            visibleIn: [ACTION_PARENT.SINGLE_ITEM],
                        },
                        {
                            priority: 30,
                            id: 'delete',
                            component: DeleteContent,
                            visibleIn: [ACTION_PARENT.SINGLE_ITEM],
                            fetchMethods: [loadLocationItemExtendedInfo],
                        },
                    ],
                },
            ]}
        >
            {renderEmpty()}
        </TreeBuilderModule>
    );
};

TreeBrowser.propTypes = {
    subitemsLimit: PropTypes.number,
    subitemsLoadLimit: PropTypes.number,
    treeMaxDepth: PropTypes.number,
    sort: PropTypes.shape({
        sortClause: PropTypes.string,
        sortOrder: PropTypes.string,
    }),
};

TreeBrowser.defaultProps = {
    subitemsLimit: ibexa?.adminUiConfig.contentTree.childrenLoadMaxLimit,
    subitemsLoadLimit: ibexa?.adminUiConfig.contentTree.loadMoreLimit,
    treeMaxDepth: ibexa?.adminUiConfig.contentTree.treeMaxDepth,
    sort: {},
};

export default TreeBrowser;
