import React, { useContext, useState, useRef, useEffect } from 'react';

import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

import {
    CanvasContext,
    TechnicalCanvasContext,
    ImageHistoryContext,
    AdditionalDataContext,
    ActiveActionContext,
} from '../../../image.editor.modules';

const { Translator, ibexa } = window;

const IDENTIFIER = 'focal-point';
const FOCAL_COLOR = '#4191FF';
const FOCAL_COLOR_ALPHA = '#4191FF20';

const FocalPoint = () => {
    const [canvas] = useContext(CanvasContext);
    const [technicalCanvas] = useContext(TechnicalCanvasContext);
    const [imageHistory, dispatchImageHistoryAction] = useContext(ImageHistoryContext);
    const [additionalData, setAdditionalData] = useContext(AdditionalDataContext);
    const [activeAction, setActiveAction] = useContext(ActiveActionContext);
    const refInputPositionX = useRef();
    const refInputPositionY = useRef();
    const [focalPointCoordinates, setFocalPointCoordinates] = useState({
        x: canvas ? canvas.current.width / 2 : null,
        y: canvas ? canvas.current.height / 2 : null,
    });
    const btnShowClassName = createCssClassNames({
        btn: true,
        'ibexa-btn': true,
        'ibexa-btn--tertiary': true,
        'ibexa-btn--small': true,
        'c-image-editor-focal-point__btn-toggle': true,
        'c-image-editor-focal-point__btn-toggle--active': activeAction === IDENTIFIER,
    });
    const btnRemoveClassName = createCssClassNames({
        btn: true,
        'ibexa-btn': true,
        'ibexa-btn--tertiary': true,
        'ibexa-btn--small': true,
        'ibexa-btn--no-text': true,
        'c-image-editor-focal-point__btn-remove': true,
        'c-image-editor-focal-point__btn-remove--hidden': activeAction !== IDENTIFIER,
    });
    const toggleActiveAction = () => {
        const active = activeAction === IDENTIFIER ? '' : IDENTIFIER;

        setActiveAction(active);
    };
    const setCoordinatesFromInputs = () => {
        const positionX = Math.min(parseInt(refInputPositionX.current.value, 10), parseInt(refInputPositionX.current.max, 10));
        const positionY = Math.min(parseInt(refInputPositionY.current.value, 10), parseInt(refInputPositionY.current.max, 10));

        setFocalPointCoordinates({
            x: Math.round(positionX),
            y: Math.round(positionY),
        });
    };
    const setCoordinatesFromMouseEvent = (event) => {
        setFocalPointCoordinates({
            x: Math.round(event.offsetX),
            y: Math.round(event.offsetY),
        });
    };
    const resetCoordinates = () => {
        setFocalPointCoordinates({
            x: canvas ? Math.round(canvas.current.width / 2) : null,
            y: canvas ? Math.round(canvas.current.height / 2) : null,
        });
    };
    const saveFocalPoint = () => {
        setAdditionalData((previousAdditionalData) => {
            return {
                ...previousAdditionalData,
                focalPointX: focalPointCoordinates.x,
                focalPointY: focalPointCoordinates.y,
            };
        });

        dispatchImageHistoryAction({
            type: 'ADD_TO_HISTORY',
            image: imageHistory[imageHistory.length - 1].image,
            additionalData: { ...additionalData, focalPointX: focalPointCoordinates.x, focalPointY: focalPointCoordinates.y },
        });
    };
    const clearTechnicalCanvas = () => {
        if (!technicalCanvas) {
            return;
        }

        const ctx = technicalCanvas.current.getContext('2d');

        ctx.clearRect(0, 0, technicalCanvas.current.width, technicalCanvas.current.height);
    };
    const drawFocalPoint = () => {
        const ctx = technicalCanvas.current.getContext('2d');

        clearTechnicalCanvas();

        ctx.save();

        ctx.strokeStyle = FOCAL_COLOR;
        ctx.fillStyle = FOCAL_COLOR_ALPHA;
        ctx.lineWidth = 5;

        ctx.beginPath();
        ctx.arc(focalPointCoordinates.x, focalPointCoordinates.y, 100, 0, Math.PI * 2, true);
        ctx.stroke();
        ctx.fill();

        ctx.fillStyle = FOCAL_COLOR;

        ctx.beginPath();
        ctx.arc(focalPointCoordinates.x, focalPointCoordinates.y, 15, 0, Math.PI * 2, true);
        ctx.fill();

        ctx.restore();
    };
    const handleMouseDown = (event) => {
        technicalCanvas.current.addEventListener('mousemove', setCoordinatesFromMouseEvent, false);
        technicalCanvas.current.addEventListener(
            'mouseup',
            () => {
                technicalCanvas.current.removeEventListener('mousemove', setCoordinatesFromMouseEvent, false);
            },
            false,
        );

        setCoordinatesFromMouseEvent(event);
    };
    const handleMouseUp = () => {
        technicalCanvas.current.removeEventListener('mousemove', setCoordinatesFromMouseEvent, false);

        saveFocalPoint();
    };

    useEffect(() => {
        if (technicalCanvas && activeAction === IDENTIFIER) {
            technicalCanvas.current.addEventListener('mouseup', handleMouseUp, false);
        }

        return () => {
            if (technicalCanvas && technicalCanvas.current) {
                technicalCanvas.current.removeEventListener('mouseup', handleMouseUp, false);
            }
        };
    });

    useEffect(() => {
        if (additionalData.focalPointX === undefined && additionalData.focalPointY === undefined) {
            resetCoordinates();
        }
    }, [canvas]);

    useEffect(() => {
        if (activeAction === IDENTIFIER) {
            drawFocalPoint();

            if (!technicalCanvas) {
                return;
            }

            technicalCanvas.current.addEventListener('mousedown', handleMouseDown, false);
        }

        return () => {
            if (technicalCanvas && technicalCanvas.current) {
                technicalCanvas.current.removeEventListener('mousedown', handleMouseDown, false);
            }
        };
    }, [activeAction]);

    useEffect(() => {
        if (!technicalCanvas || activeAction !== IDENTIFIER) {
            return;
        }

        drawFocalPoint();
    }, [focalPointCoordinates]);

    useEffect(() => {
        if (additionalData.focalPointX === undefined && additionalData.focalPointY === undefined) {
            resetCoordinates();

            return;
        }

        setFocalPointCoordinates({
            x: additionalData.focalPointX,
            y: additionalData.focalPointY,
        });
    }, [additionalData]);

    return (
        <div className="c-image-editor-focal-point">
            <button type="button" className={btnShowClassName} onClick={toggleActiveAction}>
                <Icon name="localize" extraClasses="ibexa-icon--small" />
                {Translator.trans(/*@Desc("Show point")*/ 'image_editor.actions.focal_point.show', {}, 'ibexa_image_editor')}
            </button>
            <div className="c-image-editor-focal-point__settings">
                <div className="c-image-editor-focal-point__settings-title">
                    {Translator.trans(/*@Desc("Position")*/ 'image_editor.actions.focal_point.settings.title', {}, 'ibexa_image_editor')}
                </div>
                <div className="c-image-editor-focal-point__input-wrapper">
                    <input
                        type="number"
                        min="0"
                        max={canvas ? canvas.current.width : null}
                        value={focalPointCoordinates.x}
                        ref={refInputPositionX}
                        onChange={setCoordinatesFromInputs}
                        onBlur={saveFocalPoint}
                        readOnly={activeAction !== IDENTIFIER}
                        className="form-control c-image-editor-focal-point__input c-image-editor-focal-point__input--position-x"
                    />
                    <div className="c-image-editor-focal-point__input-label">
                        {Translator.trans(
                            /*@Desc("X")*/ 'image_editor.actions.focal_point.abbreviation.position_x',
                            {},
                            'ibexa_image_editor',
                        )}
                    </div>
                </div>
                <div className="c-image-editor-focal-point__input-wrapper">
                    <input
                        type="number"
                        min="0"
                        max={canvas ? canvas.current.height : null}
                        value={focalPointCoordinates.y}
                        ref={refInputPositionY}
                        onChange={setCoordinatesFromInputs}
                        onBlur={saveFocalPoint}
                        readOnly={activeAction !== IDENTIFIER}
                        className="form-control c-image-editor-focal-point__input c-image-editor-focal-point__input--position-y"
                    />
                    <div className="c-image-editor-focal-point__input-label">
                        {Translator.trans(
                            /*@Desc("Y")*/ 'image_editor.actions.focal_point.abbreviation.position_y',
                            {},
                            'ibexa_image_editor',
                        )}
                    </div>
                </div>
                <button
                    type="button"
                    className={btnRemoveClassName}
                    onClick={resetCoordinates}
                    title={Translator.trans(/*@Desc("Restore")*/ 'image_editor.actions.focal_point.restore', {}, 'ibexa_image_editor')}
                    data-tooltip-container-selector=".c-image-editor-focal-point"
                >
                    <Icon name="restore" extraClasses="ibexa-icon--small" />
                </button>
            </div>
        </div>
    );
};

FocalPoint.propTypes = {};

FocalPoint.defaultProps = {};

export default FocalPoint;

ibexa.addConfig(
    'imageEditor.actions.focal-point',
    {
        label: Translator.trans(/*@Desc("Focal point")*/ 'image_editor.actions.focal_point.label', {}, 'ibexa_image_editor'),
        component: FocalPoint,
        icon: ibexa.helpers.icon.getIconPath('focus-image'),
        identifier: IDENTIFIER,
    },
    true,
);
