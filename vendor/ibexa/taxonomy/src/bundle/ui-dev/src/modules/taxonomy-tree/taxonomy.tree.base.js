import React, { forwardRef, useState, useEffect, useRef, useImperativeHandle } from 'react';

import deepClone from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/deep.clone.helper.js';
import { getData, saveData } from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/helpers/localStorage';
import { getAllChildren } from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/helpers/tree';

import { loadNode, loadTreeRoot, loadTree, loadSearchResults } from '../common/services/taxonomy.tree.service';
import { getSearchTreeProps } from '../common/helpers/search';
import { getLastElement } from '../common/helpers/array';
import { getTotal, getPath } from '../common/helpers/getters';
import { findItem } from '../common/helpers/tree';
import EmptyTree from '../common/components/empty-tree/empty.tree';

const { ibexa } = window;

const MIN_SEARCH_LENGTH = 1;

const TaxonomyTreeBase = forwardRef((props, ref) => {
    const {
        userId,
        taxonomyName,
        moduleId,
        subId,
        subtreeId,
        currentPath,
        renderLabel,
        getItemLink,
        treeBuilderExtraProps,
        languageCode,
        generateExtraBottomItems,
        useTheme,
    } = props;
    const [searchTree, setSearchTree] = useState(null);
    const [searchActive, setSearchActive] = useState(false);
    const [searchValue, setSearchValue] = useState('');
    const searchRequestTimeoutRef = useRef();
    const abortControllerSearchRef = useRef();
    const treeBuilderModuleRef = useRef(null);
    const [tree, setTree] = useState(null);
    const [isLoaded, setIsLoaded] = useState(false);
    const readSubtree = () => {
        return getData({ moduleId: moduleId, userId, subId: taxonomyName, dataId: subtreeId }) ?? [];
    };
    const subtree = useRef(readSubtree());
    const setInitialItemsState = (location) => {
        setIsLoaded(true);
        setTree(location);
    };
    const saveSubtree = () => {
        const data = [...new Set(subtree.current)];

        saveData({ moduleId: moduleId, userId, subId: taxonomyName, dataId: subtreeId, data });
    };
    const getLoadTreePromise = () => {
        if (subtree.current?.length) {
            const entryIds = subtree.current.map((entryPath) => getLastElement(entryPath.split('/')));
            const [currentId] = currentPath.split('/').slice(-1);

            entryIds.push(currentId);

            return loadTree({ taxonomyName, entryIds });
        }
        return loadTreeRoot({ taxonomyName });
    };
    const loadTreeToState = () => {
        return getLoadTreePromise()
            .then((rootResponse) => {
                const [rootItem] = rootResponse;

                setInitialItemsState(rootItem);

                const idsToExpand = currentPath
                    .split('/')
                    .slice(0, -1)
                    .map((id) => parseInt(id, 10));

                if (!idsToExpand.length) {
                    idsToExpand.push(parseInt(currentPath, 10));
                }

                const fakeItemsToExpand = idsToExpand.map((id) => ({ id }));

                treeBuilderModuleRef.current?.expandItems(fakeItemsToExpand);

                return rootItem;
            })
            .catch(window.ibexa.helpers.notification.showErrorNotification);
    };
    const loadMoreSubitems = (item) => {
        return loadNode({ taxonomyName, entryId: item.id })
            .then((nodeResponse) => {
                const children = nodeResponse.__children;
                const treeItem = findItem([tree], item.path.split('/'));

                if (treeItem) {
                    const nextTree = deepClone(tree);
                    const nextTreeItem = findItem([nextTree], item.path.split('/'));

                    nextTreeItem.__children = children;

                    setTree(nextTree);
                }

                return children;
            })
            .catch(window.ibexa.helpers.notification.showErrorNotification);
    };
    const callbackToggleExpanded = (item, { isExpanded, loadMore }) => {
        const regexp = new RegExp(`/?${item.id}$`, 'g');
        const parentPath = item.path.replace(regexp, '');

        if (isExpanded) {
            subtree.current = subtree.current.filter((entryPath) => entryPath !== parentPath);

            if (!subtree.current.includes(item.path)) {
                subtree.current.push(item.path);
            }

            const shouldLoadInitialItems = item.subitems && !item.subitems.length;

            if (shouldLoadInitialItems) {
                loadMore();
            }
        } else {
            subtree.current = subtree.current.filter((entryPath) => entryPath.indexOf(item.path) !== 0);

            if (parentPath && !subtree.current.includes(item.path)) {
                subtree.current.push(parentPath);
            }
        }

        saveSubtree();
    };
    const renderEmpty = () => {
        if (!isLoaded || tree?.id !== undefined) {
            return null;
        }

        return <EmptyTree />;
    };
    const buildItem = (item, restProps) =>
        item.internalItem
            ? item
            : {
                  internalItem: item,
                  id: item.id,
                  href: getItemLink(item),
                  path: getPath(item, restProps),
                  subitems: item.__children,
                  total: getTotal(item),
                  renderLabel: (renderItem, params, ...args) => renderLabel(renderItem, { ...params, searchActive, searchValue }, ...args),
                  customItemClass: 'c-tt-list-item',
                  isContainer: !!getTotal(item),
              };
    const onItemsMoved = loadTreeToState;
    const expandAllItems = (rootItem) => {
        const hasSubitems = ({ subitems }) => !!subitems && subitems.length;
        const allChildren = getAllChildren({ data: rootItem, buildItem, condition: hasSubitems });

        treeBuilderModuleRef.current?.expandItems(allChildren);
    };
    const onSearchInputChange = (inputValue) => {
        setSearchValue(inputValue.trim());
    };

    useEffect(() => {
        abortControllerSearchRef.current?.abort();
        clearTimeout(searchRequestTimeoutRef.current);
        searchRequestTimeoutRef.current = setTimeout(() => {
            if (searchValue.length >= MIN_SEARCH_LENGTH) {
                abortControllerSearchRef.current = new AbortController();
                loadSearchResults({
                    taxonomyName,
                    searchValue,
                    languageCode,
                    signal: abortControllerSearchRef.current.signal,
                }).then((rootResponse) => {
                    if (!rootResponse?.length) {
                        setSearchTree(null);
                    } else {
                        const [rootItem] = rootResponse;
                        setSearchTree(rootItem);
                        expandAllItems(rootItem);
                    }
                    setSearchActive(true);
                });
            } else {
                setSearchTree(null);
                setSearchActive(false);
            }
        }, 100);
        return () => {
            clearTimeout(searchRequestTimeoutRef.current);
        };
    }, [searchValue]);

    useImperativeHandle(
        ref,
        () => {
            return {
                onItemsMoved,
                expandAllItems,
            };
        },
        [],
    );

    useEffect(() => {
        let parentPath = currentPath.split('/').slice(0, -1).join('/');

        if (!parentPath) {
            parentPath = currentPath;
        }

        if (parentPath && !subtree.current.includes(parentPath)) {
            subtree.current.push(parentPath);

            saveSubtree();
        }

        loadTreeToState();
    }, []);

    return (
        <ibexa.modules.TreeBuilder
            ref={treeBuilderModuleRef}
            moduleId={moduleId}
            userId={userId}
            subId={subId}
            tree={tree}
            buildItem={buildItem}
            loadMoreSubitems={loadMoreSubitems}
            callbackToggleExpanded={callbackToggleExpanded}
            isLoading={!isLoaded}
            searchVisible={true}
            onSearchInputChange={onSearchInputChange}
            initialSearchValue={searchValue}
            extraBottomItems={generateExtraBottomItems({ searchActive, searchValue })}
            useTheme={useTheme}
            {...treeBuilderExtraProps}
            {...getSearchTreeProps({ searchTree, searchActive })}
        >
            {renderEmpty()}
        </ibexa.modules.TreeBuilder>
    );
});

TaxonomyTreeBase.displayName = 'TaxonomyTreeBase';

TaxonomyTreeBase.propTypes = {
    userId: PropTypes.number.isRequired,
    taxonomyName: PropTypes.string.isRequired,
    moduleId: PropTypes.string.isRequired,
    subId: PropTypes.string.isRequired,
    subtreeId: PropTypes.string.isRequired,
    currentPath: PropTypes.string.isRequired,
    renderLabel: PropTypes.func.isRequired,
    getItemLink: PropTypes.func.isRequired,
    treeBuilderExtraProps: PropTypes.object.isRequired,
    languageCode: PropTypes.string.isRequired,
    generateExtraBottomItems: PropTypes.func,
    useTheme: PropTypes.bool,
};

TaxonomyTreeBase.defaultProps = {
    generateExtraBottomItems: () => [],
    useTheme: false,
};

export default TaxonomyTreeBase;
