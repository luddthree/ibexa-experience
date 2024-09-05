import React, { useContext } from 'react';

import SidebarAction from './sidebar.action';
import SidebarGroup from './sidebar.group';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';

import {
    CanvasContext,
    TechnicalCanvasContext,
    ImageHistoryContext,
    ImageRedoContext,
    AdditionalDataContext,
} from '../../image.editor.modules';

const { Translator } = window;

const Sidebar = () => {
    const [canvas] = useContext(CanvasContext);
    const [technicalCanvas] = useContext(TechnicalCanvasContext);
    const [imageHistory, dispatchImageHistoryAction] = useContext(ImageHistoryContext);
    const [imageRedo, dispatchImageRedoAction] = useContext(ImageRedoContext);
    const [, setAdditionalData] = useContext(AdditionalDataContext);
    const drawNewImage = (image) => {
        const ctx = canvas.current.getContext('2d');

        canvas.current.width = image.width;
        canvas.current.height = image.height;

        technicalCanvas.current.width = image.width;
        technicalCanvas.current.height = image.height;

        ctx.save();
        ctx.drawImage(image, 0, 0, image.width, image.height);
        ctx.restore();
    };
    const undo = () => {
        drawNewImage(imageHistory[imageHistory.length - 2].image);

        dispatchImageRedoAction({
            type: 'ADD_TO_HISTORY',
            image: imageHistory[imageHistory.length - 1].image,
            additionalData: imageHistory[imageHistory.length - 1].additionalData,
        });
        dispatchImageHistoryAction({ type: 'REMOVE_LAST_ITEM_FROM_HISTORY' });

        setAdditionalData(imageHistory[imageHistory.length - 2].additionalData);
    };
    const redo = () => {
        const { image } = imageRedo[imageRedo.length - 1];

        drawNewImage(image);

        dispatchImageHistoryAction({ type: 'ADD_TO_HISTORY', image, additionalData: imageRedo[imageRedo.length - 1].additionalData });
        dispatchImageRedoAction({ type: 'REMOVE_LAST_ITEM_FROM_HISTORY' });

        setAdditionalData(imageRedo[imageRedo.length - 1].additionalData);
    };
    const resetImage = () => {
        const [{ image }] = imageHistory;

        drawNewImage(image);

        dispatchImageRedoAction({ type: 'CLEAR_HISTORY' });
        dispatchImageHistoryAction({ type: 'SET_HISTORY', history: [{ image, additionalData: imageHistory[0].additionalData }] });

        setAdditionalData(imageHistory[0].additionalData);
    };

    return (
        <div className="c-image-editor-sidebar">
            <div className="c-image-editor-sidebar__header">
                <h2>{Translator.trans(/*@Desc("Edit image")*/ 'image_editor.edit_image', {}, 'ibexa_image_editor')}</h2>
            </div>
            <div className="c-image-editor-sidebar__actions">
                <div className="c-image-editor-sidebar__tools">
                    <button
                        type="button"
                        className="btn ibexa-btn ibexa-btn--tertiary"
                        onClick={undo}
                        title={Translator.trans(/*@Desc("Undo")*/ 'image_editor.undo', {}, 'ibexa_image_editor')}
                        data-tooltip-container-selector=".c-image-editor-sidebar__tools"
                        disabled={imageHistory.length <= 1}
                    >
                        <Icon name="undo" extraClasses="ibexa-icon--small" />
                    </button>
                    <button
                        type="button"
                        className="btn ibexa-btn ibexa-btn--tertiary"
                        onClick={redo}
                        title={Translator.trans(/*@Desc("Redo")*/ 'image_editor.redo', {}, 'ibexa_image_editor')}
                        data-tooltip-container-selector=".c-image-editor-sidebar__tools"
                        disabled={imageRedo.length === 0}
                    >
                        <Icon name="redo" extraClasses="ibexa-icon--small" />
                    </button>
                    <button
                        type="button"
                        className="btn ibexa-btn ibexa-btn--tertiary ibexa-btn--reset-image"
                        onClick={resetImage}
                        disabled={imageHistory.length <= 1}
                    >
                        {Translator.trans(/*@Desc("Reset image")*/ 'image_editor.reset_image', {}, 'ibexa_image_editor')}
                    </button>
                </div>
                {Object.entries(window.ibexa.adminUiConfig.imageEditor.config.actionGroups).map(([groupId, group]) => {
                    const renderActions = () => {
                        return Object.entries(group.actions).map(([actionId, action]) => {
                            const actionConfig = window.ibexa.imageEditor.actions[actionId];

                            return <SidebarAction key={action} {...actionConfig} componentConfig={action} />;
                        });
                    };

                    if (Object.keys(window.ibexa.adminUiConfig.imageEditor.config.actionGroups).length > 1) {
                        return (
                            <SidebarGroup key={groupId} label={group.label}>
                                {renderActions()}
                            </SidebarGroup>
                        );
                    }
                    return renderActions();
                })}
            </div>
        </div>
    );
};

Sidebar.propTypes = {};

Sidebar.defaultProps = {};

export default Sidebar;
