import React, { useState, useContext, useEffect, useRef, useCallback } from 'react';
import PropTypes from 'prop-types';

import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';

import List from '../list/list';
import Toggler from '../toggler/toggler';
import LoadMore from '../load-more/load.more';
import Limit from '../limit/limit';
import ContextualMenu from '../contextual-menu/contextual.menu';
import ListMenu from '../list-menu/list.menu';
import IndentationHorizontal from '../indentation-horizontal/indentation.horizontal';

import { ExpandContext } from '../local-storage-expand-connector/local.storage.expand.connector';
import { SelectedContext } from '../selected-provider/selected.provider';
import { PlaceholderContext } from '../placeholder-provider/placeholder.provider';
import { DraggableContext } from '../dnd-provider/dnd.provider';
import { WidthContainerContext, TreeFullWidthContext } from '../width-container/width.container';
import { IntermediateActionContext } from '../intermediate-action-provider/intermediate.action.provider';
import {
    STORED_ITEMS_REPLACE,
    STORED_ITEMS_TOGGLE,
    STORED_ITEMS_REMOVE,
    STORED_ITEMS_SET,
    STORED_ITEMS_ADD,
} from '../../hooks/useStoredItemsReducer';
import useDidUpdateEffect from '../../hooks/useDidUpdateEffect';
import { isItemStored } from '../../helpers/item';
import {
    ModuleIdContext,
    ActiveContext,
    DraggableDisabledContext,
    LoadMoreSubitemsContext,
    MenuActionsContext,
    CallbackContext,
    SelectedLimitContext,
    TreeDepthLimitContext,
    DisabledItemContext,
    DisabledItemInputContext,
    ScrollWrapperContext,
    ACTION_TYPE,
    ACTION_PARENT,
    DelayedChildrenSelectContext,
    BuildItemContext,
} from '../../tree.builder.module';
import { DELAYED_CHILDREN_SELECT_REMOVE } from '../../hooks/useDelayedChildrenSelectReducer';
import { getAllChildren } from '../../helpers/tree';
import { getBootstrap, getTranslator } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';
import { showWarningNotification } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/notification.helper';
import { parse as parseTooltip } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/tooltips.helper';
import {
    QUICK_ACTION_MODES,
    QuickActionsContext,
} from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/tree.builder.module';

const START_DRAG_TIMEOUT = 300;

const isSelectedLimitReached = (selectedLimit, selectedData) => {
    if (selectedLimit === null || selectedLimit === 1) {
        return false;
    }

    return selectedData.length >= selectedLimit;
};

const ListItemSingle = ({
    index,
    isRoot,
    item,
    itemDepth,
    parents,
    rootSelectionDisabled,
    selectionDisabled,
    rootElementDisabled,
    showHighlight,
    isLastItem,
}) => {
    const Translator = getTranslator();
    const bootstrap = getBootstrap();
    const isActive = useContext(ActiveContext);
    const { callbackToggleExpanded, callbackQuickEditElement, callbackQuickCreateElement } = useContext(CallbackContext);
    const checkIsDisabled = useContext(DisabledItemContext);
    const checkIsInputDisabled = useContext(DisabledItemInputContext);
    const { startDragging, stopDragging, isDragging } = useContext(DraggableContext);
    const dragDisabled = useContext(DraggableDisabledContext);
    const { expandedData, dispatchExpandedData } = useContext(ExpandContext);
    const { intermediateAction } = useContext(IntermediateActionContext);
    const loadMoreSubitems = useContext(LoadMoreSubitemsContext);
    const { actionsType, actionsVisible } = useContext(MenuActionsContext);
    const moduleId = useContext(ModuleIdContext);
    const { mouseMove } = useContext(PlaceholderContext);
    const { selectedData, dispatchSelectedData } = useContext(SelectedContext);
    const selectedLimit = useContext(SelectedLimitContext);
    const treeDepthLimit = useContext(TreeDepthLimitContext);
    const saveTreeFullWidth = useContext(TreeFullWidthContext);
    const [widthContainerData] = useContext(WidthContainerContext);
    const scrollWrapperRef = useContext(ScrollWrapperContext);
    const { delayedChildrenSelectParentsIds, dispatchDelayedChildrenSelectAction } = useContext(DelayedChildrenSelectContext);
    const buildItem = useContext(BuildItemContext);
    const { quickActionMode, quickActionItemId, setQuickActionMode, setQuickActionItemId } = useContext(QuickActionsContext);
    const isWaitingForDrag = useRef(false);
    const itemRef = useRef(null);
    const alreadyScrolledToInitialPosition = useRef(false);
    const quickEditInputRef = useRef(false);
    const quickCreateInputRef = useRef(false);
    const rootElementHidden = rootElementDisabled && itemDepth === -1;
    const dragItemDisabled = dragDisabled || (item.dragItemDisabled ?? false);
    const actionsDisabled = item.actionsDisabled ?? false;
    const isItemActive = isActive(item);
    const scrollToElementRef = (node) => {
        itemRef.current = node;

        if (isItemActive && scrollWrapperRef.current && node && !alreadyScrolledToInitialPosition.current) {
            alreadyScrolledToInitialPosition.current = true;

            const scrollWrapperTop = scrollWrapperRef.current.getBoundingClientRect().top;
            const itemTop = node.getBoundingClientRect().top;
            const offset = itemTop - scrollWrapperTop;

            scrollWrapperRef.current.scrollTo(0, offset);
        }
    };
    const labelTruncatedCallbackRef = useCallback(
        (node) => {
            if (node) {
                const tooltipInstance = bootstrap.Tooltip.getInstance(node);

                if (!tooltipInstance) {
                    return;
                }

                if (node.scrollWidth <= node.offsetWidth) {
                    tooltipInstance.disable();
                } else {
                    tooltipInstance.enable();

                    saveTreeFullWidth(node.scrollWidth - node.offsetWidth);
                }

                parseTooltip(node);
            }
        },
        [widthContainerData.containerWidth],
    );
    const [isHovered, setIsHovered] = useState(false);
    const [updatedName, setUpdatedName] = useState(item.name);
    const [createdName, setCreatedName] = useState('');
    const [isContextMenuOpened, setIsContextMenuOpened] = useState(false);
    const [isLoading, setIsLoading] = useState(false);
    const isQuickEditModeEnabled = quickActionItemId === item.id && quickActionMode === QUICK_ACTION_MODES.EDIT;
    const isQuickCreateModeEnabled = quickActionItemId === item.id && quickActionMode === QUICK_ACTION_MODES.CREATE;
    const areActionsDisabled = intermediateAction.isActive || item.internalItem.areActionsDisabled;
    const isDisabled = intermediateAction.checkIsDisabled?.(item, { parents }) || checkIsDisabled(item, { intermediateAction });
    const isExpanded = isItemStored(item, expandedData);
    const isSelected = isItemStored(item, selectedData);
    const isEqualItem = (itemToCompare) => itemToCompare.id === item.id;
    const isDestination =
        intermediateAction.isActive && intermediateAction.highlightDestination && intermediateAction.listItems.some(isEqualItem);
    const parent = parents[parents.length - 1];
    const quickCreatePlaceholder = Translator.trans(/*@Desc("New item")*/ 'quick_actions.create.placeholder', {}, 'ibexa_tree_builder_ui');
    const getCheckboxTooltip = () => {
        if (!isSelectedLimitReached(selectedLimit, selectedData)) {
            return null;
        }

        return Translator.trans(
            /*@Desc("You cannot select more than %selectedLimit% items.")*/ 'checkbox.limit.message',
            { selectedLimit },
            'ibexa_tree_builder_ui',
        );
    };
    const hoverIn = () => {
        setIsHovered(true);

        if (item.onItemHoverIn) {
            item.onItemHoverIn();
        }
    };
    const hoverOut = () => {
        setIsHovered(false);

        if (item.onItemHoverOut) {
            item.onItemHoverOut();
        }
    };
    const loadMore = () => {
        if (!loadMoreSubitems) {
            return;
        }

        setIsLoading(true);

        return loadMoreSubitems(item).then((response) => {
            setIsLoading(false);

            return response;
        });
    };
    const onLabelClick = (event) => {
        if (isDisabled) {
            event.preventDefault();

            return;
        }

        if (intermediateAction.isActive) {
            event.preventDefault();

            if (intermediateAction.callback) {
                intermediateAction.callback(item);
            }

            return;
        }

        if (item.onItemClick) {
            item.onItemClick();
        }
    };
    const renderActions = () => {
        switch (actionsType) {
            case ACTION_TYPE.LIST_MENU:
                return <ListMenu item={item} isDisabled={areActionsDisabled} parent={ACTION_PARENT.SINGLE_ITEM} />;
            case ACTION_TYPE.CONTEXTUAL_MENU:
                return (
                    <ContextualMenu
                        item={item}
                        isDisabled={areActionsDisabled}
                        parent={ACTION_PARENT.SINGLE_ITEM}
                        isExpanded={isContextMenuOpened}
                        setIsExpanded={setIsContextMenuOpened}
                        scrollWrapperRef={scrollWrapperRef}
                    />
                );
            default:
                return null;
        }
    };
    const renderActionsWrapper = () => {
        if (!actionsVisible || actionsDisabled) {
            return null;
        }

        return <div className="c-tb-list-item-single__actions">{renderActions()}</div>;
    };
    const renderQuickEditAction = () => {
        const triggerRename = () => {
            callbackQuickEditElement(item, updatedName);
            setQuickActionMode(null);
            setQuickActionItemId(null);
        };
        const cancelRename = () => {
            setUpdatedName(item.name);
            setQuickActionMode(null);
            setQuickActionItemId(null);
        };

        return (
            <div className="c-tb-list-item-single__quick-actions-wrapper c-tb-list-item-single__quick-actions-wrapper--edit">
                <div className="c-tb-list-item-single__quick-action">
                    <Icon name="folder" extraClasses="ibexa-icon--small" />
                    <input
                        ref={quickEditInputRef}
                        type="text"
                        className="c-tb-list-item-single__quick-action-input c-tb-list-item-single__quick-action-input--edit form-control ibexa-input ibexa-input--text"
                        value={updatedName}
                        onChange={(event) => setUpdatedName(event.currentTarget.value)}
                        onKeyUp={(event) => {
                            const { code } = event;

                            if (code === 'Escape') {
                                cancelRename();
                            }

                            if (code === 'Enter') {
                                triggerRename();
                            }
                        }}
                        onBlur={() => triggerRename()}
                    />
                </div>
            </div>
        );
    };
    const renderQuickCreateAction = () => {
        const triggerCreate = () => {
            callbackQuickCreateElement(item.internalItem, createdName);
            setCreatedName('');
            setQuickActionMode(null);
            setQuickActionItemId(null);
        };
        const cancelCreate = () => {
            setCreatedName('');
            setQuickActionMode(null);
            setQuickActionItemId(null);
        };

        return (
            <div className="c-tb-list-item-single__quick-actions-wrapper c-tb-list-item-single__quick-actions-wrapper--create">
                {renderIndentationHorizontal()}
                <div className="c-tb-list-item-single__quick-action">
                    <Icon name="folder" extraClasses="ibexa-icon--small" />
                    <input
                        ref={quickCreateInputRef}
                        type="text"
                        className="c-tb-list-item-single__quick-action-input c-tb-list-item-single__quick-action-input--create form-control ibexa-input ibexa-input--text"
                        placeholder={quickCreatePlaceholder}
                        value={createdName}
                        onChange={(event) => setCreatedName(event.currentTarget.value)}
                        onKeyUp={(event) => {
                            const { code } = event;

                            if (code === 'Escape') {
                                cancelCreate();
                            }

                            if (code === 'Enter') {
                                triggerCreate();
                            }
                        }}
                        onBlur={() => triggerCreate()}
                    />
                </div>
            </div>
        );
    };
    const renderDragIcon = () => {
        const dragIconClass = createCssClassNames({
            'ibexa-icon--tiny-small': true,
            'c-tb-list-item-single__drag-icon': true,
            'c-tb-list-item-single__drag-icon--hidden': dragItemDisabled,
        });

        return <Icon name="drag" extraClasses={dragIconClass} />;
    };
    const renderIndentationVerticalLine = () => {
        const indentationClass = createCssClassNames({
            'c-tb-list-item-single__indentation-line': true,
            'c-tb-list-item-single__indentation-line--vertical': true,
        });

        if (!isExpanded || item.total === 0) {
            return null;
        }

        return <div className={indentationClass} style={{ '--indent': itemDepth }} />;
    };
    const renderIndentationHorizontal = () => {
        if (isRoot && rootSelectionDisabled) {
            return null;
        }

        return <IndentationHorizontal itemDepth={itemDepth} />;
    };
    const startDraggingItem = (event) => {
        if (dragItemDisabled) {
            return;
        }

        dispatchExpandedData({ items: [item], type: STORED_ITEMS_REMOVE });
        startDragging(event, { item, parent, index, target: itemRef.current });
    };
    const stopDraggingItem = (event) => {
        if (!isDragging) {
            return;
        }

        stopDragging(event);
    };
    const handleMouseMove = (event) => {
        if (!isDragging) {
            return;
        }

        mouseMove(event, { item, parent, index, isExpanded, isDisabled });
    };
    const handleMouseDown = (event) => {
        const { target } = event;

        if (target.classList.contains('c-tb-list-item-single__quick-action-input--edit')) {
            return;
        }

        event.preventDefault();

        if (event.button !== 0) {
            return;
        }

        isWaitingForDrag.current = true;

        setTimeout(() => {
            if (isWaitingForDrag.current) {
                startDraggingItem(event);
            }
        }, START_DRAG_TIMEOUT);
    };
    const handleMouseUp = (event) => {
        isWaitingForDrag.current = false;

        stopDraggingItem(event);
    };
    const handleMouseLeave = (event) => {
        if (isWaitingForDrag.current) {
            startDraggingItem(event);

            isWaitingForDrag.current = false;

            return;
        }
    };
    const getIconChoice = () => {
        if (item.renderIcon && !isHovered && !isSelected) {
            return item.renderIcon(item, { isLoading, labelTruncatedCallbackRef });
        }

        return renderSelectInput();
    };
    const getLabel = () => {
        if (item.label) {
            return item.label;
        }

        if (item.renderLabel) {
            return item.renderLabel(item, { isLoading, labelTruncatedCallbackRef });
        }

        return '';
    };
    const getHiddenInfo = () => {
        if (!item.hidden) {
            return null;
        }

        const hiddenIconClass = createCssClassNames({
            'ibexa-icon--small': true,
            'c-tb-list-item-single__hidden-icon': true,
        });

        return <Icon name="view-hide" extraClasses={hiddenIconClass} />;
    };
    const renderLabel = () => {
        const labelProps = {
            className: 'c-tb-list-item-single__label',
            onClick: onLabelClick,
        };
        const label = getLabel();

        if (!item.href) {
            return (
                <div {...labelProps}>
                    {getIconChoice()}
                    {label}
                    {getHiddenInfo()}
                </div>
            );
        }

        return (
            <div {...labelProps}>
                {getIconChoice()}
                <a className="c-tb-list-item-single__link" href={item.href}>
                    {label}
                    {getHiddenInfo()}
                </a>
            </div>
        );
    };
    const renderSelectInput = () => {
        const inputType = selectedLimit === 1 ? 'radio' : 'checkbox';
        const isInputDisabled =
            !isSelected && (areActionsDisabled || isSelectedLimitReached(selectedLimit, selectedData) || checkIsInputDisabled(item));

        if ((isRoot && rootSelectionDisabled) || selectionDisabled) {
            return null;
        }

        return (
            <input
                type={inputType}
                id={`ibexa-tb-row-selected-${moduleId}-${item.id}`}
                className={`ibexa-input ibexa-input--${inputType}`}
                onChange={toggleSelectInput}
                checked={isSelected}
                disabled={isInputDisabled}
                title={getCheckboxTooltip()}
            />
        );
    };
    const updateIndentationLine = (itemRefElement, indentHeight) => {
        const childList = itemRefElement.querySelector(':scope > .c-tb-list');

        if (!childList || !childList.hasChildNodes()) {
            return indentHeight;
        }

        const lastChild = childList.lastElementChild.firstElementChild;
        const lastChildHasChildren = lastChild.classList.contains('c-tb-list-item-single--has-sub-items');

        indentHeight += lastChild.offsetTop;

        if (lastChildHasChildren) {
            indentHeight = updateIndentationLine(lastChild, indentHeight);
        }

        return indentHeight;
    };
    const setIndentHeight = () => {
        let newIndentHeight = itemRef.current.offsetTop;
        const itemHasChildren = itemRef.current.classList.contains('c-tb-list-item-single--has-sub-items');
        const parentElement = itemRef.current.parentElement.closest('.c-tb-list-item-single--has-sub-items');
        const indentationLine = parentElement?.querySelector('.c-tb-list-item-single__indentation-line--vertical');

        if (itemHasChildren) {
            newIndentHeight = updateIndentationLine(itemRef.current, newIndentHeight);
        }

        if (indentationLine) {
            indentationLine.style.height = `${newIndentHeight}px`;
        }
    };
    const toggleSelectInput = () => {
        const actionType = selectedLimit === 1 ? STORED_ITEMS_SET : STORED_ITEMS_TOGGLE;

        dispatchSelectedData({ items: [item], type: actionType });
    };
    const toggleExpanded = () => {
        if (treeDepthLimit !== null && itemDepth >= treeDepthLimit) {
            const notificationMessage = Translator.trans(
                /*@Desc("Cannot load sub-items for this Location because you reached max tree depth.")*/ 'expand_item.limit.message',
                {},
                'ibexa_tree_builder_ui',
            );

            showWarningNotification(notificationMessage);

            return;
        }

        dispatchExpandedData({ items: [item], type: STORED_ITEMS_TOGGLE });
    };
    const itemAttrs = { ...item.customAttrs };

    useDidUpdateEffect(() => {
        callbackToggleExpanded(item, { isExpanded, loadMore });
    }, [isExpanded]);

    useEffect(() => {
        if (item.subitems?.length === 0 && isExpanded) {
            dispatchExpandedData({ items: [item], type: STORED_ITEMS_REMOVE });
        }
    }, [item.subitems?.length]);

    useEffect(() => {
        dispatchSelectedData({ items: [item], type: STORED_ITEMS_REPLACE });
    }, []);

    useEffect(() => {
        const shouldSelectChildren = delayedChildrenSelectParentsIds.includes(item.id);
        const areChildrenAlreadyLoaded = !!item.subitems?.length;

        if (shouldSelectChildren && areChildrenAlreadyLoaded) {
            const allItems = getAllChildren({ data: item, buildItem, condition: (subitem) => subitem.id !== item.id });
            const items = selectedLimit ? allItems.slice(0, selectedLimit) : allItems;

            dispatchDelayedChildrenSelectAction({ type: DELAYED_CHILDREN_SELECT_REMOVE, parentId: item.id });
            dispatchSelectedData({ type: STORED_ITEMS_ADD, items });
        }

        if (isLastItem && !isRoot && !rootElementDisabled) {
            setIndentHeight();
        }
    });

    useEffect(() => {
        if (isQuickCreateModeEnabled) {
            quickCreateInputRef.current.focus();

            if (!isExpanded && item.total) {
                toggleExpanded();
            }
        }

        if (isQuickEditModeEnabled) {
            quickEditInputRef.current.focus();
        }
    }, [isQuickCreateModeEnabled, isQuickEditModeEnabled]);

    useEffect(() => {
        setUpdatedName(item.name);
    }, [item.name]);

    itemAttrs.className = createCssClassNames({
        'c-tb-list-item-single': true,
        'c-tb-list-item-single--has-sub-items': item.total,
        'c-tb-list-item-single--hovered':
            isHovered && !isDragging && !isQuickEditModeEnabled && !isQuickCreateModeEnabled && !(dragItemDisabled && actionsDisabled),
        'c-tb-list-item-single--highlighted': showHighlight,
        'c-tb-list-item-single--clickable': item.href || item.onItemClick,
        'c-tb-list-item-single--disabled': isDisabled,
        'c-tb-list-item-single--expanded': isExpanded,
        'c-tb-list-item-single--active': isItemActive,
        'c-tb-list-item-single--hidden': item.hidden,
        'c-tb-list-item-single--destination': isDestination,
        'c-tb-list-item-single--context-menu-opened': isContextMenuOpened,
        'c-tb-list-item-single--draggable': !dragItemDisabled,
        'c-tb-list-item-single--action-and-drag-disabled': dragItemDisabled && actionsDisabled,
        'c-tb-list-item-single--quick-edit-mode': isQuickEditModeEnabled,
        'c-tb-list-item-single--quick-create-mode': isQuickCreateModeEnabled,
        [item.customItemClass]: !!item.customItemClass,
    });

    return (
        <div {...itemAttrs} ref={scrollToElementRef}>
            {!rootElementHidden && (
                <>
                    {renderIndentationVerticalLine()}
                    <div
                        className="c-tb-list-item-single__element c-tb-list-item-single__element--main"
                        onMouseEnter={hoverIn}
                        onMouseMove={handleMouseMove}
                        onMouseDown={handleMouseDown}
                        onMouseUp={handleMouseUp}
                        onMouseLeave={(event) => {
                            hoverOut();
                            handleMouseLeave(event);
                        }}
                    >
                        {renderDragIcon()}
                        {renderIndentationHorizontal()}
                        <Toggler onClick={toggleExpanded} totalSubitemsCount={item.total} />
                        {renderLabel()}
                        {renderQuickEditAction()}
                        {renderActionsWrapper()}
                    </div>
                </>
            )}
            <List
                parents={[...parents, item]}
                isExpanded={isExpanded}
                subitems={item.subitems}
                depth={itemDepth}
                checkIsDisabled={checkIsDisabled}
                itemRef={itemRef}
                selectionDisabled={selectionDisabled}
            />
            {renderQuickCreateAction()}
            <LoadMore
                isExpanded={isExpanded}
                isLoading={isLoading}
                loadMore={loadMore}
                subitems={item.subitems}
                totalSubitemsCount={item.total}
                itemDepth={itemDepth + 1}
            />
            <Limit isExpanded={isExpanded} subitems={item.subitems} itemDepth={itemDepth + 1} />
        </div>
    );
};

ListItemSingle.propTypes = {
    item: PropTypes.object.isRequired,
    index: PropTypes.number.isRequired,
    checkIsDisabled: PropTypes.func,
    isRoot: PropTypes.bool,
    itemDepth: PropTypes.number,
    parents: PropTypes.array,
    setParentIndentHeight: PropTypes.func,
    rootSelectionDisabled: PropTypes.bool,
    selectionDisabled: PropTypes.bool,
    rootElementDisabled: PropTypes.bool,
    showHighlight: PropTypes.bool,
    isLastItem: PropTypes.bool,
    enableQuickEditMode: PropTypes.bool,
    enableQuickCreateMode: PropTypes.bool,
};

ListItemSingle.defaultProps = {
    checkIsDisabled: () => false,
    isRoot: false,
    itemDepth: 0,
    parents: [],
    setParentIndentHeight: () => {},
    rootSelectionDisabled: false,
    selectionDisabled: false,
    rootElementDisabled: false,
    showHighlight: false,
    isLastItem: false,
    enableQuickEditMode: false,
    enableQuickCreateMode: false,
};

export default ListItemSingle;
