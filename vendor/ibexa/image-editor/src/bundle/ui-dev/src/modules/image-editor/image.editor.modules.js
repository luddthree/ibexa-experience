import React, { useEffect, useLayoutEffect, useState, createContext } from 'react';
import PropTypes from 'prop-types';

import Header from './components/header/header';
import Workspace from './components/workspace/workspace';
import Sidebar from './components/sidebar/sidebar';
import ConfirmModal from './components/confirm-modal/confirm.modal';

const CLASS_SCROLL_DISABLED = 'ibexa-scroll-disabled';
const DEFAULT_IMAGE_TYPE = 'image/jpeg';

export const CancelContext = createContext();
export const ConfirmContext = createContext();
export const CanvasContext = createContext();
export const TechnicalCanvasContext = createContext();
export const ImageHistoryContext = createContext();
export const ImageRedoContext = createContext();
export const AdditionalDataContext = createContext();
export const ActiveActionContext = createContext();

import { useImageHistoryReducer } from './hooks/useImageHistoryReducer';

const ImageEditorModule = (props) => {
    const [canvas, setCanvas] = useState();
    const [technicalCanvas, setTechnicalCanvas] = useState();
    const [additionalData, setAdditionalData] = useState(props.additionalData);
    const [activeAction, setActiveAction] = useState({});
    const [isConfirmPopupVisible, setIsConfirmPopupVisible] = useState(false);
    const [imageHistory, dispatchImageHistoryAction] = useImageHistoryReducer([]);
    const [imageRedo, dispatchImageRedoAction] = useImageHistoryReducer([]);
    const getImageFormat = () => {
        const regex = /data:(.*);.*/;
        const results = props.imageUrl.match(regex);

        return results ? results[1] : DEFAULT_IMAGE_TYPE;
    };
    const confirm = (saveAsNew = false, closeEditorAfterSave = true) => {
        const image = new Image();
        const imageFormat = getImageFormat();

        image.onload = () => {
            props.onConfirm(image, additionalData, saveAsNew, closeEditorAfterSave);
        };

        image.src = canvas.current.toDataURL(imageFormat, 1.0);
    };
    const onConfirm = ({ closeEditorAfterSave }) => {
        if (props.saveAsNewPossible) {
            setIsConfirmPopupVisible(true);
        } else {
            confirm(props.saveAsNewPossible, closeEditorAfterSave);
        }
    };
    const renderConfirmPopup = () => {
        if (!isConfirmPopupVisible) {
            return;
        }

        return (
            <div className="m-image-editor__confirm-popup">
                <ConfirmModal confirm={confirm} cancel={() => setIsConfirmPopupVisible(false)} />
            </div>
        );
    };

    useEffect(() => {
        window.document.body.classList.add(CLASS_SCROLL_DISABLED);
        window.ibexa.helpers.tooltips.parse();

        return () => {
            window.document.body.classList.remove(CLASS_SCROLL_DISABLED);
            window.ibexa.helpers.tooltips.hideAll();
        };
    });

    useLayoutEffect(() => {
        if (!technicalCanvas) {
            return;
        }

        const ctx = technicalCanvas.current.getContext('2d');

        ctx.clearRect(0, 0, technicalCanvas.current.width, technicalCanvas.current.height);
    }, [activeAction]);

    return (
        <div className="m-image-editor">
            <CancelContext.Provider value={props.onCancel}>
                <ConfirmContext.Provider value={onConfirm}>
                    <CanvasContext.Provider value={[canvas, setCanvas]}>
                        <TechnicalCanvasContext.Provider value={[technicalCanvas, setTechnicalCanvas]}>
                            <ImageHistoryContext.Provider value={[imageHistory, dispatchImageHistoryAction]}>
                                <ImageRedoContext.Provider value={[imageRedo, dispatchImageRedoAction]}>
                                    <AdditionalDataContext.Provider value={[additionalData, setAdditionalData]}>
                                        <ActiveActionContext.Provider value={[activeAction, setActiveAction]}>
                                            <div className="m-image-editor__header">
                                                <Header saveAsNewPossible={props.saveAsNewPossible} imageName={props.imageName} />
                                            </div>
                                            <div className="m-image-editor__workspace">
                                                <Workspace imageUrl={props.imageUrl} />
                                            </div>
                                            <div className="m-image-editor__sidebar">
                                                <Sidebar />
                                            </div>
                                            {renderConfirmPopup()}
                                        </ActiveActionContext.Provider>
                                    </AdditionalDataContext.Provider>
                                </ImageRedoContext.Provider>
                            </ImageHistoryContext.Provider>
                        </TechnicalCanvasContext.Provider>
                    </CanvasContext.Provider>
                </ConfirmContext.Provider>
            </CancelContext.Provider>
        </div>
    );
};

ImageEditorModule.propTypes = {
    onCancel: PropTypes.func.isRequired,
    onConfirm: PropTypes.func.isRequired,
    imageUrl: PropTypes.string.isRequired,
    imageName: PropTypes.string.isRequired,
    additionalData: PropTypes.object,
    saveAsNewPossible: PropTypes.bool,
};

ImageEditorModule.defaultProps = {
    additionalData: {},
    saveAsNewPossible: false,
};

window.ibexa.addConfig('modules.ImageEditorModule', ImageEditorModule);

const imageEditorContainer = document.createElement('div');

imageEditorContainer.classList.add('ibexa-image-editor');

document.body.append(imageEditorContainer);

export default ImageEditorModule;
