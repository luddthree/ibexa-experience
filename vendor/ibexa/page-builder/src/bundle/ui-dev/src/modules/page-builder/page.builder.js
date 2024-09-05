import React, { Component } from 'react';
import { createPortal } from 'react-dom';
import * as ReactDOMClient from '@react-dom/client';
import PropTypes from 'prop-types';
import Iframe from './components/iframe/iframe';
import PreviewBlock from './components/block/preview.block';
import Toolbox from './components/toolbox/toolbox';
import BlocksToolbox from './components/toolbox/toolbox.blocks';
import StructureToolbox from './components/toolbox/toolbox.structure';
import LayoutSelector from './components/layout-selector/layout.selector';
import LayoutSwitcher from './components/layout-switcher/layout.switcher';
import SettingsPopup from './components/settings-popup/settings.popup';
import ErrorBoundary from './components/error-boundary/error.boundary';
import HiddenBlocks from './components/hidden-block/hidden.blocks';
import Notification, { NOTIFICATION_TYPE } from './components/notification/notification.js';
import TimelineModule from '../timeline.module';
import generateGuid from '../guid-generator/guid.generator';
import { getBlockPreview } from './services/preview.service';
import { getTimelineEvents } from './services/timeline.service';
import deepClone from '../helpers/deep.clone';

import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

const { Translator, ibexa, document, Routing } = window;

const CLASS_PLACEHOLDER = 'droppable-placeholder';
const CLASS_HIGHLIGHT = 'c-pb-block-preview--highlighted';
const CLASS_ZONE = 'm-page-builder__zone';
const CLASS_ZONE_EMPTY = 'm-page-builder__zone--empty';
const CLASS_ZONE_DRAGOVER = 'm-page-builder__zone--dragover';
const CLASS_PAGE_BUILDER_DISABLED = 'ibexa-pb-app--is-disabled';
const CLASS_GO_BACK_ENABLED = 'ibexa-pb-app--go-back-enabled';
const SELECTOR_PLACEHOLDER = '.droppable-placeholder';
const SELECTOR_ZONE = '[data-ibexa-zone-id]';
const CLASS_BLOCK_PREVIEW_HOVERED = 'c-pb-action-menu--visible';
const SELECTOR_SAVE_DRAFT = '#ezplatform_content_forms_content_edit_saveDraft';
const PLACEHOLDER_TYPE_ZONE = 'zone';
const IDENTIFIER_BLOCK_DATA_ATTRIBUTE = 'ezBlockId';
const ELEMENT_DIV = 'div';
const TIMEOUT_REMOVE_HIGHLIGHT = 3000;
const BLOCKS_BASE_STATE = {
    shouldDisplayError: false,
    isValid: false,
    preview: '',
};
export const POSITION_CHANGE_METHOD = {
    DRAG: 'drag',
    ARROWS: 'arrows',
};

class PageBuilder extends Component {
    constructor(props) {
        super(props);

        this.requestForm = this.requestForm.bind(this);
        this.handleLayoutSelectorCancelExternal = this.handleLayoutSelectorCancelExternal.bind(this);
        this.handleLayoutSelectorCancelInternal = this.handleLayoutSelectorCancelInternal.bind(this);
        this.handleLayoutSelectorCancelOnCreate = this.handleLayoutSelectorCancelOnCreate.bind(this);
        this.handleLayoutSelectorConfirm = this.handleLayoutSelectorConfirm.bind(this);
        this.handleIframeLoad = this.handleIframeLoad.bind(this);
        this.handleDrop = this.handleDrop.bind(this);
        this.handleDragStartSidebarBlock = this.handleDragStartSidebarBlock.bind(this);
        this.handleDragStartPreviewBlock = this.handleDragStartPreviewBlock.bind(this);
        this.handleElementDragOver = this.handleElementDragOver.bind(this);
        this.handleBlockRemove = this.handleBlockRemove.bind(this);
        this.renderSinglePreviewBlock = this.renderSinglePreviewBlock.bind(this);
        this.handleZoneDragOver = this.handleZoneDragOver.bind(this);
        this.handleBlockDataUpdate = this.handleBlockDataUpdate.bind(this);
        this.updateBlocksPreview = this.updateBlocksPreview.bind(this);
        this.updateBlocksPreviewState = this.updateBlocksPreviewState.bind(this);
        this.attachDocumentDropListeners = this.attachDocumentDropListeners.bind(this);
        this.getPreviewDocument = this.getPreviewDocument.bind(this);
        this.getPreviewWindow = this.getPreviewWindow.bind(this);
        this.getBlockPreviewNode = this.getBlockPreviewNode.bind(this);
        this.getPlaceholderPreviewNode = this.getPlaceholderPreviewNode.bind(this);
        this.getBlocksFromZones = this.getBlocksFromZones.bind(this);
        this.disableInIframeClicks = this.disableInIframeClicks.bind(this);
        this.setPageFieldTypeFormFieldValue = this.setPageFieldTypeFormFieldValue.bind(this);
        this.validateAllBlocksData = this.validateAllBlocksData.bind(this);
        this.validateAllBlocksAvailability = this.validateAllBlocksAvailability.bind(this);
        this.validateLayoutData = this.validateLayoutData.bind(this);
        this.areBlocksValid = this.areBlocksValid.bind(this);
        this.validateBlockData = this.validateBlockData.bind(this);
        this.setBlockValidState = this.setBlockValidState.bind(this);
        this.initBlocksPreviewState = this.initBlocksPreviewState.bind(this);
        this.handleDragBlock = this.handleDragBlock.bind(this);
        this.showLayoutSelector = this.showLayoutSelector.bind(this);
        this.setIframeRef = this.setIframeRef.bind(this);
        this.getPlaceholderNodes = this.getPlaceholderNodes.bind(this);
        this.handleDragOverTimeout = this.handleDragOverTimeout.bind(this);
        this.cancelDropState = this.cancelDropState.bind(this);
        this.setIsEditableState = this.setIsEditableState.bind(this);
        this.setDocumentDragOverEventHandler = this.setDocumentDragOverEventHandler.bind(this);
        this.unsetDocumentDragOverEventHandler = this.unsetDocumentDragOverEventHandler.bind(this);
        this.setClientYPosition = this.setClientYPosition.bind(this);
        this.updateTimelineEvents = this.updateTimelineEvents.bind(this);
        this.updateTimelineEventsState = this.updateTimelineEventsState.bind(this);
        this.handleOnBlockClick = this.handleOnBlockClick.bind(this);
        this.handleClickOutsideBlock = this.handleClickOutsideBlock.bind(this);
        this.clearZoneDragOverState = this.clearZoneDragOverState.bind(this);
        this.updatePreviewRequestParams = this.updatePreviewRequestParams.bind(this);
        this.duplicatePreviewBlock = this.duplicatePreviewBlock.bind(this);
        this.setSidebarSide = this.setSidebarSide.bind(this);
        this.scrollIntoBlockPreview = this.scrollIntoBlockPreview.bind(this);
        this.hoverInBlockPreview = this.hoverInBlockPreview.bind(this);
        this.hoverOutBlockPreview = this.hoverOutBlockPreview.bind(this);
        this.getLastBlockTypeNumber = this.getLastBlockTypeNumber.bind(this);
        this.handleStructureViewEvent = this.handleStructureViewEvent.bind(this);
        this.handleMoveEvent = this.handleMoveEvent.bind(this);
        this.setSettingsModalVisible = this.setSettingsModalVisible.bind(this);
        this.renderNotifications = this.renderNotifications.bind(this);
        this.addNotification = this.addNotification.bind(this);
        this.removeNotification = this.removeNotification.bind(this);

        this.blockConfigRequestForm = window.document.querySelector('form[name="request_block_configuration"]');
        this.blockConfigTextarea = this.blockConfigRequestForm.querySelector('#request_block_configuration_page');
        this.blockConfigBlocksInput = this.blockConfigRequestForm.querySelector('#request_block_configuration_block_id');
        this.blockConfigSubmit = this.blockConfigRequestForm.querySelector('#request_block_configuration_request');

        this.updatePreviewRequestParamsComponents = window.ibexa.pageBuilder.components
            ? window.ibexa.pageBuilder.components.updatePreviewRequestParams
            : [];

        this.onDragOverTimeout = null;
        this.contentEditForm = null;
        this._clientY = 0;
        this._blockPreviewRefs = new Map();

        const { convertDateToTimezone } = window.ibexa.helpers.timezone;
        const blocksMap = this.initBlocksMap();
        const layoutSelectorTitle = Translator.trans(/*@Desc("Select layout")*/ 'layout_selector.create.title', {}, 'ibexa_page_builder');
        const layoutSelectorConfirmBtnLabel = Translator.trans(
            /*@Desc("Select")*/ 'layout_selector.create.select.label',
            {},
            'ibexa_page_builder',
        );

        this.state = {
            ...props.getInitDragDropState(),
            draggedPreviewBlockData: null,
            draggedSidebarBlockType: null,
            iframeLoaded: false,
            isIframelessMode: false,
            previewUrl: props.previewUrl,
            fieldValue: props.fieldValue,
            noDropzones: !props.fieldValue.zones.length,
            blocksMap,
            shouldUpdateBlocksPreviewState: !!Object.keys(blocksMap).length,
            isSwitchingLayout: false,
            shouldCreateBlocksPreviewNodes: false,
            layoutSelectorTitle,
            layoutSelectorConfirmBtnLabel,
            timestamp: convertDateToTimezone(new Date()).valueOf(),
            timelineEvents: ibexa.pageBuilder.timeline.events,
            activeBlockId: null,
            isOverZone: false,
            dragOverZoneId: null,
            blockPreviewPagePreviewRequestParams: {},
            isValidActiveLayout: this.isValidActiveLayout(props.fieldValue),
            layoutSelected: false,
            hasLayoutValidationError: false,
            isSidebarLeftSide: false,
            blocksTypeMap: this.initBlocksTypeMap(props.blocksConfig),
            blocksIdMap: this.initBlocksIdMap(props.fieldValue, props.blocksConfig),
            isSettingsModalVisible: true,
            blocksIconMap: this.initBlocksIconMap(props.blocksConfig),
            notificationsMap: new Map(),
        };
    }

    componentDidMount() {
        const { iframeLoaded, shouldUpdateBlocksPreviewState, noDropzones } = this.state;
        const isCreateMode = this.isCreateMode();

        this.modalContainer = document.createElement(ELEMENT_DIV);
        this.udwContainer = document.createElement(ELEMENT_DIV);
        this.airtimeContainer = document.createElement(ELEMENT_DIV);
        this.layoutSwitcherContainer = document.querySelector('.ibexa-pb-action-bar__layout-switcher-toggler');
        this.pageBuilderConfigPanelWrapper = document.querySelector('.ibexa-pb-config-panels-wrapper');
        this.timelineContainer = document.querySelector('.ibexa-pb-timeline-wrapper');
        this.loader = document.querySelector('.ibexa-pb-iframe-backdrop');
        this.actionBar = document.querySelector('.ibexa-pb-action-bar');

        if (!isCreateMode) {
            this.loader.classList.remove('ibexa-pb-iframe-backdrop--is-loading');
        }

        this.actionBar.classList.remove('ibexa-pb-action-bar--hidden');
        this.modalContainer.classList.add('m-page-builder__modal-container');
        this.udwContainer.classList.add('m-page-builder__udw-container');
        this.airtimeContainer.classList.add('m-page-builder__airtime-container');
        document.body.appendChild(this.modalContainer);
        document.body.appendChild(this.udwContainer);
        document.body.appendChild(this.airtimeContainer);
        document.body.classList.add(CLASS_PAGE_BUILDER_DISABLED, CLASS_GO_BACK_ENABLED);

        if (noDropzones) {
            const message = Translator.trans(
                /*@Desc("This page does not have a designed dropzone area for adding content")*/ 'notification.no_dropzone',
                {},
                'ibexa_page_builder',
            );

            this.addNotification({ id: 'notification-no-dropzone', message, type: NOTIFICATION_TYPE.WARNING, noCloseBtn: true });
        }

        if (iframeLoaded) {
            this.manageZoneEventHandlers();
            this.disableInIframeClicks();

            if (shouldUpdateBlocksPreviewState) {
                this.initBlocksPreviewState();
            }

            document.body.classList.remove(CLASS_PAGE_BUILDER_DISABLED, CLASS_GO_BACK_ENABLED);
        }

        if (isCreateMode) {
            document.dispatchEvent(
                new CustomEvent('ibexa-pb-config-panel-open', {
                    cancelable: true,
                    detail: {},
                }),
            );
            this.setState(() => ({ isIframelessMode: true }));
        }

        if (!this.state.isValidActiveLayout) {
            this.showLayoutSelector();
        }

        if (!this.validateAllBlocksAvailability()) {
            const message = Translator.trans(
                /*@Desc("One or more blocks is not available in this page")*/ 'block.no_availability',
                {},
                'ibexa_page_builder',
            );

            this.addNotification({ message, type: NOTIFICATION_TYPE.ERROR });
        }
        this.validateAllBlocksData();

        document.body.addEventListener(
            'ibexa-pb:validation:layout',
            ({ detail: { isValid } }) => {
                this.setState(() => ({ hasLayoutValidationError: !isValid }));
            },
            false,
        );
        document.body.addEventListener('ibexa-pb-blocks:move', this.handleMoveEvent, false);
        document.body.addEventListener('ibexa-pb-blocks:action', this.handleStructureViewEvent, false);
        document.body.addEventListener('click', this.handleClickOutsideBlock, false);

        window.ibexa.addConfig('pb.notification', {
            addNotification: this.addNotification,
            removeNotification: this.removeNotification,
            type: NOTIFICATION_TYPE,
        });
    }

    shouldComponentUpdate(nextProps, nextState) {
        const { draggedPreviewBlockData, draggedSidebarBlockType, blocksMap } = this.state;
        const isDuringDragDrop = !!draggedPreviewBlockData || !!draggedSidebarBlockType;
        const willBeDuringDragDrop = !!nextState.draggedPreviewBlockData || !!nextState.draggedSidebarBlockType;
        const blocksMapWillChange = blocksMap !== nextState.blocksMap;
        const dragDropContinues = isDuringDragDrop && willBeDuringDragDrop;
        const shouldNotUpdate = dragDropContinues && !blocksMapWillChange;

        return !shouldNotUpdate;
    }

    componentDidUpdate(prevProps, prevState) {
        const { iframeLoaded, shouldUpdateBlocksPreviewState, shouldCreateBlocksPreviewNodes, isOverZone } = this.state;

        if (!iframeLoaded) {
            return;
        }

        if (shouldUpdateBlocksPreviewState) {
            this.initBlocksPreviewState();
        }

        if (shouldCreateBlocksPreviewNodes) {
            this.createBlocksPreviewNodes();
        }

        if (!isOverZone) {
            this.props.removePlaceholders(this.getPlaceholderNodes(), this.props.removePlaceholderWithAnimation);
        }

        this.renderPreviewBlocks();
        this.manageZoneEventHandlers();
        this.disableInIframeClicks();

        if (!prevState.activeBlockId && this.state.activeBlockId) {
            this.getPreviewDocument().body.dispatchEvent(new CustomEvent('ibexa-active-block-clicked'));
        }
    }

    componentWillUnmount() {
        document.body.removeChild(this.modalContainer);
        document.body.removeChild(this.udwContainer);
        document.body.removeChild(this.airtimeContainer);
    }

    isCreateMode() {
        return ['create', 'translate_without_base_language'].includes(this.props.mode);
    }

    isValidActiveLayout(fieldValue) {
        return this.props.layoutsConfig.some((layoutConfig) => layoutConfig.id === fieldValue.layout && layoutConfig.visible);
    }

    getFieldValue() {
        const fieldSelector = this.props.pageFieldValueSelector.trim();
        const pageFieldInput = document.querySelector(fieldSelector);
        const fieldValue = JSON.parse(pageFieldInput.value);

        return fieldValue;
    }

    duplicatePreviewBlock(zoneId, block) {
        const fieldValue = this.getPageFieldTypeState();
        const zoneKey = fieldValue.zones.findIndex(({ id }) => id === zoneId);
        const blockPosition = fieldValue.zones[zoneKey].blocks.findIndex(({ id }) => id === block.id);
        const newBlock = deepClone(block);

        newBlock.id = generateGuid('b-');
        newBlock.attributes.forEach((attribute) => {
            attribute.id = generateGuid('a-');
        });
        this.insertPreviewBlock(newBlock, { zoneKey, blockPosition: blockPosition + 1, isDuplicated: true }, true);
    }

    insertPreviewBlock(newBlock, { zoneKey, blockPosition, isReverted = false, isDuplicated = false }, emitChange = false) {
        const fieldValue = this.getPageFieldTypeState();
        const blocksMap = this.getBlocksMapState();
        const zoneId = fieldValue.zones[zoneKey].id;
        const nextBlockId = fieldValue.zones[zoneKey].blocks[blockPosition]?.id ?? null;
        const config = this.props.blocksConfig.find((configEntry) => configEntry.type === newBlock.type);
        const newBlockDOMElement = document.createElement('div');
        const nextBlockDOMElement = this.getPreviewDocument().querySelector(`[data-ez-block-id="${nextBlockId}"]`);
        const destinationZoneDOMElement = this.getPreviewDocument().querySelector(`[data-ibexa-zone-id="${zoneId}"]`);
        const leftoverBlockNode = this.getBlockPreviewNode(newBlock.id);
        const blocksIdMap = new Map(this.state.blocksIdMap);

        if (leftoverBlockNode) {
            leftoverBlockNode.ibexaBlockRoot.unmount();
            leftoverBlockNode.remove();
        }

        if (!blocksIdMap.has(newBlock.id)) {
            const blockName = this.state.blocksTypeMap.get(newBlock.type);
            const lastBlockTypeNumber = this.getLastBlockTypeNumber(blockName);

            blocksIdMap.set(newBlock.id, `${blockName} #${lastBlockTypeNumber + 1}`);
        }

        fieldValue.zones = this.insertBlockDataIntoZone(fieldValue.zones, newBlock, zoneId, nextBlockId);
        blocksMap[newBlock.id] = {
            ...BLOCKS_BASE_STATE,
            config,
            isVisible: true,
            isNew: !isReverted,
            isReverted,
            isDuplicated,
        };
        newBlockDOMElement.dataset.ezBlockId = newBlock.id;

        destinationZoneDOMElement.insertBefore(newBlockDOMElement, nextBlockDOMElement);

        this.setState(
            () => ({
                fieldValue,
                blocksMap,
                blocksIdMap,
            }),
            () => {
                this.updateBlocksPreview([newBlock.id], emitChange);
            },
        );
    }

    removePreviewBlock(blockId) {
        const blocksMap = this.getBlocksMapState();
        const blockToRemove = this._blockPreviewRefs.get(blockId);

        delete blocksMap[blockId];

        blockToRemove.removeBlock(false);
    }

    reorderPreviewBlock(block, { destinationZoneKey, destinationPosition, positionChangeMethod }, emitChangeEvent = false) {
        const fieldValue = this.getPageFieldTypeState();
        const zoneIndex = fieldValue.zones.findIndex((zone) => zone.blocks.some(({ id }) => id === block.id));
        const destinationZoneLength = fieldValue.zones[destinationZoneKey].blocks.length;
        const blockIndex = fieldValue.zones[destinationZoneKey].blocks.findIndex(({ id }) => id === block.id);
        let nextBlockPosition = destinationPosition;

        if (positionChangeMethod === POSITION_CHANGE_METHOD.ARROWS) {
            nextBlockPosition = destinationZoneKey < zoneIndex ? destinationZoneLength : 0;

            if (blockIndex !== -1) {
                nextBlockPosition = destinationPosition > blockIndex ? destinationPosition + 1 : destinationPosition;
            }
        }

        const nextBlockId = fieldValue.zones[destinationZoneKey].blocks[nextBlockPosition]?.id ?? null;
        const destinationZoneId = fieldValue.zones[destinationZoneKey].id;

        fieldValue.zones = this.removeBlockDataFromZone([...fieldValue.zones], block.id);
        fieldValue.zones = this.insertBlockDataIntoZone(fieldValue.zones, block, destinationZoneId, nextBlockId);

        const blockDOMElement = this.getPreviewDocument().querySelector(`[data-ez-block-id="${block.id}"]`);
        const nextBlockDOMElement = this.getPreviewDocument().querySelector(`[data-ez-block-id="${nextBlockId}"]`);
        const destinationZoneDOMElement = this.getPreviewDocument().querySelector(`[data-ibexa-zone-id="${destinationZoneId}"]`);

        destinationZoneDOMElement.insertBefore(blockDOMElement, nextBlockDOMElement);

        this.setState(
            () => ({ fieldValue }),
            () => this.setPageFieldTypeFormFieldValue(this.stringifyPageFieldTypeState(), emitChangeEvent),
        );
    }

    updatePreviewBlock(blockId) {
        this.setState(
            () => ({ fieldValue: this.getFieldValue() }),
            () => this.updateBlocksPreview([blockId], false),
        );
    }

    updatePreviewRequestParams(blockPreviewPagePreviewRequestParams) {
        const blocksIds = Object.keys(this.state.blocksMap);

        this.setState(
            () => ({
                blockPreviewPagePreviewRequestParams,
            }),
            () => this.updateBlocksPreview(blocksIds),
        );
    }

    handleOnBlockClick(activeBlockId) {
        this.setState(() => ({ activeBlockId }));
    }

    handleClickOutsideBlock({ target }) {
        const block = target.closest('.c-pb-block-preview');

        if (block) {
            return;
        }

        this.setState(() => ({ activeBlockId: null }));
    }

    validateAllBlocksAvailability() {
        const blocks = this.props.fieldValue.zones.reduce((total, zone) => [...total, ...zone.blocks], []);
        const { blocksConfig } = this.props;

        return blocks.every((block) => {
            const blockConfig = blocksConfig.find((config) => config.type === block.type);

            return blockConfig.visible;
        });
    }

    createBlocksPreviewNodes() {
        const fieldValue = this.getPageFieldTypeState();
        const preview = this.getPreviewDocument();

        fieldValue.zones.forEach((zone) => {
            zone.blocks.forEach((block) => {
                const container = document.createElement(ELEMENT_DIV);

                container.dataset.ezBlockId = block.id;
                container.dataset.zoneId = zone.id;

                preview.querySelector(`[data-ibexa-zone-id="${zone.id}"]`).append(container);
            });
        });

        this.setState(() => ({ shouldCreateBlocksPreviewNodes: false }));
    }

    initBlocksTypeMap(blocksConfig) {
        const blocksTypeMap = new Map();

        blocksConfig.forEach((blockConfig) => {
            blocksTypeMap.set(blockConfig.type, blockConfig.name);
        });

        return blocksTypeMap;
    }

    initBlocksIconMap(blocksConfig) {
        const blocksIconMap = new Map();

        blocksConfig.forEach((blockConfig) => {
            blocksIconMap.set(blockConfig.type, blockConfig.thumbnail);
        });

        return blocksIconMap;
    }

    initBlocksIdMap(fieldValue, blocksConfig) {
        const blocksIdMap = new Map();
        const blockNameCount = {};
        const blocksTypeMap = this.initBlocksTypeMap(blocksConfig);

        fieldValue.zones.forEach((zone) => {
            zone.blocks.forEach(({ id, type }) => {
                const blockName = blocksTypeMap.get(type);

                blockNameCount[blockName] = (blockNameCount[blockName] || 0) + 1;
                blocksIdMap.set(id, `${blockName} #${blockNameCount[blockName]}`);
            });
        });

        return blocksIdMap;
    }

    initBlocksMap() {
        const blocks = this.props.fieldValue.zones.reduce((total, zone) => [...total, ...zone.blocks], []);
        const { blocksConfig } = this.props;
        const blocksMap = blocks.reduce((total, block) => {
            const blockConfig = blocksConfig.find((config) => config.type === block.type);

            total[block.id] = {
                ...BLOCKS_BASE_STATE,
                config: blockConfig,
                isValid: this.validateBlockData(block, blockConfig),
                isVisible: block.visible,
            };

            return total;
        }, {});

        return blocksMap;
    }

    initBlocksPreviewState() {
        const blocksMap = deepClone(this.state.blocksMap);

        Object.entries(blocksMap).forEach(([id, meta]) => {
            meta.preview = this.getBlockPreviewNode(id).innerHTML;
        });

        this.setState(() => ({ blocksMap, shouldUpdateBlocksPreviewState: false }));
    }

    getLastBlockTypeNumber(type) {
        let highestValue = 0;

        for (const value of this.state.blocksIdMap.values()) {
            if (value.includes(type)) {
                const lastDigits = value.split('#').at(-1).replace(')', '');
                const lastNumber = parseInt(lastDigits, 10);

                if (lastNumber > highestValue) {
                    highestValue = lastNumber;
                }
            }
        }

        return highestValue;
    }

    getBlocksFromZones() {
        return this.state.fieldValue.zones.reduce((total, zone) => [...total, ...zone.blocks], []);
    }

    validateAllBlocksData() {
        const { areValid, blocksMap } = this.areBlocksValid();

        if (areValid) {
            return true;
        }

        this.setState(() => ({ blocksMap }));

        return false;
    }

    validateLayoutData() {
        return this.state.isValidActiveLayout;
    }

    setBlockValidState(block) {
        const isValid = this.validateBlockData(block, this.state.blocksMap[block.id].config);

        return { ...block, isValid };
    }

    areBlocksValid() {
        const blocksMap = this.getBlocksMapState();

        this.state.fieldValue.zones.forEach((zone) => {
            zone.blocks.forEach((block) => {
                const isValid = this.validateBlockData(block, blocksMap[block.id].config);

                if (!isValid) {
                    blocksMap[block.id] = { ...deepClone(blocksMap[block.id]), isValid: false };
                }

                blocksMap[block.id].shouldDisplayError = !blocksMap[block.id].isValid;
            });
        });

        return {
            blocksMap,
            areValid: Object.values(blocksMap).every((block) => block.isValid),
        };
    }

    validateBlockData(block, config) {
        if (config.visible) {
            return block.attributes.every((attribute) => {
                const attrConfig = config.attributes.find((attributeConfig) => attributeConfig.id === attribute.name);

                if (attrConfig?.constraints?.not_blank) {
                    const { value } = attribute;

                    if (value === null || value === undefined || value === '') {
                        return false;
                    }

                    return !!`${value}`.trim().length;
                }

                return true;
            });
        }

        return false;
    }

    updateBlocksPreview(blockIds, emitChangeEvent = true) {
        const blocksMap = this.getBlocksMapState();
        const hasBlockIds = Array.isArray(blockIds) && blockIds.length;
        blockIds = hasBlockIds ? blockIds : this.getBlocksFromZones().map((block) => block.id);
        const page = this.stringifyPageFieldTypeState();
        const now = new Date().getTime();
        const referenceTimestamp = Math.max(now, this.state.timestamp);
        const promise = getBlockPreview(
            {
                page,
                blockIds,
                intent: this.props.mode,
                parameters: {
                    ...this.props.blockPreviewRequestParams,
                    intentParameters: this.props.intentParameters,
                    pagePreview: {
                        referenceTimestamp: Math.floor(referenceTimestamp / 1000),
                        ...this.state.blockPreviewPagePreviewRequestParams,
                    },
                },
            },
            this.props.previewSiteaccess,
        );
        const blocksToUpdate = blockIds.reduce(
            (output, id) => ({
                ...output,
                [id]: {
                    html: `
                        <div class="c-pb-block-preview__loader-container">
                            <div class="c-pb-block-preview__loader"></div>
                        </div>
                    `,
                    data: {
                        visible: blocksMap[id].isVisible,
                        isNew: true,
                        loading: true,
                    },
                },
            }),
            {},
        );

        this.setPageFieldTypeFormFieldValue(page, emitChangeEvent);

        this.updateBlocksPreviewState({
            blocks: blocksToUpdate,
        });

        promise
            .then((response) => response.json())
            .then((data) => this.updateBlocksPreviewState(data, true))
            .then((detail) => {
                setTimeout(() => {
                    this.getPreviewDocument().body.dispatchEvent(
                        new CustomEvent('ibexa-post-update-blocks-preview', {
                            detail: {
                                fieldValue: detail.fieldValue,
                                blocksMap: detail.blocksMap,
                                blockIds: detail.blockIds,
                            },
                        }),
                    );
                }, 0);
            })
            .catch((error) => {
                const message = Translator.trans(/*@Desc("Cannot update blocks preview")*/ 'block.cannot_update', {}, 'ibexa_page_builder');

                this.addNotification({ message, type: NOTIFICATION_TYPE.ERROR });
                console.error('Cannot update blocks preview', error);
            });
    }

    updateBlocksPreviewState(data) {
        const blockIds = [];
        const blocksMap = this.getBlocksMapState();
        const fieldValue = this.getPageFieldTypeState();

        Object.entries(data.blocks).forEach(([id, meta]) => {
            if (!blocksMap[id]) {
                return;
            }

            const [block] = fieldValue.zones
                .reduce((total, zone) => [...total, zone.blocks.find((blockEntry) => blockEntry.id === id)], [])
                .filter((blockEntry) => blockEntry);

            blocksMap[id].isNew = meta.data.isNew ?? false;
            blocksMap[id].preview = meta.html;
            blocksMap[id].isVisible = meta.data.visible;
            blocksMap[id].shouldDisplayError = true;
            blocksMap[id].isValid = this.validateBlockData(block, blocksMap[id].config);

            blockIds.push(id);
        });

        this.setState(() => ({ blocksMap }));

        return { blockIds, blocksMap, fieldValue };
    }

    updateTimelineEvents() {
        const page = this.stringifyPageFieldTypeState();
        const promise = getTimelineEvents({
            page,
            intent: this.props.mode,
            parameters: {
                ...this.props.timelineGetEventsRequestParams,
                intentParameters: this.props.intentParameters,
                pagePreview: {
                    referenceTimestamp: parseInt(this.state.timestamp / 1000, 10),
                },
            },
        });
        promise.then((response) => response.json()).then(this.updateTimelineEventsState);
    }

    updateTimelineEventsState(data) {
        this.setState(() => ({ timelineEvents: data.events }));
    }

    getBlocksMapState() {
        return deepClone(this.state.blocksMap);
    }

    getPageFieldTypeState() {
        return deepClone(this.state.fieldValue);
    }

    stringifyPageFieldTypeState() {
        return JSON.stringify(this.state.fieldValue);
    }

    setPageFieldTypeFormFieldValue(page, emitChangeEvent = true) {
        const fieldSelector = this.props.pageFieldValueSelector.trim();

        if (!fieldSelector || !fieldSelector.length) {
            console.error('Missing page fieldtype form field selector!');

            return;
        }

        const pageFieldInput = document.querySelector(fieldSelector);

        pageFieldInput.value = page;

        if (emitChangeEvent) {
            pageFieldInput.dispatchEvent(new CustomEvent('change'));
        }
    }

    handleBlockDataUpdate({ blockData, isVisible }) {
        const fieldValue = this.getPageFieldTypeState();
        const blocksMap = this.getBlocksMapState();

        fieldValue.zones = fieldValue.zones.map((zone) => {
            zone.blocks = zone.blocks.map((block) => {
                if (block.id === blockData.id) {
                    block = blockData;
                }

                return { ...block };
            });

            return zone;
        });

        blocksMap[blockData.id].isVisible = !!isVisible;

        this.setState(
            () => ({ fieldValue, blocksMap }),
            () => {
                this.updateBlocksPreview([blockData.id]);
                this.updateTimelineEvents();
            },
        );
    }

    getPreviewWindow() {
        return this._refIframeComponent._refIframe.contentWindow;
    }

    getPreviewDocument() {
        return this._refIframeComponent._refIframe.contentDocument;
    }

    getBlockPreviewNode(id) {
        return this.getPreviewDocument().querySelector(`[data-ez-block-id="${id}"]:not(${SELECTOR_PLACEHOLDER})`);
    }

    getPlaceholderPreviewNode() {
        return this.getPreviewDocument().querySelector(SELECTOR_PLACEHOLDER);
    }

    removeBlockDataFromZone(zones, blockId) {
        return zones.map((zone) => {
            zone.blocks = zone.blocks.filter((block) => block.id !== blockId);

            return zone;
        });
    }

    insertBlockDataIntoZone(zones, newBlock, zoneId, nextBlockId) {
        const updatedZones = zones.map((zone) => {
            const isSameZone = zone.id === zoneId;

            if (isSameZone && nextBlockId) {
                const nextBlock = zone.blocks.find((block) => block.id === nextBlockId);
                const insertIndex = nextBlock ? zone.blocks.indexOf(nextBlock) : 0;

                zone.blocks.splice(insertIndex, 0, newBlock);
            } else if (isSameZone && !nextBlockId) {
                zone.blocks = [...zone.blocks, newBlock];
            }

            return zone;
        });

        this.handleOnBlockClick(newBlock.id);

        return updatedZones;
    }

    getBlock(target) {
        return this.props.getElement(target, (element) => element.dataset && element.dataset.ezBlockId);
    }

    handleBlockRemove(block, emitChangeEvent = true) {
        const fieldValue = this.getPageFieldTypeState();
        const blocksMap = this.getBlocksMapState();

        fieldValue.zones = this.removeBlockDataFromZone(fieldValue.zones, block.id);

        delete blocksMap[block.id];

        this.setState(
            () => ({ draggedSidebarBlockType: null, draggedPreviewBlockData: null, fieldValue, blocksMap }),
            () => {
                this.setPageFieldTypeFormFieldValue(this.stringifyPageFieldTypeState(), emitChangeEvent);
                this.updateTimelineEvents();
            },
        );
    }

    handleDrop(event) {
        event.preventDefault();
        event.stopPropagation();

        this.unsetDocumentDragOverEventHandler();

        if (this.state.draggedSidebarBlockType) {
            this.handleDropSidebarBlock();
        } else if (this.state.draggedPreviewBlockData) {
            this.handleDropPreviewBlock();
        }

        this.clearZoneDragOverState(event.currentTarget);
    }

    clearZoneDragOverState(zone) {
        zone.classList.remove(CLASS_ZONE_DRAGOVER);

        return zone;
    }

    cancelDropState() {
        const initDragDropState = this.props.getInitDragDropState();

        this.unsetDocumentDragOverEventHandler();

        this.setState(() => ({
            ...initDragDropState,
            draggedPreviewBlockData: null,
            draggedSidebarBlockType: null,
            isOverZone: false,
            dragOverZoneId: null,
        }));
    }

    handleDropPreviewBlock() {
        const placeholder = this.getPlaceholderPreviewNode();

        if (!placeholder) {
            return this.cancelDropState();
        }

        const data = this.state.draggedPreviewBlockData;
        const { zoneId } = placeholder.dataset;
        let nextBlockId = this.props.findNextElementIdentifier(placeholder, IDENTIFIER_BLOCK_DATA_ATTRIBUTE);
        const fieldValue = this.getPageFieldTypeState();
        const initDragDropState = this.props.getInitDragDropState();
        const droppedBlockId = data.block.id;
        const dragImage = document.querySelector(`[data-ibexa-toolbox-block-type="${data.meta.config.type}"]`);
        const blocksMap = this.getBlocksMapState();

        if (nextBlockId === droppedBlockId) {
            fieldValue.zones.forEach((zone) => {
                const nextBlockData = zone.blocks.find((block) => block.id === nextBlockId);

                if (nextBlockData) {
                    const afterNextBlockIndex = zone.blocks.indexOf(nextBlockData) + 1;

                    nextBlockId = zone.blocks[afterNextBlockIndex] ? zone.blocks[afterNextBlockIndex].id : null;
                }
            });
        }

        fieldValue.zones = this.removeBlockDataFromZone([...fieldValue.zones], data.block.id);
        fieldValue.zones = this.insertBlockDataIntoZone(fieldValue.zones, data.block, zoneId, nextBlockId);
        blocksMap[data.block.id].isNew = false;

        data.unMountBlock();

        placeholder.classList.add(...data.wrapperClasses);
        placeholder.classList.remove(CLASS_PLACEHOLDER);
        placeholder.dataset.ezBlockId = data.block.id;
        dragImage.hidden = true;

        this.setState(
            () => ({ ...initDragDropState, draggedPreviewBlockData: null, draggedSidebarBlockType: null, fieldValue, blocksMap }),
            () => this.setPageFieldTypeFormFieldValue(this.stringifyPageFieldTypeState()),
        );
    }

    handleDropSidebarBlock() {
        const placeholder = this.getPlaceholderPreviewNode();

        if (!placeholder) {
            return this.cancelDropState();
        }

        const config = this.props.blocksConfig.find((configEntry) => configEntry.type === this.state.draggedSidebarBlockType);
        const { zoneId } = placeholder.dataset;
        const nextBlockId = this.props.findNextElementIdentifier(placeholder, IDENTIFIER_BLOCK_DATA_ATTRIBUTE);
        const newBlock = {
            id: generateGuid('b-'),
            type: config.type,
            name: config.name,
            view: Object.keys(config.views)[0],
            attributes: config.attributes.reduce((total, attr) => {
                total.push({
                    id: generateGuid('a-'),
                    name: attr.id,
                    value: attr.value || null,
                });

                return total;
            }, []),
        };
        const fieldValue = this.getPageFieldTypeState();
        const blocksMap = this.getBlocksMapState();
        const initDragDropState = this.props.getInitDragDropState();
        const blocksIdMap = new Map(this.state.blocksIdMap);
        const blockName = this.state.blocksTypeMap.get(newBlock.type);
        const lastBlockTypeNumber = this.getLastBlockTypeNumber(blockName);

        fieldValue.zones = this.insertBlockDataIntoZone(fieldValue.zones, newBlock, zoneId, nextBlockId);
        blocksMap[newBlock.id] = { ...BLOCKS_BASE_STATE, config, isVisible: true, isNew: true };
        blocksIdMap.set(newBlock.id, `${blockName} #${lastBlockTypeNumber + 1}`);

        placeholder.classList.remove(CLASS_PLACEHOLDER);
        placeholder.classList.add(CLASS_HIGHLIGHT);
        placeholder.dataset.ezBlockId = newBlock.id;

        global.setTimeout(() => {
            const placeholders = this.getPreviewDocument().querySelectorAll('.c-pb-block-preview--highlighted');

            placeholders.forEach((placeholderElement) => {
                placeholderElement.classList.remove(CLASS_HIGHLIGHT);
            });
        }, TIMEOUT_REMOVE_HIGHLIGHT);

        this.setState(
            () => ({
                ...initDragDropState,
                draggedPreviewBlockData: null,
                draggedSidebarBlockType: null,
                fieldValue,
                blocksMap,
                blocksIdMap,
            }),
            () => this.setPageFieldTypeFormFieldValue(this.stringifyPageFieldTypeState()),
        );
    }

    setClientYPosition({ clientY }) {
        this._clientY = clientY;
    }

    setDocumentDragOverEventHandler() {
        this.getPreviewDocument().addEventListener('dragover', this.setClientYPosition, false);
    }

    unsetDocumentDragOverEventHandler() {
        this.getPreviewDocument().removeEventListener('dragover', this.setClientYPosition);
    }

    handleDragStartSidebarBlock(draggedSidebarBlockType) {
        this.setDocumentDragOverEventHandler();
        this.setState(
            () => ({
                draggedSidebarBlockType,
                draggedPreviewBlockData: null,
            }),
            this.attachDocumentDropListeners,
        );
    }

    handleDragStartPreviewBlock(draggedPreviewBlockData) {
        this.setDocumentDragOverEventHandler();
        this.setState(
            () => ({
                draggedSidebarBlockType: null,
                draggedPreviewBlockData,
            }),
            this.attachDocumentDropListeners,
        );
    }

    attachDocumentDropListeners() {
        const elements = [this.getPreviewDocument(), document];

        elements.forEach((element) => {
            element.ondragover = () => this.props.removePlaceholdersAfterTimeout(this.getPlaceholderNodes, this.handleDragOverTimeout);
        });
    }

    handleDragOverTimeout() {
        this.cancelDropState();
    }

    handleIframeLoad() {
        if (this.state.iframeLoaded) {
            return;
        }

        const previewDoc = this.getPreviewDocument();
        const link = previewDoc.createElement('link');

        link.id = 'ibexa-pb-app-styles';
        link.rel = 'stylesheet';
        link.href = this.props.iframeCss;

        this.handleUndoRedoFromKeyboard();
        this.getPreviewDocument().body.appendChild(link);

        this.setState(
            () => ({ iframeLoaded: true }),
            () => {
                document.body.dispatchEvent(new CustomEvent('ibexa-pb-app-iframe-loaded'));
                document.body.classList.remove(CLASS_PAGE_BUILDER_DISABLED);
                previewDoc.onbeforeunload = () => this.setState(() => ({ iframeLoaded: false }));
            },
        );
    }

    handleUndoRedoFromKeyboard() {
        const previewDoc = this.getPreviewDocument();

        const handleKeyboard = (event) => {
            const { activeElement } = event.currentTarget;
            const isInputActiveElement = activeElement.tagName.toLowerCase() === 'input';
            const isTextAreaActiveElement = activeElement.tagName.toLowerCase() === 'textarea';

            if (isInputActiveElement || isTextAreaActiveElement) {
                return;
            }

            const isUndoPressed = ibexa.helpers.system.isUndoPressed(event);
            const isRedoPressed = ibexa.helpers.system.isRedoPressed(event);

            if (isUndoPressed) {
                event.preventDefault();
                document.body.dispatchEvent(new CustomEvent('ibexa-pb-history:undo'));
            }

            if (isRedoPressed) {
                event.preventDefault();
                document.body.dispatchEvent(new CustomEvent('ibexa-pb-history:redo'));
            }
        };

        previewDoc.addEventListener('keydown', handleKeyboard, false);
        document.addEventListener('keydown', handleKeyboard, false);
    }

    handleZoneDragOver({ target }) {
        const { dragOverZoneId, placeholderType } = this.state;
        const zoneId = target.dataset.ibexaZoneId;

        if (!zoneId || (zoneId === dragOverZoneId && placeholderType === PLACEHOLDER_TYPE_ZONE)) {
            return false;
        }

        this.setState(
            () => ({ placeholderType: PLACEHOLDER_TYPE_ZONE, dragOverZoneId: zoneId, isOverZone: true }),
            () => {
                this.getPreviewDocument().querySelectorAll(SELECTOR_ZONE).forEach(this.clearZoneDragOverState);

                target.classList.add(CLASS_ZONE_DRAGOVER);

                this.props.addPlaceholderInZone(
                    target,
                    this.getPlaceholderNodes(),
                    IDENTIFIER_BLOCK_DATA_ATTRIBUTE,
                    this.handleElementDragOver,
                );
            },
        );
    }

    getPlaceholderNodes() {
        return this.getPreviewDocument().querySelectorAll(SELECTOR_PLACEHOLDER);
    }

    handleElementDragOver({ target, clientY, currentTarget }) {
        const block = this.getBlock(target);
        const isPlaceholder = currentTarget.classList.contains(CLASS_PLACEHOLDER);

        if (!block || isPlaceholder) {
            return;
        }

        block.closest(SELECTOR_ZONE).classList.add(CLASS_ZONE_DRAGOVER);

        const placeholders = this.getPlaceholderNodes();
        const { placeholderPosition, placeholderElementId, placeholderZoneId } = this.state;
        const placeholderMeta = this.props.addPlaceholderBesideElement(
            block,
            clientY,
            placeholders,
            IDENTIFIER_BLOCK_DATA_ATTRIBUTE,
            this.handleElementDragOver,
            { placeholderPosition, placeholderElementId, placeholderZoneId },
        );

        this.setState(() => ({ placeholderType: 'block', ...placeholderMeta }));
    }

    toggleZoneColor(zone) {
        const noBlocks = [...zone.children].every((zoneItem) => {
            return (
                zoneItem.classList.contains('m-page-builder__fieldset') || zoneItem.classList.contains('c-pb-block-preview--is-removing')
            );
        });

        zone.classList.toggle(CLASS_ZONE_EMPTY, noBlocks);
    }

    manageZoneEventHandlers() {
        const { noDropzones, notificationsMap } = this.state;
        const zones = [...this.getPreviewDocument().querySelectorAll(SELECTOR_ZONE)];
        const callToActionText = Translator.trans(/*@Desc("Drag and drop blocks here")*/ 'drag.drop.blocks.here', {}, 'ibexa_page_builder');
        const noZonesNotificationAdded = notificationsMap.has('notification-no-zones');

        if (!zones.length && !noDropzones && !noZonesNotificationAdded) {
            const message = Translator.trans(
                /*@Desc("There is no added zones in template")*/ 'notification.no_zones_in_template',
                {},
                'ibexa_page_builder',
            );

            this.addNotification({ id: 'notification-no-zones', message, type: NOTIFICATION_TYPE.WARNING, noCloseBtn: true });
        }

        zones.forEach((zone, index) => {
            const hasFieldset = zone.querySelector('fieldset') !== null;

            if (!hasFieldset) {
                const dropZoneLabel = Translator.trans(
                    /*@Desc("Drop zone %number%")*/ 'structure.drop.zone',
                    {
                        number: index + 1,
                    },
                    'ibexa_page_builder',
                );
                const fieldset = document.createElement('fieldset');
                const legend = document.createElement('legend');

                fieldset.classList.add('m-page-builder__fieldset');
                legend.classList.add('m-page-builder__legend');
                fieldset.appendChild(legend);
                legend.textContent = dropZoneLabel;
                zone.insertBefore(fieldset, zone.firstChild);
                zone.classList.add(CLASS_ZONE);
            }

            this.toggleZoneColor(zone);
            zone.dataset.callToActionText = callToActionText;
            zone.ondragover = this.handleZoneDragOver;
            zone.ondragenter = this.handleZoneDragOver;
            zone.ondrop = this.handleDrop;
        });
    }

    disableInIframeClicks() {
        this.getPreviewDocument().body.addEventListener('click', (event) => event.preventDefault(), false);
        this.getPreviewDocument().body.addEventListener('click', this.handleClickOutsideBlock, false);
    }

    renderPreviewBlocks() {
        this.getBlocksFromZones().forEach(this.renderSinglePreviewBlock);
    }

    renderSinglePreviewBlock(block) {
        const blockMeta = { ...this.state.blocksMap[block.id] };
        const blockNode = this.getBlockPreviewNode(block.id);
        const { convertDateToTimezone } = window.ibexa.helpers.timezone;
        const isEditable = this.state.timestamp <= convertDateToTimezone(new Date()).valueOf() && this.state.isValidActiveLayout;
        const blockConfig = this.props.blocksConfig.find((blockConfigEntry) => blockConfigEntry.type === block.type);
        const zoneId = blockNode.closest(SELECTOR_ZONE).dataset.ibexaZoneId;
        const blockNo = this.state.blocksIdMap.get(block.id);
        const label = (
            <>
                <strong>{blockNo}:</strong> {block.name}
            </>
        );

        if (blockMeta.isNew) {
            if (window.getComputedStyle(blockNode).getPropertyValue('position') === 'static') {
                blockNode.style.position = 'relative';
            }

            this.props.customizeNewBlockNode(blockNode, {
                blockType: blockMeta.config.type,
                pageLayoutIdentifier: this.state.fieldValue.layout,
                zoneId,
            });
        }

        blockNode.ibexaBlockRoot = blockNode.ibexaBlockRoot ?? ReactDOMClient.createRoot(blockNode);
        blockNode.ibexaBlockRoot.render(
            <PreviewBlock
                key={block.id}
                ref={(node) => {
                    this._blockPreviewRefs.set(block.id, node);
                }}
                label={label}
                blockNo={blockNo}
                block={block}
                root={blockNode.ibexaBlockRoot}
                meta={blockMeta}
                onDragOver={this.handleElementDragOver}
                onDragStart={this.handleDragStartPreviewBlock}
                onDrag={this.handleDragBlock}
                onRemove={this.handleBlockRemove}
                onBlockDataUpdate={this.handleBlockDataUpdate}
                onConfigPopupOpen={this.requestForm}
                onDuplicate={this.duplicatePreviewBlock.bind(null, zoneId)}
                udwContainer={this.udwContainer}
                airtimeContainer={this.airtimeContainer}
                previewWindow={this.getPreviewWindow()}
                isEditable={isEditable}
                isActive={this.state.activeBlockId === block.id}
                onClick={this.handleOnBlockClick}
                isAvailable={blockConfig.visible}
            />,
        );
    }

    handleDragBlock() {
        this.props.scrollContainer({ clientY: this._clientY }, this.getPreviewWindow());
    }

    requestForm(block) {
        this.blockConfigRequestForm.target = block.id;
        this.blockConfigTextarea.value = JSON.stringify(this.state.fieldValue);
        this.blockConfigBlocksInput.value = block.id;
        this.blockConfigRequestForm.submit();
    }

    getBlocksData(zones) {
        return zones.map((zone) => ({
            ...zone,
            blocks: zone.blocks.map((block) => block.data),
        }));
    }

    handleLayoutSelectorCancelExternal() {
        const { isValidActiveLayout } = this.state;
        const isCreateMode = this.isCreateMode();

        if (isCreateMode || !isValidActiveLayout) {
            return false;
        }

        this.setState({ isSwitchingLayout: false, hasLayoutValidationError: false });

        return true;
    }

    handleLayoutSelectorCancelInternal() {
        this.setState({
            isSwitchingLayout: !document.dispatchEvent(new CustomEvent('ibexa-pb-config-panel-close-itself')),
            hasLayoutValidationError: false,
        });
    }

    handleLayoutSelectorCancelOnCreate() {
        const previousHref = window.ibexa.pageBuilder.config.onCreateCancelURL;

        if (previousHref.length) {
            window.location.href = previousHref;
        } else {
            window.history.back();
        }
    }

    handleLayoutSelectorConfirm(layoutId) {
        const zones = this.createZonesStructure(layoutId);
        const fieldValue = this.getPageFieldTypeState();
        const { isSwitchingLayout } = this.state;

        fieldValue.layout = layoutId;
        fieldValue.zones = zones;

        this.setState(
            () => ({
                iframeLoaded: false,
                isSwitchingLayout: !document.dispatchEvent(new CustomEvent('ibexa-pb-config-panel-close-itself')),
                hasLayoutValidationError: false,
                shouldCreateBlocksPreviewNodes: isSwitchingLayout,
                fieldValue,
                layoutSelected: true,
                isValidActiveLayout: this.isValidActiveLayout(fieldValue),
            }),
            () => {
                this.setPageFieldTypeFormFieldValue(this.stringifyPageFieldTypeState());
                window.document.querySelector(SELECTOR_SAVE_DRAFT).click();
            },
        );
    }

    createZonesStructure(layoutId) {
        const layoutConfig = this.props.layoutsConfig.find((layout) => layout.id === layoutId);
        const fieldValue = this.getPageFieldTypeState();
        const zones = [...layoutConfig.zones].map((zone) => {
            zone.blocks = [];

            return zone;
        });

        if (this.state.isSwitchingLayout) {
            fieldValue.zones.forEach((zone, index) => {
                const zoneToAddBlocks = zones[index] || zones[zones.length - 1];

                zoneToAddBlocks.blocks = [...zoneToAddBlocks.blocks, ...zone.blocks];
            });
        }

        return zones;
    }

    setSettingsModalVisible() {
        this.setState((state) => ({ ...state, isSettingsModalVisible: !state.isSettingsModalVisible }));
    }

    goToSettings() {
        const editDropdownId = '#user_setting_update_block_settings';

        window.location.href = `${Routing.generate('ibexa.user_settings.update', {
            identifier: 'content_edit',
        })}${editDropdownId}`;
    }

    renderSettingsModal() {
        const { isSettingsModalVisible } = this.state;
        const { isPageBuilderVisited = false } = ibexa.adminUiConfig;

        if (!isSettingsModalVisible || !this.modalContainer || isPageBuilderVisited) {
            return null;
        }

        return createPortal(
            <SettingsPopup
                isVisible={isSettingsModalVisible}
                onContinue={this.setSettingsModalVisible}
                onChangeSettings={this.goToSettings}
            />,
            this.modalContainer,
        );
    }

    renderLayoutSelector() {
        const { fieldValue, layoutSelectorTitle, layoutSelectorConfirmBtnLabel, isSwitchingLayout, layoutSelected, isValidActiveLayout } =
            this.state;
        const { layoutsConfig } = this.props;
        const isOnlyOneLayout = this.isOnlyOneLayout();
        const isCreateMode = this.isCreateMode();

        if (layoutSelected) {
            return null;
        }

        if (isOnlyOneLayout && isCreateMode) {
            this.handleLayoutSelectorConfirm(fieldValue.layout);

            return null;
        }

        if (!isCreateMode && !isSwitchingLayout) {
            return null;
        }

        const attrs = {
            layouts: layoutsConfig,
            onCancel: isCreateMode ? this.handleLayoutSelectorCancelOnCreate : this.handleLayoutSelectorCancelInternal,
            onConfirm: this.handleLayoutSelectorConfirm,
            modalContainer: this.modalContainer,
            itemSelectedId: fieldValue.layout,
            title: layoutSelectorTitle,
            confirmBtnLabel: layoutSelectorConfirmBtnLabel,
            isVisible: isSwitchingLayout,
            isValidActiveLayout,
            isCreateMode,
            displaySaveDraftWarning: !isCreateMode,
        };

        return createPortal(<LayoutSelector {...attrs} />, this.pageBuilderConfigPanelWrapper);
    }

    renderLayoutSwitcher() {
        if (this.isOnlyOneLayout()) {
            return null;
        }

        const { isSwitchingLayout, isValidActiveLayout, hasLayoutValidationError } = this.state;
        const onClick = isSwitchingLayout ? this.handleLayoutSelectorCancelInternal : this.showLayoutSelector;

        return createPortal(
            <LayoutSwitcher
                isSwitchingLayout={isSwitchingLayout}
                onClick={onClick}
                isDisabled={!isValidActiveLayout}
                hasErrorState={hasLayoutValidationError && !isSwitchingLayout}
            />,
            this.layoutSwitcherContainer,
        );
    }

    isOnlyOneLayout() {
        return this.props.layoutsConfig.filter((layout) => layout.visible).length === 1;
    }

    setIsEditableState(oldTimestamp, selectedTimestamp) {
        const blocksIds = Object.keys(this.state.blocksMap);

        this.setState(
            () => ({ timestamp: selectedTimestamp }),
            () => this.updateBlocksPreview(blocksIds),
        );
    }

    renderTimeline() {
        const { timelineEvents } = this.state;
        const { referenceTimestamp } = this.props;
        const attrs = {
            onTimelineEventSelect: this.setIsEditableState,
            events: timelineEvents,
            selectedTimestamp: referenceTimestamp * 1000,
        };

        return createPortal(<TimelineModule {...attrs} />, this.timelineContainer);
    }

    showLayoutSelector() {
        const layoutSelectorTitle = Translator.trans(/*@Desc("Switch layout")*/ 'layout_selector.switch.title', {}, 'ibexa_page_builder');
        const layoutSelectorConfirmBtnLabel = Translator.trans(
            /*@Desc("Submit")*/ 'layout_selector.switch.confirm.label',
            {},
            'ibexa_page_builder',
        );

        this.setState(() => {
            return {
                isSwitchingLayout: document.dispatchEvent(
                    new CustomEvent('ibexa-pb-config-panel-open', {
                        cancelable: true,
                        detail: { settings: { onClose: this.handleLayoutSelectorCancelExternal } },
                    }),
                ),
                hasLayoutValidationError: false,
                layoutSelected: false,
                layoutSelectorTitle,
                layoutSelectorConfirmBtnLabel,
            };
        });
    }

    renderIframe() {
        const { isIframelessMode, previewUrl, iframeLoaded } = this.state;

        if (isIframelessMode) {
            return null;
        }

        return <Iframe ref={this.setIframeRef} onLoad={this.handleIframeLoad} src={previewUrl} isLoading={!iframeLoaded} />;
    }

    renderActiveLayoutError() {
        if (this.state.isValidActiveLayout) {
            return null;
        }

        const errorMessage = Translator.trans(
            /*@Desc("The layout of this landing page is no longer available and you cannot publish it. Please select a different layout.")*/ 'layout_selector.error.label',
            {},
            'ibexa_page_builder',
        );
        const errorButtonMessage = Translator.trans(
            /*@Desc("Change layout")*/ 'layout_selector.error.change_layout',
            {},
            'ibexa_page_builder',
        );

        return (
            <div className="m-page-builder__active-layout-error">
                <div className="m-page-builder__active-layout-error-content container">
                    <div className="m-page-builder__active-layout-error-message">
                        <Icon name="warning" extraClasses="ibexa-icon--medium" />
                        {errorMessage}
                    </div>
                    <button className="btn btn-primary" onClick={this.showLayoutSelector} type="button">
                        {errorButtonMessage}
                    </button>
                </div>
            </div>
        );
    }

    setIframeRef(ref) {
        this._refIframeComponent = ref;
    }

    setSidebarSide() {
        this.setState((state) => ({ ...state, isSidebarLeftSide: !state.isSidebarLeftSide }));
    }

    scrollIntoBlockPreview(blockId) {
        const iframeDocument = this.getPreviewDocument();
        const blockPreview = iframeDocument.querySelector(`[data-ez-block-id="${blockId}"]`);

        blockPreview.scrollIntoView({ behavior: 'smooth' });
    }

    hoverInBlockPreview(blockId) {
        this.toggleHoverBlockPreview(blockId, true);
    }

    hoverOutBlockPreview(blockId) {
        this.toggleHoverBlockPreview(blockId, false);
    }

    toggleHoverBlockPreview(blockId, isHovered) {
        const iframeDocument = this.getPreviewDocument();
        const blockPreviewActionMenu = iframeDocument.querySelector(`[data-ez-block-id="${blockId}"] > .c-pb-action-menu`);

        if (blockPreviewActionMenu) {
            blockPreviewActionMenu.classList.toggle(CLASS_BLOCK_PREVIEW_HOVERED, isHovered);
        }
    }

    getPreviewBlockRef(blockId) {
        const blockPreviewRef = this._blockPreviewRefs.get(blockId);

        return blockPreviewRef;
    }

    handleStructureViewEvent(event) {
        const { blockId, action } = event.detail;
        const blockPreviewRef = this.getPreviewBlockRef(blockId);

        if (!blockPreviewRef) {
            return;
        }

        switch (action) {
            case 'remove':
                blockPreviewRef.removeBlock();

                break;
            case 'configure':
                blockPreviewRef.prepareConfigPopup(event);

                break;
            case 'refresh':
                blockPreviewRef.refreshBlock();

                break;
            case 'duplicate':
                blockPreviewRef.duplicateBlock();

                break;
        }
    }

    handleMoveEvent({ detail }) {
        const { blockId, action } = detail;
        const { zones } = this.state.fieldValue;
        const findBlockById = (findBlockId) => {
            for (const zone of zones) {
                const blockElement = zone.blocks.find((block) => block.id === findBlockId);

                if (blockElement) {
                    return blockElement;
                }
            }
            return null;
        };
        const findZoneAndBlockIndex = (findBlockId) => {
            for (let i = 0; i < zones.length; i++) {
                const blockIndex = zones[i].blocks.findIndex((block) => block.id === findBlockId);

                if (blockIndex !== -1) {
                    return { zoneIndex: i, blockIndex };
                }
            }

            return null;
        };
        const block = findBlockById(blockId);
        const { zoneIndex, blockIndex } = findZoneAndBlockIndex(blockId);
        const lastZoneIndex = zones.length - 1;
        const lastZoneBlockIndex = zones[lastZoneIndex].blocks.length > 0 ? zones[lastZoneIndex].blocks.length - 1 : 0;
        const lastBlockIndex = zones[zoneIndex].blocks.length > 0 ? zones[zoneIndex].blocks.length - 1 : 0;
        let destinationPosition = null;
        let destinationZoneKey = zoneIndex;
        let positionChangeMethod = POSITION_CHANGE_METHOD.ARROWS;

        this.hoverOutBlockPreview(blockId);

        switch (action) {
            case 'move':
                ({ destinationZoneKey, destinationPosition } = detail);
                positionChangeMethod = POSITION_CHANGE_METHOD.DRAG;

                break;
            case 'move-up':
                if (blockIndex === 0 && zoneIndex === 0) {
                    return;
                }

                destinationPosition = blockIndex - 1;

                if (blockIndex === 0) {
                    destinationZoneKey = zoneIndex - 1;
                    destinationPosition = zones[destinationZoneKey].blocks.length;
                }

                break;
            case 'move-down':
                if (blockIndex === lastZoneBlockIndex && zoneIndex === lastZoneIndex) {
                    return;
                }

                destinationPosition = blockIndex + 1;

                if (blockIndex === lastBlockIndex) {
                    destinationZoneKey = zoneIndex + 1;
                    destinationPosition = 0;
                }

                break;
        }

        this.reorderPreviewBlock(block, { destinationZoneKey, destinationPosition, positionChangeMethod }, true);
    }

    addNotification(notificationConfig) {
        this.setState((prevState) => {
            const { id: customId } = notificationConfig;
            const notificationsMap = new Map(prevState.notificationsMap);
            const id = customId ? customId : `notification-${notificationsMap.size}`;

            notificationsMap.set(id, { ...notificationConfig });

            return { notificationsMap };
        });
    }

    removeNotification(key) {
        this.setState(() => {
            const notificationsMap = new Map(this.state.notificationsMap);

            notificationsMap.delete(key);

            return { notificationsMap };
        });
    }

    renderNotifications() {
        const notificationsMapToArray = Array.from(this.state.notificationsMap);
        const handleOnClose = (key, onClose) => {
            this.removeNotification(key);
            onClose && onClose();
        };

        return (
            <>
                {notificationsMapToArray.map(([key, { message, type, onClose, noCloseBtn }]) => (
                    <Notification
                        key={key}
                        message={message}
                        type={type}
                        onClose={handleOnClose.bind(null, key, onClose)}
                        noCloseBtn={noCloseBtn}
                    />
                ))}
            </>
        );
    }

    render() {
        const pageBuilderContentClass = createCssClassNames({
            'm-page-builder__content': true,
            'm-page-builder__content--reversed': this.state.isSidebarLeftSide,
        });
        const { blocksConfig, toolboxTitle } = this.props;
        const { iframeLoaded, timestamp } = this.state;
        const { convertDateToTimezone } = window.ibexa.helpers.timezone;
        const blocksToRender = !!blocksConfig.length ? blocksConfig.filter((config) => config.visible) : [];
        const isAddingBlocksEnabled =
            timestamp <= convertDateToTimezone(new Date()).valueOf() && iframeLoaded && this.state.isValidActiveLayout;
        const blockPreviewPagePreviewRequestParams = deepClone(this.state.blockPreviewPagePreviewRequestParams);

        return (
            <ErrorBoundary>
                <div className="m-page-builder">
                    {iframeLoaded && this.renderSettingsModal()}
                    {this.modalContainer && this.renderLayoutSelector()}
                    {this.layoutSwitcherContainer && this.renderLayoutSwitcher()}
                    {this.timelineContainer && this.renderTimeline()}
                    {this.renderActiveLayoutError()}
                    <div className={pageBuilderContentClass}>
                        <div className="m-page-builder__preview-wrapper">
                            <div className="m-page-builder__notifications" role="alert">
                                {this.renderNotifications()}
                            </div>
                            <div className="m-page-builder__preview">{this.renderIframe()}</div>
                        </div>
                        <Toolbox setSidebarSide={this.setSidebarSide}>
                            <BlocksToolbox
                                name="elements"
                                iconName="block-add"
                                title={toolboxTitle}
                                blocksToRender={blocksToRender}
                                onBlockDrag={this.handleDragBlock}
                                onBlockDragStart={this.handleDragStartSidebarBlock}
                                isAddingBlocksEnabled={isAddingBlocksEnabled}
                            />
                            <StructureToolbox
                                name="structure"
                                iconName="content-tree"
                                title={Translator.trans(/*@Desc("Structure view")*/ 'toolbox.structure.view', {}, 'ibexa_page_builder')}
                                fieldValue={this.state.fieldValue}
                                scrollTo={this.scrollIntoBlockPreview}
                                hoverIn={this.hoverInBlockPreview}
                                hoverOut={this.hoverOutBlockPreview}
                                blocksIdMap={this.state.blocksIdMap}
                                blocksIconMap={this.state.blocksIconMap}
                            />
                        </Toolbox>
                        <HiddenBlocks blocksToRender={blocksToRender} />
                    </div>
                    <div className="m-page-builder__block-config-wrapper" />
                    {this.updatePreviewRequestParamsComponents.map((CustomComponent, index) => {
                        const key = index;

                        return (
                            <CustomComponent
                                key={key}
                                updatePreviewRequestParams={this.updatePreviewRequestParams}
                                blockPreviewPagePreviewRequestParams={blockPreviewPagePreviewRequestParams}
                            />
                        );
                    })}
                </div>
            </ErrorBoundary>
        );
    }
}

const builderPropTypes = {
    getInitDragDropState: PropTypes.func.isRequired,
    getElement: PropTypes.func.isRequired,
    scrollContainer: PropTypes.func.isRequired,
    removePlaceholderWithAnimation: PropTypes.func.isRequired,
    removePlaceholderWithoutAnimation: PropTypes.func.isRequired,
    addPlaceholderBesideElement: PropTypes.func.isRequired,
    addPlaceholderInZone: PropTypes.func.isRequired,
    removePlaceholders: PropTypes.func.isRequired,
    findNextElementIdentifier: PropTypes.func.isRequired,
    insertAfter: PropTypes.func.isRequired,
    insertBefore: PropTypes.func.isRequired,
    removePlaceholdersAfterTimeout: PropTypes.func.isRequired,
};
const pageBuilderConfigPropTypes = {
    propTypes: {
        blockPreviewRequestParams: PropTypes.object,
        blocksConfig: PropTypes.array.isRequired,
        fieldValue: PropTypes.object.isRequired,
        iframeCss: PropTypes.string.isRequired,
        intentParameters: PropTypes.object,
        layoutsConfig: PropTypes.arrayOf(PropTypes.object.isRequired).isRequired,
        mode: PropTypes.string.isRequired,
        pageFieldValueSelector: PropTypes.string.isRequired,
        previewSiteaccess: PropTypes.string.isRequired,
        previewUrl: PropTypes.string.isRequired,
        referenceTimestamp: PropTypes.number.isRequired,
        timelineGetEventsRequestParams: PropTypes.object,
    },
    defaultProps: {
        blockPreviewRequestParams: {},
        timelineGetEventsRequestParams: {},
        intentParameters: {},
    },
};

PageBuilder.propTypes = {
    customizeNewBlockNode: PropTypes.func,
    ...builderPropTypes,
    ...pageBuilderConfigPropTypes.propTypes,
};

PageBuilder.defaultProps = {
    ...pageBuilderConfigPropTypes.defaultProps,
    /**
     * Extension point to customize the new block HTML attributes
     *
     * @function customizeNewBlockNode
     * @param {HTMLElement} block
     * @param {Object} meta
     * @param {String} meta.blockType
     * @param {String} meta.pageLayoutIdentifier
     * @param {String} meta.zoneId
     * @returns {HTMLElement}
     */
    customizeNewBlockNode: (node, meta) => {
        if (window.ibexa.pageBuilder && window.ibexa.pageBuilder.callbacks && window.ibexa.pageBuilder.callbacks.customizeNewBlockNode) {
            return window.ibexa.pageBuilder.callbacks.customizeNewBlockNode(node, meta);
        }

        return node;
    },
};

export default PageBuilder;
