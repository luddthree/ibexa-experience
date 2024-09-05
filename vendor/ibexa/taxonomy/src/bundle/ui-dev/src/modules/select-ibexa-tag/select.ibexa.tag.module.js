import React, { useState, useEffect, useRef } from 'react';

import deepClone from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/deep.clone.helper.js';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';

import { getAllChildren } from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/helpers/tree';

import { loadSearchResults, loadNode, loadTreeRoot, loadTree } from '../common/services/taxonomy.tree.service';
import { getTotal, getPath } from '../common/helpers/getters';
import { findItem, getExpandedItems } from '../common/helpers/tree';
import { hasTranslation, getTranslatedName } from '../common/helpers/languages';
import { getSearchTreeProps } from '../common/helpers/search';
import EmptyTree from '../common/components/empty-tree/empty.tree';
import NotTranslatedInfo from '../common/components/not-translated-info/not.translated.info';
import NameContent from '../common/components/name-content/name-content';

const { ibexa, Translator } = window;
const MIN_SEARCH_LENGTH = 1;

const SelectIbexaTag = (props) => {
    const {
        moduleId,
        userId,
        selectedItems,
        taxonomyName,
        taxonomyEntryId,
        isMultiChoice,
        languageCode,
        isSearchVisible,
        rootSelectionDisabled,
    } = props;
    const treeBuilderModuleRef = useRef(null);
    const [tree, setTree] = useState(null);
    const [searchTree, setSearchTree] = useState(null);
    const [searchActive, setSearchActive] = useState(false);
    const [searchValue, setSearchValue] = useState('');
    const [isLoaded, setIsLoaded] = useState(false);
    const [showTranslationWarning, setShowTranslationWarning] = useState(false);
    const searchRequestTimeoutRef = useRef();
    const abortControllerSearchRef = useRef();
    const setInitialItemsState = (location) => {
        setIsLoaded(true);
        setTree(location);
    };
    const setTranslationWarningVisibility = (treeNode) => {
        if (!treeNode) {
            return false;
        }

        const expandedItems = getExpandedItems(treeNode);
        const visibleNodes = [];

        expandedItems.forEach((expandedItem) => {
            visibleNodes.push(expandedItem, ...expandedItem.__children);
        });

        setShowTranslationWarning(visibleNodes.some((node) => !hasTranslation(node, languageCode)));
    };
    const loadTreeToState = () => {
        if (selectedItems.length) {
            return loadTree({ taxonomyName, entryIds: getSelectedItemsIds() })
                .then((rootResponse) => {
                    const [rootItem] = rootResponse;
                    const expandedItems = getExpandedItems(rootItem);

                    setInitialItemsState(rootItem);
                    setTranslationWarningVisibility(rootItem);
                    treeBuilderModuleRef.current?.expandItems(expandedItems);

                    return rootItem;
                })
                .catch(ibexa.helpers.notification.showErrorNotification);
        }

        return loadTreeRoot({ taxonomyName })
            .then((rootResponse) => {
                const [rootItem] = rootResponse;

                return loadNode({ taxonomyName, entryId: rootItem.id }).then((nodeResponse) => {
                    rootItem.__children = nodeResponse.__children;

                    setInitialItemsState(rootItem);
                    setTranslationWarningVisibility(rootItem);
                    treeBuilderModuleRef.current?.expandItems([buildItem(rootItem)]);

                    return rootItem;
                });
            })
            .catch(ibexa.helpers.notification.showErrorNotification);
    };
    const renderLabel = (item) => {
        const { internalItem } = item;
        const { id } = internalItem;
        const name = getTranslatedName(item.internalItem, languageCode);

        return (
            <label htmlFor={`ibexa-tb-row-selected-${moduleId}-${id}`} className="c-tt-list-item__link">
                <NameContent searchActive={searchActive} searchValue={searchValue} name={name} />
                {!hasTranslation(internalItem, languageCode) && (
                    <Icon name="warning-triangle" extraClasses="ibexa-icon--small c-tt-list-item__icon-warning" />
                )}
            </label>
        );
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
                    setTranslationWarningVisibility(nextTree);
                }

                return children;
            })
            .catch(window.ibexa.helpers.notification.showErrorNotification);
    };
    const renderEmpty = () => {
        if (!isLoaded || tree?.id !== undefined) {
            return null;
        }

        return <EmptyTree />;
    };
    const buildItem = (item, restProps = {}) =>
        item.internalItem
            ? item
            : {
                  internalItem: item,
                  id: item.id,
                  path: getPath(item, restProps),
                  subitems: item.__children,
                  total: getTotal(item),
                  renderLabel,
                  customItemClass: 'c-tt-list-item',
              };
    const checkIsInputDisabled = (item) => {
        const unavailableEntriesIds = item.path.split('/').map((id) => parseInt(id, 10));

        return unavailableEntriesIds.includes(taxonomyEntryId);
    };
    const getSelectedItemsIds = () => selectedItems.map((item) => item.id);
    const moduleName = Translator.trans(/*@Desc("Taxonomy tree")*/ 'taxonomy.tree_name', {}, 'ibexa_taxonomy_ui');
    const onSearchInputChange = (inputValue) => {
        setSearchValue(inputValue.trim());
    };

    useEffect(() => {
        loadTreeToState();
    }, []);

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

                        const hasSubitems = ({ subitems }) => !!subitems && subitems.length;
                        const allChildren = getAllChildren({ data: rootItem, buildItem, condition: hasSubitems });

                        treeBuilderModuleRef.current?.expandItems(allChildren);
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

    return (
        <ibexa.modules.TreeBuilder
            ref={treeBuilderModuleRef}
            moduleId={moduleId}
            moduleName={moduleName}
            userId={userId}
            subId={taxonomyName}
            tree={tree}
            buildItem={buildItem}
            checkIsInputDisabled={checkIsInputDisabled}
            isActive={() => false}
            loadMoreSubitems={loadMoreSubitems}
            selectedLimit={isMultiChoice ? undefined : 1}
            dragDisabled={true}
            isResizable={false}
            headerVisible={false}
            actionsVisible={false}
            initiallySelectedItemsIds={selectedItems ? getSelectedItemsIds() : []}
            onSearchInputChange={onSearchInputChange}
            initialSearchValue={searchValue}
            searchVisible={isSearchVisible}
            isLoading={!isLoaded}
            rootSelectionDisabled={rootSelectionDisabled}
            {...getSearchTreeProps({ searchTree, searchActive })}
        >
            {renderEmpty()}
            {showTranslationWarning && <NotTranslatedInfo languageCode={languageCode} />}
        </ibexa.modules.TreeBuilder>
    );
};

SelectIbexaTag.propTypes = {
    moduleId: PropTypes.string.isRequired,
    userId: PropTypes.number.isRequired,
    restInfo: PropTypes.shape({
        token: PropTypes.string.isRequired,
        siteaccess: PropTypes.string.isRequired,
    }).isRequired,
    taxonomyName: PropTypes.string.isRequired,
    selectedItems: PropTypes.array,
    isMultiChoice: PropTypes.bool,
    languageCode: PropTypes.string,
    taxonomyEntryId: PropTypes.string,
    isSearchVisible: PropTypes.bool,
    rootSelectionDisabled: PropTypes.bool,
};

SelectIbexaTag.defaultProps = {
    selectedItems: null,
    isMultiChoice: false,
    languageCode: window.ibexa.adminUiConfig.languages.priority[0],
    taxonomyEntryId: null,
    isSearchVisible: true,
    rootSelectionDisabled: false,
};

export default SelectIbexaTag;
