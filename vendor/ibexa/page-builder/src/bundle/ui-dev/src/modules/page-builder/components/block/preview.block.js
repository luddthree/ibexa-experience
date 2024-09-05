import React, { Component, Fragment, createRef } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';
import Preview from './preview';
import Iframe from '../iframe/iframe';
import ActionMenu from '../action-menu/action.menu';
import ConfigPopup from '../config-popup/config.popup';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import DatePicker from '../date-picker/date.picker';
import BlockConfigPanel from '../block-config-panel/block.config.panel';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

const { Translator, ibexa, bootstrap, Popper } = window;

const SELECTOR_BLOCK_PREVIEW = '.c-pb-block-preview__inner';
const CLASS_ACTION_MENU_TO_RIGHT = 'c-pb-preview-block--action-menu-to-right';
const CLASS_PREVIEW = 'c-pb-block-preview';
const CLASS_PREVIEW_DRAGGING = 'c-pb-block-preview--is-dragging-out';
const CLASS_PREVIEW_HIGHLIGHT = 'c-pb-block-preview--highlighted';
const CLASS_TREE_ELEMENT_SELECTED = 'c-tb-list-item-single__element--selected';

class PreviewBlock extends Component {
    constructor(props) {
        super(props);

        this.pageBuilderConfigPanelWrapper = document.querySelector('.ibexa-pb-config-panels-wrapper');
        this.pageBuilderPreviewBlockOptionsMenu = document.querySelector('.ibexa-pb-preview-block-options-menu');
        this._refPreviewComponent = createRef();
        this._refIframeComponent = null;
        this._refActionMenuComponent = createRef();
        this._refDatePicker = createRef();
        this._refOptionsMenuBtn = createRef();
        this._refOptionsPopoverDOM = createRef();
        this.state = {
            shouldPopupWatchLoad: false,
            isConfigPopupVisible: false,
            isConfigDataSent: false,
            isConfigDistractionFreeModeActive: false,
            isUdwOpened: false,
            udwProps: null,
            isAirtimePopupOpened: false,
            maxHeight: 50,
            isActive: props.isActive,
            blockConfigPanelTitle: props.block.name || props.meta.config.name,
            isOptionsMenuOpened: false,
            automaticallyOpenBlockSettingsEnabled: ibexa.adminUiConfig.automaticallyOpenBlockSettings === 'enabled',
            isOpenedOnInit: false,
        };

        this.prepareConfigPopup = this.prepareConfigPopup.bind(this);
        this.removeBlock = this.removeBlock.bind(this);
        this.hideConfigPopup = this.hideConfigPopup.bind(this);
        this.handleDragStart = this.handleDragStart.bind(this);
        this.initDragging = this.initDragging.bind(this);
        this.unMountBlock = this.unMountBlock.bind(this);
        this.showConfigPopup = this.showConfigPopup.bind(this);
        this.handleOnConfigPopupIframeLoad = this.handleOnConfigPopupIframeLoad.bind(this);
        this.handleLoadAfterConfigFormSubmit = this.handleLoadAfterConfigFormSubmit.bind(this);
        this.preventHidingConfigPopup = this.preventHidingConfigPopup.bind(this);
        this.openUdw = this.openUdw.bind(this);
        this.closeUdw = this.closeUdw.bind(this);
        this.setConfigIframeRef = this.setConfigIframeRef.bind(this);
        this.setConfigFormSubmittedState = this.setConfigFormSubmittedState.bind(this);
        this.getConfigIframe = this.getConfigIframe.bind(this);
        this.removeDragEventListeners = this.removeDragEventListeners.bind(this);
        this.setActionMenuPosition = this.setActionMenuPosition.bind(this);
        this.openAirtimePopup = this.openAirtimePopup.bind(this);
        this.changeAirtime = this.changeAirtime.bind(this);
        this.closeAirtimePopup = this.closeAirtimePopup.bind(this);
        this.setAirtimePopupRef = this.setAirtimePopupRef.bind(this);
        this.setActiveBlock = this.setActiveBlock.bind(this);
        this.updatePanelTitle = this.updatePanelTitle.bind(this);
        this.createPopperInstanceOrUpdate = this.createPopperInstanceOrUpdate.bind(this);
        this.closeOptionsMenuOnClickOutside = this.closeOptionsMenuOnClickOutside.bind(this);
        this.duplicateBlock = this.duplicateBlock.bind(this);
        this.refreshBlock = this.refreshBlock.bind(this);
        this.toggleTreeElementSelected = this.toggleTreeElementSelected.bind(this);
        this.handleHoverInStructureBlock = this.handleHoverInStructureBlock.bind(this);
        this.handleHoverOutStructureBlock = this.handleHoverOutStructureBlock.bind(this);
    }

    componentDidMount() {
        const wrapper = this.findBlockWrapper(this._refPreviewComponent.current);

        if (window.getComputedStyle(wrapper).position === 'static') {
            wrapper.style.position = 'relative';
        }

        wrapper.dataset.type = 'preview';
        wrapper.tabIndex = '0';
        wrapper.classList.add(CLASS_PREVIEW);
        wrapper.addEventListener('dragover', this.props.onDragOver, false);
        wrapper.addEventListener('dragstart', this.initDragging, false);
        wrapper.addEventListener('dragend', this.removeDragEventListeners, false);
        wrapper.addEventListener('mouseenter', this.setActionMenuPosition, false);
        wrapper.addEventListener('click', this.setActiveBlock, false);
        wrapper.addEventListener('mouseenter', this.handleHoverInStructureBlock, false);
        wrapper.addEventListener('mouseleave', this.handleHoverOutStructureBlock, false);

        window.ibexa.helpers.tooltips.parse(wrapper);

        if (this.props.meta.isNew && !this.props.meta.isReverted && this.state.automaticallyOpenBlockSettingsEnabled) {
            this.prepareConfigPopup(null, true);
        }

        this.previewIframeOffsetTop = this.props.previewWindow.frameElement.getBoundingClientRect().top;
    }

    componentDidUpdate(prevProps, prevState) {
        document.body.classList.toggle('ibexa-airtime-popup-visible', this.state.isAirtimePopupOpened);

        if (this.state.isAirtimePopupOpened) {
            const { hourElement } = this._refDatePicker.current.dateTimePickerWidget.flatpickrInstance;

            hourElement.addEventListener(
                'focusin',
                () => {
                    const modalInstance = bootstrap.Modal.getInstance(this._refAirtimePopupComponent._refModal);

                    if (modalInstance) {
                        modalInstance._focustrap.deactivate();

                        hourElement.addEventListener(
                            'focusout',
                            () => {
                                modalInstance._focustrap.activate();
                            },
                            { once: true },
                        );
                    }
                },
                false,
            );
        }

        if (this.state.isOptionsMenuOpened !== prevState.isOptionsMenuOpened) {
            if (this.state.isOptionsMenuOpened) {
                document.body.addEventListener('click', this.closeOptionsMenuOnClickOutside, false);
                this.props.previewWindow.document.body.addEventListener('click', this.closeOptionsMenuOnClickOutside, false);
            } else {
                document.body.removeEventListener('click', this.closeOptionsMenuOnClickOutside);
                this.props.previewWindow.document.body.removeEventListener('click', this.closeOptionsMenuOnClickOutside);
            }
        }

        this._refActionMenuComponent.current.classList.toggle('c-pb-action-menu--visible', this.state.isOptionsMenuOpened);
    }

    componentWillUnmount() {
        const wrapper = this.findBlockWrapper(this._refPreviewComponent.current);

        wrapper.removeEventListener('mouseenter', this.showActionMenu);
        wrapper.removeEventListener('dragover', this.props.onDragOver);
        wrapper.removeEventListener('dragend', this.removeDragEventListeners);
        wrapper.removeEventListener('mouseenter', this.setActionMenuPosition);
        wrapper.removeEventListener('click', this.setActiveBlock);
        wrapper.removeEventListener('mouseenter', this.handleHoverInStructureBlock, false);
        wrapper.removeEventListener('mouseleave', this.handleHoverOutStructureBlock, false);
    }

    /**
     * Finds block preview wrapper
     *
     * @method findBlockWrapper
     * @param {HTMLElement} block
     * @returns {HTMLElement|undefined}
     */
    findBlockWrapper(block) {
        if (!block) {
            return;
        }

        return block.dataset.blockId ? block : block.closest('[data-ez-block-id]');
    }

    getConfigIframe() {
        return this._refIframeComponent && this._refIframeComponent._refIframe;
    }

    getAirtimePopup() {
        return this._refAirtimePopupComponent && this._refAirtimePopupComponent._refModal;
    }

    setActiveBlock() {
        const { onClick, block } = this.props;

        onClick(block.id);
    }

    setActionMenuPosition({ currentTarget }) {
        if (currentTarget.querySelector('.c-pb-action-menu')) {
            const rect = currentTarget.getBoundingClientRect();
            const shouldAlignActionMenuToRight = rect.left > window.innerWidth / 2;
            const actionMenuTopPosition = rect.y < 0 ? Math.abs(rect.y) : 0;

            currentTarget.querySelector('.c-pb-action-menu').style.top = `${actionMenuTopPosition}px`;
            currentTarget.classList.toggle(CLASS_ACTION_MENU_TO_RIGHT, shouldAlignActionMenuToRight);
        }
    }

    openAirtimePopup({ detail }) {
        this.airtimeDate = detail.airtime;
        this.setState({ isAirtimePopupOpened: true, airtimePopupData: { onSubmit: detail.onSubmit } });
    }

    applyAirtime(callback) {
        if (!this.airtimeDate) {
            return;
        }

        this.closeAirtimePopup();

        callback(this.airtimeDate);
    }

    changeAirtime(airtime) {
        this.airtimeDate = airtime;
    }

    setAirtimePopupRef(popup) {
        this._refAirtimePopupComponent = popup;
    }

    closeAirtimePopup() {
        this.forceAirtimePopupBackdropHidden();
        this.setState({ isAirtimePopupOpened: false });
    }

    openUdw({ detail }) {
        const udwProps = {
            ...detail,
            onCancel: () => {
                detail?.onCancel?.();
                this.closeUdw();
            },
            onConfirm: (items) => {
                detail?.onConfirm?.(items);
                this.closeUdw();
            },
        };

        this.setState({ isUdwOpened: true, udwProps });
    }

    closeUdw() {
        this.setState({ isUdwOpened: false, udwProps: null });
    }

    unMountBlock() {
        const wrapper = this.findBlockWrapper(this._refPreviewComponent.current);

        window.ibexa.helpers.tooltips.hideAll(wrapper);
        this.props.root.unmount();
        wrapper.parentNode.removeChild(wrapper);
    }

    removeBlock(emitChangeEvent = true) {
        const wrapper = this.findBlockWrapper(this._refPreviewComponent.current);
        const blockFlaggedToRemove = wrapper.classList.contains('c-pb-block-preview--is-removing');

        if (blockFlaggedToRemove) {
            return;
        }

        this.props.onRemove(this.props.block, emitChangeEvent);
        window.ibexa.helpers.tooltips.hideAll(wrapper);
        wrapper.classList.add('c-pb-block-preview--is-removing');
        wrapper.addEventListener(
            'animationend',
            (event) => {
                if (event.animationName === 'remove-field') {
                    this.unMountBlock();
                }
            },
            false,
        );
        this.setState({ isOptionsMenuOpened: false });
    }

    setConfigFormSubmittedState() {
        this.getConfigIframe().onload = this.handleLoadAfterConfigFormSubmit;
        this.setState((state) => ({
            ...state,
            isConfigPopupVisible: true,
            isConfigDataSent: true,
            shouldPopupWatchLoad: true,
        }));
    }

    handleLoadAfterConfigFormSubmit() {
        const iframe = this.getConfigIframe();
        const iframeDocument = iframe.contentDocument;
        const blockConfiguration = iframeDocument.querySelector('[name="BlockConfiguration"]');
        const blockVisible = iframeDocument.querySelector('[name="BlockVisible"]');

        if (!blockConfiguration) {
            this.setState(
                (state) => ({ ...state, isConfigDataSent: false, shouldPopupWatchLoad: false }),
                () => {
                    this.attachConfigPopupEventHandlers();
                },
            );

            return;
        }

        const blockData = JSON.parse(blockConfiguration.content);
        const isVisible = parseInt(blockVisible.content, 10);

        iframe.onload = null;

        this.setState(
            (state) => {
                return {
                    ...state,
                    isConfigPopupVisible: false,
                    isConfigDataSent: false,
                    shouldPopupWatchLoad: false,
                    isOpenedOnInit: false,
                };
            },
            () => this.props.onBlockDataUpdate({ blockData, isVisible }),
        );
    }

    handleOnConfigPopupIframeLoad(iframe) {
        if (this.state.isUdwOpened) {
            return;
        }

        this.showConfigPopup(iframe);
    }

    renderConfigPopup() {
        const { isConfigPopupVisible, isConfigDataSent, blockConfigPanelTitle, isConfigDistractionFreeModeActive, isOpenedOnInit } =
            this.state;
        const { block, blockNo, meta } = this.props;
        const { isDuplicated } = meta;
        const duplicatedBlockTitle = Translator.trans(
            /*@Desc("%block_name% [duplicate]")*/ 'block.name.duplicate',
            { block_name: blockConfigPanelTitle },
            'ibexa_page_builder',
        );
        const title = isOpenedOnInit && isDuplicated ? duplicatedBlockTitle : blockConfigPanelTitle;

        if (!isConfigPopupVisible) {
            return null;
        }

        const iframeAttrs = {
            ref: this.setConfigIframeRef,
            src: 'about:blank',
            id: block.id,
            name: block.id,
            title: block.id,
            onLoad: isConfigDataSent ? this.handleLoadAfterConfigFormSubmit : this.handleOnConfigPopupIframeLoad,
            isVisible: !isConfigDataSent,
            isLoading: isConfigDataSent,
        };

        return ReactDOM.createPortal(
            <BlockConfigPanel
                onCancel={this.hideConfigPopup}
                title={title}
                subtitle={blockNo}
                isClosed={!isConfigPopupVisible}
                isDistractionFreeModeActive={isConfigDistractionFreeModeActive}
                isOpenedOnInit={isOpenedOnInit}
            >
                <Iframe {...iframeAttrs} fs={true} />
            </BlockConfigPanel>,
            this.pageBuilderConfigPanelWrapper,
        );
    }

    forceAirtimePopupBackdropHidden() {
        const popup = this.getAirtimePopup();

        this.forcePopupBackdropHidden(popup);
    }

    forcePopupBackdropHidden(popup) {
        if (popup) {
            window.bootstrap.Modal.getOrCreateInstance(popup).hide();
        }
    }

    hideConfigPopup() {
        this.setState((state) => ({
            ...state,
            isConfigPopupVisible: false,
            isConfigDataSent: false,
            shouldPopupWatchLoad: false,
            isOpenedOnInit: false,
        }));
    }

    showConfigPopup() {
        if (!this.state.shouldPopupWatchLoad) {
            return;
        }

        this.setState(
            (state) => ({
                ...state,
                isConfigPopupVisible: true,
                isConfigDataSent: false,
                shouldPopupWatchLoad: false,
            }),
            this.attachConfigPopupEventHandlers,
        );
    }

    preventHidingConfigPopup(event) {
        event.preventDefault();
    }

    prepareConfigPopup(event, isOpenedOnInit = false) {
        const actionBtn = event?.currentTarget;

        this.setState(
            (state) => ({
                ...state,
                isConfigPopupVisible: true,
                shouldPopupWatchLoad: true,
                isOpenedOnInit,
            }),
            () => {
                actionBtn?.blur();
                this.props.onConfigPopupOpen(this.props.block);
            },
        );
    }

    updatePanelTitle({ currentTarget }) {
        this.setState((state) => ({ ...state, blockConfigPanelTitle: currentTarget.value }));
    }

    attachConfigPopupEventHandlers() {
        if (this.state.isConfigDataSent) {
            return;
        }

        const iframeDoc = this.getConfigIframe().contentDocument;

        iframeDoc.body.addEventListener('ibexa-open-udw', this.openUdw, false);
        iframeDoc.body.addEventListener('ibexa-open-airtime-popup', this.openAirtimePopup, false);
        iframeDoc.body.addEventListener(
            'ibexa-distraction-free:enable',
            () => this.setState({ isConfigDistractionFreeModeActive: true }),
            false,
        );
        iframeDoc.body.addEventListener(
            'ibexa-distraction-free:disable',
            () => this.setState({ isConfigDistractionFreeModeActive: false }),
            false,
        );
        iframeDoc.querySelector('[name="block_configuration"]').addEventListener('submit', this.setConfigFormSubmittedState, false);
        iframeDoc.querySelector('[data-form-action="discard"]').addEventListener('click', this.hideConfigPopup, false);
        iframeDoc.querySelector('[name="block_configuration[name]"]').addEventListener('input', this.updatePanelTitle, false);
    }

    initDragging(event) {
        const preview = event.currentTarget.querySelector(SELECTOR_BLOCK_PREVIEW);
        const image = document.querySelector(`[data-ibexa-toolbox-block-type="${this.props.meta.config.type}"]`);

        image.hidden = false;

        if (preview) {
            event.currentTarget.classList.remove(CLASS_PREVIEW_HIGHLIGHT);
            event.currentTarget.classList.add(CLASS_PREVIEW_DRAGGING);
            event.currentTarget.ondragover = () => true;
            event.currentTarget.addEventListener('drag', this.props.onDrag, false);
        }

        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text/html', image);
        event.dataTransfer.setDragImage(image, 0, 0);
    }

    removeDragEventListeners({ currentTarget }) {
        const wrapper = this.findBlockWrapper(this._refPreviewComponent.current);

        currentTarget.classList.remove(CLASS_PREVIEW_DRAGGING);
        wrapper.removeEventListener('drag', this.props.onDrag);
    }

    handleDragStart() {
        const wrapper = this.findBlockWrapper(this._refPreviewComponent.current);
        const { block, meta } = this.props;
        const preview = wrapper.querySelector(SELECTOR_BLOCK_PREVIEW);
        const wrapperClasses = wrapper.className.split(' ').filter((wrapperClass) => wrapperClass !== '');

        meta.preview = preview.innerHTML;
        wrapper.draggable = true;

        this.props.onDragStart({
            block,
            meta,
            wrapperClasses: wrapperClasses,
            unMountBlock: this.unMountBlock,
        });
    }

    handleHoverInStructureBlock({ currentTarget }) {
        this.toggleTreeElementSelected(currentTarget, true);
    }

    handleHoverOutStructureBlock({ currentTarget }) {
        this.toggleTreeElementSelected(currentTarget, false);
    }

    toggleTreeElementSelected(currentTarget, isHovered) {
        const { ezBlockId } = currentTarget.dataset;
        const structureElement = document.querySelector(`.c-tb-list-item-single[data-block-id="${ezBlockId}"]`);

        if (structureElement) {
            const singleItemElement = structureElement.querySelector('& > .c-tb-list-item-single__element');

            singleItemElement.classList.toggle(CLASS_TREE_ELEMENT_SELECTED, isHovered);
        }
    }

    renderActionMenu() {
        const { block, isEditable, isAvailable, label } = this.props;
        const props = {
            label,
            handleDragStart: this.handleDragStart,
            blockId: block.id,
            isEditable: isEditable && isAvailable,
            ref: this._refActionMenuComponent,
        };

        return <ActionMenu {...props}>{this.renderActions()}</ActionMenu>;
    }

    duplicateBlock() {
        this.setState({ isOptionsMenuOpened: false });
        this.props.onDuplicate(this.props.block);
    }

    refreshBlock() {
        const { block, meta } = this.props;

        this.setState({ isOptionsMenuOpened: false });
        this.props.onBlockDataUpdate({ blockData: block, isVisible: meta.isVisible });
    }

    closeOptionsMenuOnClickOutside(event) {
        const isClickedInsidePopupMenu = this._refOptionsPopoverDOM.current.contains(event.target);
        const isClickedMenuOptionsBtn = this._refOptionsMenuBtn.contains(event.target);

        if (!isClickedInsidePopupMenu && !isClickedMenuOptionsBtn) {
            event.preventDefault();

            this.setState({ isOptionsMenuOpened: false });
        }
    }

    createPopperInstanceOrUpdate(btnNode) {
        if (!btnNode || !this._refOptionsPopoverDOM.current) {
            return;
        }

        if (!this._refOptionsPopoverInstance) {
            const newPoperInstance = Popper.createPopper(btnNode, this._refOptionsPopoverDOM.current, {
                placement: 'bottom',
                modifiers: [
                    {
                        name: 'offset',
                        options: {
                            offset: ({ placement, popper }) => {
                                if (placement === 'left' || placement === 'right') {
                                    return [popper.height / 2 - 30, this.previewIframeOffsetTop + 8];
                                }
                                return [0, this.previewIframeOffsetTop + 8];
                            },
                        },
                    },
                    {
                        name: 'flip',
                        options: {
                            fallbackPlacements: ['top', 'left', 'right'],
                        },
                    },
                ],
            });

            this._refOptionsPopoverInstance = newPoperInstance;
        } else {
            this._refOptionsPopoverInstance.update();
        }
    }

    renderOptions() {
        const { isEditable } = this.props;
        const popupMenuClass = createCssClassNames({
            'ibexa-popup-menu c-pb-preview-block__options-menu': true,
            'c-pb-preview-block__options-menu--opened': this.state.isOptionsMenuOpened,
        });

        return ReactDOM.createPortal(
            <ul className={popupMenuClass} ref={this._refOptionsPopoverDOM}>
                <li className="ibexa-popup-menu__item">
                    <button type="button" className="ibexa-popup-menu__item-content" onClick={this.duplicateBlock}>
                        {Translator.trans(/*@Desc("Duplicate")*/ 'block.duplicate', {}, 'ibexa_page_builder')}
                    </button>
                </li>
                <li className="ibexa-popup-menu__item">
                    <button type="button" className="ibexa-popup-menu__item-content" onClick={this.refreshBlock}>
                        {Translator.trans(/*@Desc("Refresh")*/ 'block.refresh', {}, 'ibexa_page_builder')}
                    </button>
                </li>
                <li className="ibexa-popup-menu__item">
                    <button disabled={!isEditable} type="button" className="ibexa-popup-menu__item-content" onClick={this.removeBlock}>
                        {Translator.trans(/*@Desc("Delete")*/ 'block.delete', {}, 'ibexa_page_builder')}
                    </button>
                </li>
            </ul>,
            this.pageBuilderPreviewBlockOptionsMenu,
        );
    }

    renderActions() {
        const { block } = this.props;
        const delayShow = 400;
        const delayHide = 0;
        const moveUp = () => {
            document.body.dispatchEvent(
                new CustomEvent('ibexa-pb-blocks:move', {
                    detail: { blockId: block.id, action: 'move-up' },
                }),
            );
        };
        const moveDown = () => {
            document.body.dispatchEvent(
                new CustomEvent('ibexa-pb-blocks:move', {
                    detail: { blockId: block.id, action: 'move-down' },
                }),
            );
        };
        const moveUpAttrs = {
            className:
                'c-pb-preview-block__action ibexa-btn ibexa-btn--ghost ibexa-btn--small ibexa-btn--no-text c-pb-action-menu__move-up',
            'data-tooltip-iframe-selector': '#page-builder-preview',
            title: Translator.trans(/*@Desc("Move up block")*/ 'block.move.up', {}, 'ibexa_page_builder'),
            onClick: moveUp,
            'data-delay-show': delayShow,
            'data-delay-hide': delayHide,
        };
        const moveDownAttrs = {
            className:
                'c-pb-preview-block__action ibexa-btn ibexa-btn--ghost ibexa-btn--small ibexa-btn--no-text c-pb-action-menu__move-down',
            'data-tooltip-iframe-selector': '#page-builder-preview',
            title: Translator.trans(/*@Desc("Move down block")*/ 'block.move.down', {}, 'ibexa_page_builder'),
            onClick: moveDown,
            'data-delay-show': delayShow,
            'data-delay-hide': delayHide,
        };
        const settingAttrs = {
            className: 'c-pb-preview-block__action ibexa-btn ibexa-btn--ghost ibexa-btn--small ibexa-btn--no-text',
            'data-tooltip-iframe-selector': '#page-builder-preview',
            title: Translator.trans(/*@Desc("Block settings")*/ 'block.settings', {}, 'ibexa_page_builder'),
            onClick: this.prepareConfigPopup,
            'data-delay-show': delayShow,
            'data-delay-hide': delayHide,
        };
        const optionsAttrs = {
            className: 'c-pb-preview-block__action ibexa-btn ibexa-btn--ghost ibexa-btn--small ibexa-btn--no-text',
            'data-tooltip-iframe-selector': '#page-builder-preview',
            ref: (node) => {
                this._refOptionsMenuBtn = node;

                if (node) {
                    this.createPopperInstanceOrUpdate(node);
                }
            },
            onClick: () => {
                this.setState((state) => ({ ...state, isOptionsMenuOpened: !state.isOptionsMenuOpened }));
            },
        };

        return (
            <>
                <button {...moveUpAttrs} type="button">
                    <Icon name="back" extraClasses="ibexa-icon--small" />
                </button>
                <button {...moveDownAttrs} type="button">
                    <Icon name="back" extraClasses="ibexa-icon--small" />
                </button>
                <button {...settingAttrs} type="button">
                    <Icon name="settings-block" extraClasses="ibexa-icon--small" />
                </button>
                <button {...optionsAttrs} type="button">
                    <Icon name="options" extraClasses="ibexa-icon--small" />
                </button>
            </>
        );
    }

    setConfigIframeRef(component) {
        this._refIframeComponent = component;
    }

    renderUdw() {
        const { UniversalDiscovery } = ibexa.modules;

        return <UniversalDiscovery {...this.state.udwProps} />;
    }

    renderActionBtns() {
        const cancelLabel = Translator.trans(/*@Desc("Cancel")*/ 'airtime_popup.discard.btn', {}, 'ibexa_page_builder');
        const submitLabel = Translator.trans(/*@Desc("Submit")*/ 'airtime_popup.submit.btn', {}, 'ibexa_page_builder');

        return (
            <>
                <button
                    className="btn ibexa-btn ibexa-btn--filled-info"
                    onClick={this.applyAirtime.bind(this, this.state.airtimePopupData.onSubmit)}
                    type="button"
                >
                    {submitLabel}
                </button>
                <button className="btn ibexa-btn ibexa-btn--info" onClick={this.closeAirtimePopup} type="button">
                    {cancelLabel}
                </button>
            </>
        );
    }

    renderAirtimePopup() {
        const popupTitle = Translator.trans(/*@Desc("Content airtime settings")*/ 'airtime_popup.title', {}, 'ibexa_page_builder');

        return (
            <ConfigPopup
                ref={this.setAirtimePopupRef}
                onClose={this.closeAirtimePopup}
                isVisible={true}
                isLoading={false}
                additionalClasses="c-pb-config-popup--airtime"
                name={popupTitle}
                footer={this.renderActionBtns()}
            >
                <DatePicker onDateChange={this.changeAirtime} airtime={this.airtimeDate} ref={this._refDatePicker} />
            </ConfigPopup>
        );
    }

    render() {
        const { meta, isAvailable, udwContainer, airtimeContainer, label } = this.props;
        const { isUdwOpened, isAirtimePopupOpened, automaticallyOpenBlockSettingsEnabled, isOpenedOnInit } = this.state;

        return (
            <Fragment>
                {isUdwOpened && ReactDOM.createPortal(this.renderUdw(), udwContainer)}
                {isAirtimePopupOpened && ReactDOM.createPortal(this.renderAirtimePopup(), airtimeContainer)}
                <Preview
                    ref={this._refPreviewComponent}
                    preview={meta.preview}
                    name={meta.config.name}
                    label={label}
                    isValid={meta.isValid}
                    isVisible={meta.isVisible}
                    shouldDisplayError={meta.shouldDisplayError}
                    isAvailable={isAvailable}
                    removeBlock={this.removeBlock}
                    hasInsertAnimation={meta.isReverted}
                    isConfigPanelOpenedOnInit={isOpenedOnInit && automaticallyOpenBlockSettingsEnabled}
                />
                {isAvailable && this.renderActionMenu()}
                {isAvailable && this.renderConfigPopup()}
                {this.renderOptions()}
            </Fragment>
        );
    }
}

PreviewBlock.propTypes = {
    onDragOver: PropTypes.func.isRequired,
    onDragStart: PropTypes.func.isRequired,
    onRemove: PropTypes.func.isRequired,
    onBlockDataUpdate: PropTypes.func.isRequired,
    block: PropTypes.object.isRequired,
    root: PropTypes.object.isRequired,
    meta: PropTypes.shape({
        config: PropTypes.object.isRequired,
        preview: PropTypes.string.isRequired,
        shouldDisplayError: PropTypes.bool.isRequired,
        isValid: PropTypes.bool.isRequired,
        isVisible: PropTypes.bool.isRequired,
        isNew: PropTypes.bool,
        isReverted: PropTypes.bool,
        isDuplicated: PropTypes.bool,
    }).isRequired,
    onConfigPopupOpen: PropTypes.func.isRequired,
    udwContainer: PropTypes.instanceOf(Element).isRequired,
    onDrag: PropTypes.func.isRequired,
    isEditable: PropTypes.bool.isRequired,
    isActive: PropTypes.bool.isRequired,
    onClick: PropTypes.func.isRequired,
    isAvailable: PropTypes.bool,
    airtimeContainer: PropTypes.instanceOf(Element).isRequired,
    onDuplicate: PropTypes.func.isRequired,
    previewWindow: PropTypes.object.isRequired,
    blockNo: PropTypes.string.isRequired,
    label: PropTypes.node.isRequired,
};

PreviewBlock.defaultProps = {
    isAvailable: false,
};

export default PreviewBlock;
