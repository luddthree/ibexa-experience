import React, { PureComponent } from 'react';
import PageBuilder from './page-builder/page.builder';
import DragDrop from './core/drag.drop';
import PageBuilderHistory, { ACTIONS, findInsertBlockPosition, findReorderBlockPositions } from './core/page.builder.history';

const { ibexa, document } = window;

class PageBuilderModule extends PureComponent {
    constructor(props) {
        super(props);

        this.pageBuilderHistoryInstance = null;

        this.validateAllBlocksData = this.validateAllBlocksData.bind(this);
        this.validateLayoutData = this.validateLayoutData.bind(this);
        this.setPageBuilderRef = this.setPageBuilderRef.bind(this);
    }

    componentDidMount() {
        const historyUndoBtn = document.querySelector('.ibexa-preview-history__action[data-history-action="undo"]');
        const historyRedoBtn = document.querySelector('.ibexa-preview-history__action[data-history-action="redo"]');
        const fieldSelector = ibexa.pageBuilder.config.pageFieldValueSelector.trim();
        const pageFieldInput = document.querySelector(fieldSelector);
        this.pageBuilderHistoryInstance = new PageBuilderHistory(pageFieldInput.value);

        window.pageBuilderHistoryInstance = this.pageBuilderHistoryInstance;

        pageFieldInput.addEventListener(
            'change',
            (event) => {
                const { value } = event.currentTarget;

                this.pageBuilderHistoryInstance.addEntry(JSON.parse(value));

                this.toggleHistoryBtn(historyUndoBtn, !this.pageBuilderHistoryInstance.isPrevEmpty);
                this.toggleHistoryBtn(historyRedoBtn, !this.pageBuilderHistoryInstance.isNextEmpty);
            },
            false,
        );
        document.body.addEventListener('ibexa-pb-history:undo', () => {
            if (this.pageBuilderHistoryInstance.isPrevEmpty) {
                return;
            }

            this.pageBuilderHistoryInstance.undo(({ value, diff }) => {
                pageFieldInput.value = JSON.stringify(value);

                this.applyDiff(diff);

                return true;
            });

            this.toggleHistoryBtn(historyRedoBtn, true);

            if (this.pageBuilderHistoryInstance.isPrevEmpty) {
                this.toggleHistoryBtn(historyUndoBtn, false);
            }
        });
        document.body.addEventListener('ibexa-pb-history:redo', () => {
            if (this.pageBuilderHistoryInstance.isNextEmpty) {
                return;
            }

            this.pageBuilderHistoryInstance.redo(({ value, diff }) => {
                pageFieldInput.value = JSON.stringify(value);

                this.applyDiff(diff);

                return true;
            });

            this.toggleHistoryBtn(historyUndoBtn, true);

            if (this.pageBuilderHistoryInstance.isNextEmpty) {
                this.toggleHistoryBtn(historyRedoBtn, false);
            }
        });
        historyUndoBtn.addEventListener('click', () => {
            document.body.dispatchEvent(new CustomEvent('ibexa-pb-history:undo'));
        });
        historyRedoBtn.addEventListener('click', () => {
            document.body.dispatchEvent(new CustomEvent('ibexa-pb-history:redo'));
        });
    }

    applyDiff(diff) {
        switch (diff.action) {
            case ACTIONS.ADD:
                this._refPageBuilder.removePreviewBlock(diff.block.id);

                break;
            case ACTIONS.DELETE: {
                const blockPosition = findInsertBlockPosition(diff);

                this._refPageBuilder.insertPreviewBlock(diff.block, {
                    zoneKey: diff.zoneKey,
                    blockPosition,
                    isReverted: true,
                });

                break;
            }
            case ACTIONS.REORDER: {
                const blocksPositions = findReorderBlockPositions(diff);

                this._refPageBuilder.reorderPreviewBlock(diff.block, blocksPositions);

                break;
            }
            case ACTIONS.UPDATE:
                this._refPageBuilder.updatePreviewBlock(diff.block.id);

                break;
        }
    }

    toggleHistoryBtn(btn, isEnabled) {
        const tooltipWrapperNode = btn.querySelector('.ibexa-preview-history__tooltip-wrapper');

        btn.disabled = !isEnabled;
        tooltipWrapperNode.dataset.bsOriginalTitle = isEnabled
            ? tooltipWrapperNode.dataset.enabledTitle
            : tooltipWrapperNode.dataset.disabledTitle;
        ibexa.helpers.tooltips.parse(btn);
    }

    validateAllBlocksData() {
        return this._refPageBuilder.validateAllBlocksData();
    }

    validateLayoutData() {
        return this._refPageBuilder.validateLayoutData();
    }

    setPageBuilderRef(ref) {
        this._refPageBuilder = ref;
    }

    render() {
        return (
            <DragDrop
                render={(methods) => {
                    return <PageBuilder ref={this.setPageBuilderRef} {...this.props} {...methods} />;
                }}
            />
        );
    }
}

export default PageBuilderModule;

ibexa.addConfig('modules.PageBuilder', PageBuilderModule);
