import React, { useEffect, useState, useRef, useImperativeHandle, createContext, forwardRef, useCallback } from 'react';
import PropTypes from 'prop-types';

import { getRootDOMElement } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

import WidthContainer from './components/width-container/width.container';
import LocalStorageExpandConnector from './components/local-storage-expand-connector/local.storage.expand.connector';
import SelectedProvider from './components/selected-provider/selected.provider';
import PlaceholderProvider from './components/placeholder-provider/placeholder.provider';
import DndProvider from './components/dnd-provider/dnd.provider';
import IntermediateActionProvider from './components/intermediate-action-provider/intermediate.action.provider';
import Header from './components/header/header';
import Search from './components/search/search';
import List from './components/list/list';
import Loader from './components/loader/loader';
import { isItemEmpty } from './helpers/item';
import { getMenuActions as defaultGetMenuActions } from './helpers/tree';
import useDelayedChildrenSelectReducer from './hooks/useDelayedChildrenSelectReducer';

export const BuildItemContext = createContext();
export const MenuActionsContext = createContext();
export const CallbackContext = createContext();
export const ResizableContext = createContext();
export const ActiveContext = createContext();
export const DraggableDisabledContext = createContext();
export const LoadMoreSubitemsContext = createContext();
export const SubitemsLimitContext = createContext();
export const SelectedLimitContext = createContext();
export const TreeDepthLimitContext = createContext();
export const UserIdContext = createContext();
export const ModuleIdContext = createContext();
export const TreeContext = createContext();
export const DisabledItemContext = createContext();
export const DisabledItemInputContext = createContext();
export const SubIdContext = createContext();
export const ScrollWrapperContext = createContext();
export const DelayedChildrenSelectContext = createContext();
export const QuickActionsContext = createContext();

export const ACTION_TYPE = {
    LIST_MENU: 'LIST_MENU',
    CONTEXTUAL_MENU: 'CONTEXTUAL_MENU',
};
export const ACTION_PARENT = {
    TOP_MENU: 'TOP_MENU',
    SINGLE_ITEM: 'SINGLE_ITEM',
};
export const QUICK_ACTION_MODES = {
    CREATE: 'CREATE',
    EDIT: 'EDIT',
};

const { ibexa } = window;

const TreeBuilderModule = forwardRef(
    (
        {
            actionsType,
            actionsVisible,
            callbackAddElement,
            callbackCopyElements,
            callbackDeleteElements,
            callbackMoveElements,
            callbackToggleExpanded,
            callbackQuickEditElement,
            callbackQuickCreateElement,
            children,
            dragDisabled,
            getMenuActions,
            headerVisible,
            searchVisible,
            isActive,
            checkIsDisabled,
            checkIsInputDisabled,
            isResizable,
            loadMoreSubitems,
            moduleId,
            moduleName,
            selectedLimit,
            subitemsLimit,
            tree,
            treeDepthLimit,
            userId,
            initiallySelectedItemsIds,
            initiallyExpandedItems,
            buildItem,
            subId,
            renderHeaderContent,
            isLocalStorageActive,
            onSearchInputChange,
            initialSearchValue,
            extraClasses,
            isLoading,
            rootSelectionDisabled,
            selectionDisabled,
            extraBottomItems,
            rootElementDisabled,
            useTheme,
            moduleMenuActions,
        },
        ref,
    ) => {
        const rootDOMElement = getRootDOMElement();
        const treeNodeRef = useRef(null);
        const scrollWrapperRef = useRef(null);
        const localStorageExpandConnectorRef = useRef(null);
        const [actionsHeight, setActionsHeight] = useState(0);
        const [quickActionMode, setQuickActionMode] = useState(null);
        const [quickActionItemId, setQuickActionItemId] = useState(null);
        const [delayedChildrenSelectParentsIds, dispatchDelayedChildrenSelectAction] = useDelayedChildrenSelectReducer();
        const actionsCallbackRef = useCallback((node) => {
            const resizeObserver = new ResizeObserver((entries) => {
                setActionsHeight(entries[0].target.offsetHeight);
            });

            if (!node) {
                return;
            }

            setActionsHeight(node.offsetHeight ?? 0);
            resizeObserver.observe(node);
        }, []);
        const callbackContextData = {
            callbackAddElement,
            callbackCopyElements,
            callbackDeleteElements,
            callbackMoveElements,
            callbackToggleExpanded,
            callbackQuickEditElement,
            callbackQuickCreateElement,
        };
        const menuActionsContextData = {
            actionsType,
            actionsVisible,
            getMenuActions,
            actions: moduleMenuActions || ibexa?.treeBuilder?.[moduleId]?.menuActions || [],
        };
        const treeClassName = createCssClassNames({
            'c-tb-tree': true,
            'c-tb-tree--draggable': !dragDisabled,
            'c-tb-tree--no-header': !headerVisible,
            [extraClasses]: extraClasses !== '',
        });
        const renderHeader = () => {
            if (!headerVisible) {
                return null;
            }

            return <Header name={moduleName} renderContent={renderHeaderContent} />;
        };
        const renderSearch = () => {
            if (!searchVisible) {
                return null;
            }

            return <Search onSearchInputChange={onSearchInputChange} initialValue={initialSearchValue} />;
        };
        const renderContent = () => {
            if (isLoading) {
                return <Loader />;
            }

            return (
                <div ref={scrollWrapperRef} className="c-tb-tree__scrollable-wrapper" style={{ height: `calc(100% - ${actionsHeight}px)` }}>
                    <List
                        rootSelectionDisabled={rootSelectionDisabled}
                        rootElementDisabled={rootElementDisabled}
                        selectionDisabled={selectionDisabled}
                        isExpanded={true}
                        subitems={!isItemEmpty(tree) ? [tree] : []}
                        depth={rootElementDisabled ? -2 : -1}
                    />
                    {extraBottomItems.map((extraItem) => (
                        <List
                            key={extraItem.id}
                            selectionDisabled={true}
                            isExpanded={true}
                            subitems={[
                                {
                                    ...extraItem,
                                    subitems: [],
                                    total: 0,
                                },
                            ]}
                            depth={-1}
                        />
                    ))}
                    {children}
                </div>
            );
        };
        const triggerCreateAction = (event) => {
            const { itemId } = event.detail;

            setQuickActionMode(QUICK_ACTION_MODES.CREATE);
            setQuickActionItemId(itemId);
        };

        useEffect(() => {
            rootDOMElement.addEventListener('ibexa-tb-trigger-quick-action-create', triggerCreateAction, false);

            return () => {
                rootDOMElement.removeEventListener('ibexa-tb-trigger-quick-action-create', triggerCreateAction, false);
            };
        }, []);

        useImperativeHandle(ref, () => ({
            expandItems: (items) => localStorageExpandConnectorRef.current?.expandItems(items),
        }));

        /* eslint-disable max-len */
        return (
            <ModuleIdContext.Provider value={moduleId}>
                <UserIdContext.Provider value={userId}>
                    <SubIdContext.Provider value={subId}>
                        <ResizableContext.Provider value={{ isResizable }}>
                            <WidthContainer
                                moduleId={moduleId}
                                userId={userId}
                                scrollWrapperRef={scrollWrapperRef}
                                isLoaded={!isLoading}
                                useTheme={useTheme}
                            >
                                <BuildItemContext.Provider value={buildItem}>
                                    <IntermediateActionProvider>
                                        <ActiveContext.Provider value={isActive}>
                                            <DraggableDisabledContext.Provider value={dragDisabled}>
                                                <LoadMoreSubitemsContext.Provider value={loadMoreSubitems}>
                                                    <SubitemsLimitContext.Provider value={subitemsLimit}>
                                                        <SelectedLimitContext.Provider value={selectedLimit}>
                                                            <TreeDepthLimitContext.Provider value={treeDepthLimit}>
                                                                <MenuActionsContext.Provider value={menuActionsContextData}>
                                                                    <CallbackContext.Provider value={callbackContextData}>
                                                                        <DisabledItemContext.Provider value={checkIsDisabled}>
                                                                            <DisabledItemInputContext.Provider value={checkIsInputDisabled}>
                                                                                <SelectedProvider
                                                                                    initiallySelectedItemsIds={initiallySelectedItemsIds}
                                                                                >
                                                                                    <DelayedChildrenSelectContext.Provider
                                                                                        value={{
                                                                                            delayedChildrenSelectParentsIds,
                                                                                            dispatchDelayedChildrenSelectAction,
                                                                                        }}
                                                                                    >
                                                                                        <PlaceholderProvider>
                                                                                            <DndProvider
                                                                                                callbackMoveElements={callbackMoveElements}
                                                                                                treeRef={treeNodeRef}
                                                                                            >
                                                                                                <TreeContext.Provider value={tree}>
                                                                                                    <ScrollWrapperContext.Provider
                                                                                                        value={scrollWrapperRef}
                                                                                                    >
                                                                                                        <LocalStorageExpandConnector
                                                                                                            ref={
                                                                                                                localStorageExpandConnectorRef
                                                                                                            }
                                                                                                            initiallyExpandedItems={
                                                                                                                initiallyExpandedItems
                                                                                                            }
                                                                                                            isLocalStorageActive={
                                                                                                                isLocalStorageActive
                                                                                                            }
                                                                                                        >
                                                                                                            <div
                                                                                                                className={treeClassName}
                                                                                                                ref={treeNodeRef}
                                                                                                            >
                                                                                                                <div
                                                                                                                    className="c-tb-tree__actions"
                                                                                                                    ref={actionsCallbackRef}
                                                                                                                >
                                                                                                                    {renderHeader()}
                                                                                                                    {renderSearch()}
                                                                                                                </div>
                                                                                                                <QuickActionsContext.Provider
                                                                                                                    value={{
                                                                                                                        quickActionMode,
                                                                                                                        quickActionItemId,
                                                                                                                        setQuickActionMode,
                                                                                                                        setQuickActionItemId,
                                                                                                                    }}
                                                                                                                >
                                                                                                                    {renderContent()}
                                                                                                                </QuickActionsContext.Provider>
                                                                                                            </div>
                                                                                                        </LocalStorageExpandConnector>
                                                                                                    </ScrollWrapperContext.Provider>
                                                                                                </TreeContext.Provider>
                                                                                            </DndProvider>
                                                                                        </PlaceholderProvider>
                                                                                    </DelayedChildrenSelectContext.Provider>
                                                                                </SelectedProvider>
                                                                            </DisabledItemInputContext.Provider>
                                                                        </DisabledItemContext.Provider>
                                                                    </CallbackContext.Provider>
                                                                </MenuActionsContext.Provider>
                                                            </TreeDepthLimitContext.Provider>
                                                        </SelectedLimitContext.Provider>
                                                    </SubitemsLimitContext.Provider>
                                                </LoadMoreSubitemsContext.Provider>
                                            </DraggableDisabledContext.Provider>
                                        </ActiveContext.Provider>
                                    </IntermediateActionProvider>
                                </BuildItemContext.Provider>
                            </WidthContainer>
                        </ResizableContext.Provider>
                    </SubIdContext.Provider>
                </UserIdContext.Provider>
            </ModuleIdContext.Provider>
        );
        /* eslint-enable max-len */
    },
);

TreeBuilderModule.propTypes = {
    isActive: PropTypes.func.isRequired,
    moduleId: PropTypes.string.isRequired,
    userId: PropTypes.number.isRequired,
    loadMoreSubitems: PropTypes.func,
    moduleName: PropTypes.string,
    callbackAddElement: PropTypes.func,
    callbackCopyElements: PropTypes.func,
    callbackDeleteElements: PropTypes.func,
    callbackMoveElements: PropTypes.func,
    callbackToggleExpanded: PropTypes.func,
    callbackQuickEditElement: PropTypes.func,
    callbackQuickCreateElement: PropTypes.func,
    actionsType: PropTypes.string,
    actionsVisible: PropTypes.bool,
    buildItem: PropTypes.func,
    children: PropTypes.node,
    dragDisabled: PropTypes.bool,
    getMenuActions: PropTypes.func,
    headerVisible: PropTypes.bool,
    searchVisible: PropTypes.bool,
    checkIsDisabled: PropTypes.func,
    checkIsInputDisabled: PropTypes.func,
    isResizable: PropTypes.bool,
    selectedLimit: PropTypes.number,
    subitemsLimit: PropTypes.number,
    treeDepthLimit: PropTypes.number,
    tree: PropTypes.shape({
        total: PropTypes.number,
        label: PropTypes.string,
        renderLabel: PropTypes.func,
        id: PropTypes.string,
        subitems: PropTypes.array,
        dragItemDiasbled: PropTypes.bool,
        actionsDisabled: PropTypes.bool,
    }),
    initiallySelectedItemsIds: PropTypes.array,
    initiallyExpandedItems: PropTypes.array,
    subId: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
    renderHeaderContent: PropTypes.func,
    isLocalStorageActive: PropTypes.bool,
    onSearchInputChange: PropTypes.func,
    initialSearchValue: PropTypes.string,
    extraClasses: PropTypes.string,
    isLoading: PropTypes.bool,
    rootSelectionDisabled: PropTypes.bool,
    selectionDisabled: PropTypes.bool,
    extraBottomItems: PropTypes.array,
    rootElementDisabled: PropTypes.bool,
    useTheme: PropTypes.bool,
    moduleMenuActions: PropTypes.array,
};

TreeBuilderModule.defaultProps = {
    actionsType: null,
    actionsVisible: true,
    children: null,
    buildItem: (item) => item,
    callbackAddElement: () => {},
    callbackCopyElements: () => Promise.resolve(),
    callbackDeleteElements: () => Promise.resolve(),
    callbackMoveElements: () => Promise.resolve(),
    callbackQuickEditElement: () => Promise.resolve(),
    callbackQuickCreateElement: () => Promise.resolve(),
    callbackToggleExpanded: (item, { loadMore }) => loadMore(),
    dragDisabled: false,
    getMenuActions: ({ actions, item }) => defaultGetMenuActions({ actions, item }),
    headerVisible: true,
    searchVisible: false,
    checkIsDisabled: () => false,
    checkIsInputDisabled: () => false,
    isResizable: true,
    selectedLimit: null,
    subitemsLimit: null,
    treeDepthLimit: null,
    tree: null,
    initiallySelectedItemsIds: [],
    initiallyExpandedItems: null,
    subId: 'default',
    renderHeaderContent: null,
    isLocalStorageActive: true,
    onSearchInputChange: () => {},
    initialSearchValue: '',
    extraClasses: '',
    isLoading: false,
    rootSelectionDisabled: false,
    selectionDisabled: false,
    extraBottomItems: [],
    loadMoreSubitems: undefined,
    rootElementDisabled: false,
    moduleName: undefined,
    useTheme: false,
    moduleMenuActions: null,
};

TreeBuilderModule.displayName = 'TreeBuilderModule';

export default TreeBuilderModule;
