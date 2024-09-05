import React, { useContext, useState, useEffect } from 'react';
import PropTypes from 'prop-types';

import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

import {
    CanvasContext,
    TechnicalCanvasContext,
    ImageHistoryContext,
    AdditionalDataContext,
    ActiveActionContext,
} from '../../../image.editor.modules';

const { Translator } = window;

const IDENTIFIER = 'crop';
const GRID_COLOR = '#4191FF';
const POSITIVE_COLOR = '#00a42b';
const NEGATIVE_COLOR = '#db0032';
const ENTER_KEY_CODE = 13;

const Crop = (props) => {
    const ratioButtons = Object.entries(props.config.buttons);
    const [canvas] = useContext(CanvasContext);
    const [technicalCanvas] = useContext(TechnicalCanvasContext);
    const [activeAction, setActiveAction] = useContext(ActiveActionContext);
    const [, dispatchImageHistoryAction] = useContext(ImageHistoryContext);
    const [additionalData] = useContext(AdditionalDataContext);
    const [activeButton, setActiveButton] = useState('');
    const [cropSize, setCropSize] = useState({
        width: canvas ? canvas.current.width : null,
        height: canvas ? canvas.current.height : null,
    });
    const [cropPosition, setCropPosition] = useState({
        x: canvas ? canvas.current.width / 2 : null,
        y: canvas ? canvas.current.height / 2 : null,
    });
    const resizeHandlerWidth = 20;
    const btnWidth = 40;
    const btnRadius = 10;
    const acceptBtnPosition = {};
    const cancelBtnPosition = {};
    const lockIcon = activeButton === 'custom' ? 'lock-unlock' : 'lock';
    let resizeHandlers = [];
    const resizeMethods = {
        'top-left': (movementX, movementY) => {
            const cropRatio = ratioButtons.find(([ratioButtonId]) => ratioButtonId === activeButton)[1].ratio;

            setCropSize((previousCropSize) => {
                const newWidth = previousCropSize.width - movementX;
                const newHeight = cropRatio
                    ? ((previousCropSize.width - movementX) * cropRatio.y) / cropRatio.x
                    : previousCropSize.height - movementY;

                return {
                    width: newWidth,
                    height: newHeight,
                };
            });

            setCropPosition((previousCropPosition) => {
                const newPositionX = previousCropPosition.x + movementX / 2;
                const newPositionY = cropRatio
                    ? previousCropPosition.y + ((movementX / 2) * cropRatio.y) / cropRatio.x
                    : previousCropPosition.y + movementY / 2;

                return {
                    x: newPositionX,
                    y: newPositionY,
                };
            });
        },
        top: (movementX, movementY) => {
            const cropRatio = ratioButtons.find(([ratioButtonId]) => ratioButtonId === activeButton)[1].ratio;

            setCropSize((previousCropSize) => {
                const newWidth = cropRatio ? ((previousCropSize.height - movementY) * cropRatio.x) / cropRatio.y : previousCropSize.width;
                const newHeight = previousCropSize.height - movementY;

                return {
                    width: newWidth,
                    height: newHeight,
                };
            });

            setCropPosition((previousCropPosition) => {
                const newPositionX = previousCropPosition.x;
                const newPositionY = previousCropPosition.y + movementY / 2;

                return {
                    x: newPositionX,
                    y: newPositionY,
                };
            });
        },
        'top-right': (movementX, movementY) => {
            const cropRatio = ratioButtons.find(([ratioButtonId]) => ratioButtonId === activeButton)[1].ratio;

            setCropSize((previousCropSize) => {
                const newWidth = previousCropSize.width + movementX;
                const newHeight = cropRatio
                    ? ((previousCropSize.width + movementX) * cropRatio.y) / cropRatio.x
                    : previousCropSize.height - movementY;

                return {
                    width: newWidth,
                    height: newHeight,
                };
            });

            setCropPosition((previousCropPosition) => {
                const newPositionX = previousCropPosition.x + movementX / 2;
                const newPositionY = cropRatio
                    ? previousCropPosition.y - ((movementX / 2) * cropRatio.y) / cropRatio.x
                    : previousCropPosition.y + movementY / 2;

                return {
                    x: newPositionX,
                    y: newPositionY,
                };
            });
        },
        left: (movementX, movementY) => {
            const cropRatio = ratioButtons.find(([ratioButtonId]) => ratioButtonId === activeButton)[1].ratio;

            setCropSize((previousCropSize) => {
                const newWidth = previousCropSize.width - movementX;
                const newHeight = cropRatio ? ((previousCropSize.width - movementY) * cropRatio.y) / cropRatio.x : previousCropSize.height;

                return {
                    width: newWidth,
                    height: newHeight,
                };
            });

            setCropPosition((previousCropPosition) => {
                const newPositionX = previousCropPosition.x + movementX / 2;
                const newPositionY = previousCropPosition.y;

                return {
                    x: newPositionX,
                    y: newPositionY,
                };
            });
        },
        right: (movementX, movementY) => {
            const cropRatio = ratioButtons.find(([ratioButtonId]) => ratioButtonId === activeButton)[1].ratio;

            setCropSize((previousCropSize) => {
                const newWidth = previousCropSize.width + movementX;
                const newHeight = cropRatio ? ((previousCropSize.width - movementY) * cropRatio.y) / cropRatio.x : previousCropSize.height;

                return {
                    width: newWidth,
                    height: newHeight,
                };
            });

            setCropPosition((previousCropPosition) => {
                const newPositionX = previousCropPosition.x + movementX / 2;
                const newPositionY = previousCropPosition.y;

                return {
                    x: newPositionX,
                    y: newPositionY,
                };
            });
        },
        'bottom-left': (movementX, movementY) => {
            const cropRatio = ratioButtons.find(([ratioButtonId]) => ratioButtonId === activeButton)[1].ratio;

            setCropSize((previousCropSize) => {
                const newWidth = previousCropSize.width - movementX;
                const newHeight = cropRatio
                    ? ((previousCropSize.width + movementX) * cropRatio.y) / cropRatio.x
                    : previousCropSize.height + movementY;

                return {
                    width: newWidth,
                    height: newHeight,
                };
            });

            setCropPosition((previousCropPosition) => {
                const newPositionX = previousCropPosition.x + movementX / 2;
                const newPositionY = cropRatio
                    ? previousCropPosition.y - ((movementX / 2) * cropRatio.y) / cropRatio.x
                    : previousCropPosition.y + movementY / 2;

                return {
                    x: newPositionX,
                    y: newPositionY,
                };
            });
        },
        bottom: (movementX, movementY) => {
            const cropRatio = ratioButtons.find(([ratioButtonId]) => ratioButtonId === activeButton)[1].ratio;

            setCropSize((previousCropSize) => {
                const newWidth = cropRatio ? ((previousCropSize.height - movementY) * cropRatio.x) / cropRatio.y : previousCropSize.width;
                const newHeight = previousCropSize.height + movementY;

                return {
                    width: newWidth,
                    height: newHeight,
                };
            });

            setCropPosition((previousCropPosition) => {
                const newPositionX = previousCropPosition.x;
                const newPositionY = previousCropPosition.y + movementY / 2;

                return {
                    x: newPositionX,
                    y: newPositionY,
                };
            });
        },
        'bottom-right': (movementX, movementY) => {
            const cropRatio = ratioButtons.find(([ratioButtonId]) => ratioButtonId === activeButton)[1].ratio;

            setCropSize((previousCropSize) => {
                const newWidth = previousCropSize.width + movementX;
                const newHeight = cropRatio
                    ? ((previousCropSize.width - movementX) * cropRatio.y) / cropRatio.x
                    : previousCropSize.height + movementY;

                return {
                    width: newWidth,
                    height: newHeight,
                };
            });

            setCropPosition((previousCropPosition) => {
                const newPositionX = previousCropPosition.x + movementX / 2;
                const newPositionY = cropRatio
                    ? previousCropPosition.y + ((movementX / 2) * cropRatio.y) / cropRatio.x
                    : previousCropPosition.y + movementY / 2;

                return {
                    x: newPositionX,
                    y: newPositionY,
                };
            });
        },
    };
    const setCropSizeFromInputs = (width, height) => {
        const cropRatio = ratioButtons.find(([ratioButtonId]) => ratioButtonId === activeButton)[1].ratio;
        let ratioFactor = cropRatio && width ? cropRatio.y / cropRatio.x : 1;

        ratioFactor = cropRatio && height ? cropRatio.x / cropRatio.y : ratioFactor;

        setCropSize((previousCropSize) => {
            let newWidth = width !== null ? width : previousCropSize.width;
            let newHeight = height !== null ? height : previousCropSize.height;

            if (cropRatio) {
                newWidth = width !== null ? width : height * ratioFactor;
                newHeight = height !== null ? height : width * ratioFactor;
            }

            return {
                width: newWidth,
                height: newHeight,
            };
        });
    };
    const handleKeyDown = (event) => {
        if (ENTER_KEY_CODE === event.keyCode) {
            cropImage();
        }
    };
    const handleClickOutside = (event) => {
        if (event.target.closest('.c-image-editor-workspace__canvas-wrapper') && event.target.nodeName !== 'CANVAS') {
            cropImage();
        }
    };
    const selectRatio = (event) => {
        setActiveButton(event.currentTarget.dataset.id);
        setActiveAction(IDENTIFIER);
    };
    const resetCropSize = () => {
        setCropSize({
            width: canvas ? canvas.current.width : null,
            height: canvas ? canvas.current.height : null,
        });

        setCropPosition({
            x: canvas ? canvas.current.width / 2 : null,
            y: canvas ? canvas.current.height / 2 : null,
        });
    };
    const cancelCrop = () => {
        setActiveButton('');
        setActiveAction('');
    };
    const cropImage = () => {
        const ctx = canvas.current.getContext('2d');
        const image = new Image();

        image.onload = () => {
            const roundedWidth = Math.round(cropSize.width);
            const roundedHeight = Math.round(cropSize.height);
            const widthPositionDiff = roundedWidth / 2;
            const heightPositionDiff = roundedHeight / 2;

            canvas.current.width = roundedWidth;
            canvas.current.height = roundedHeight;

            technicalCanvas.current.width = roundedWidth;
            technicalCanvas.current.height = roundedHeight;

            ctx.clearRect(0, 0, canvas.current.width, canvas.current.height);

            ctx.save();

            ctx.drawImage(
                image,
                cropPosition.x - widthPositionDiff,
                cropPosition.y - heightPositionDiff,
                roundedWidth,
                roundedHeight,
                0,
                0,
                roundedWidth,
                roundedHeight,
            );

            ctx.restore();

            const croppedImage = new Image();

            croppedImage.onload = () => {
                dispatchImageHistoryAction({ type: 'ADD_TO_HISTORY', image: croppedImage, additionalData });

                setActiveButton('');
                setActiveAction('');
            };

            croppedImage.src = canvas.current.toDataURL();
        };

        image.src = canvas.current.toDataURL();
    };
    const clearTechnicalCanvas = () => {
        if (!technicalCanvas) {
            return;
        }

        const ctx = technicalCanvas.current.getContext('2d');

        ctx.clearRect(0, 0, technicalCanvas.current.width, technicalCanvas.current.height);
    };
    const drawCropRectangle = () => {
        const ctx = technicalCanvas.current.getContext('2d');
        const widthPositionDiff = cropSize.width / 2;
        const heightPositionDiff = cropSize.height / 2;
        const drawBorder = () => {
            ctx.strokeStyle = GRID_COLOR;
            ctx.lineWidth = 3;

            ctx.moveTo(cropPosition.x - widthPositionDiff, cropPosition.y - heightPositionDiff);
            ctx.lineTo(cropPosition.x + widthPositionDiff, cropPosition.y - heightPositionDiff);
            ctx.lineTo(cropPosition.x + widthPositionDiff, cropPosition.y + heightPositionDiff);
            ctx.lineTo(cropPosition.x - widthPositionDiff, cropPosition.y + heightPositionDiff);
            ctx.lineTo(cropPosition.x - widthPositionDiff, cropPosition.y - heightPositionDiff);

            ctx.stroke();
        };
        const drawGrid = () => {
            ctx.strokeStyle = GRID_COLOR;
            ctx.lineWidth = 1;

            ctx.moveTo(cropPosition.x - widthPositionDiff, cropPosition.y - heightPositionDiff + cropSize.height / 3);
            ctx.lineTo(cropPosition.x + widthPositionDiff, cropPosition.y - heightPositionDiff + cropSize.height / 3);

            ctx.moveTo(cropPosition.x - widthPositionDiff, cropPosition.y + heightPositionDiff - cropSize.height / 3);
            ctx.lineTo(cropPosition.x + widthPositionDiff, cropPosition.y + heightPositionDiff - cropSize.height / 3);

            ctx.moveTo(cropPosition.x - widthPositionDiff + cropSize.width / 3, cropPosition.y - heightPositionDiff);
            ctx.lineTo(cropPosition.x - widthPositionDiff + cropSize.width / 3, cropPosition.y + heightPositionDiff);

            ctx.moveTo(cropPosition.x + widthPositionDiff - cropSize.width / 3, cropPosition.y - heightPositionDiff);
            ctx.lineTo(cropPosition.x + widthPositionDiff - cropSize.width / 3, cropPosition.y + heightPositionDiff);

            ctx.stroke();
        };
        const drawResizeHandlers = () => {
            resizeHandlers = [
                {
                    x: cropPosition.x - widthPositionDiff - resizeHandlerWidth / 2,
                    y: cropPosition.y - heightPositionDiff - resizeHandlerWidth / 2,
                    cursor: 'nwse-resize',
                    id: 'top-left',
                },
                {
                    x: cropPosition.x - resizeHandlerWidth / 2,
                    y: cropPosition.y - heightPositionDiff - resizeHandlerWidth / 2,
                    cursor: 'ns-resize',
                    id: 'top',
                },
                {
                    x: cropPosition.x + widthPositionDiff - resizeHandlerWidth / 2,
                    y: cropPosition.y - heightPositionDiff - resizeHandlerWidth / 2,
                    cursor: 'nesw-resize',
                    id: 'top-right',
                },
                {
                    x: cropPosition.x - widthPositionDiff - resizeHandlerWidth / 2,
                    y: cropPosition.y - resizeHandlerWidth / 2,
                    cursor: 'ew-resize',
                    id: 'left',
                },
                {
                    x: cropPosition.x + widthPositionDiff - resizeHandlerWidth / 2,
                    y: cropPosition.y - resizeHandlerWidth / 2,
                    cursor: 'ew-resize',
                    id: 'right',
                },
                {
                    x: cropPosition.x - widthPositionDiff - resizeHandlerWidth / 2,
                    y: cropPosition.y + heightPositionDiff - resizeHandlerWidth / 2,
                    cursor: 'nesw-resize',
                    id: 'bottom-left',
                },
                {
                    x: cropPosition.x - resizeHandlerWidth / 2,
                    y: cropPosition.y + heightPositionDiff - resizeHandlerWidth / 2,
                    cursor: 'ns-resize',
                    id: 'bottom',
                },
                {
                    x: cropPosition.x + widthPositionDiff - resizeHandlerWidth / 2,
                    y: cropPosition.y + heightPositionDiff - resizeHandlerWidth / 2,
                    cursor: 'nwse-resize',
                    id: 'bottom-right',
                },
            ];

            ctx.fillStyle = GRID_COLOR;

            resizeHandlers.forEach((resizeHandler) => {
                ctx.fillRect(resizeHandler.x, resizeHandler.y, resizeHandlerWidth, resizeHandlerWidth);
            });
        };
        const drawBackdrop = () => {
            ctx.fillStyle = '#ffffff80';

            ctx.fillRect(0, 0, technicalCanvas.current.width, cropPosition.y - heightPositionDiff);
            ctx.fillRect(0, cropPosition.y - heightPositionDiff, cropPosition.x - widthPositionDiff, cropSize.height);
            ctx.fillRect(
                cropPosition.x + widthPositionDiff,
                cropPosition.y - heightPositionDiff,
                technicalCanvas.current.width,
                cropSize.height,
            );
            ctx.fillRect(0, cropPosition.y + heightPositionDiff, technicalCanvas.current.width, technicalCanvas.current.height);
        };
        const drawActionsBtns = () => {
            const topRightResizeHandler = resizeHandlers.find((resizeHandler) => resizeHandler.id === 'top-right');
            const drawRoundedRect = (x, y, fillStyle, strokeStyle) => {
                ctx.fillStyle = fillStyle;

                if (strokeStyle) {
                    ctx.strokeStyle = strokeStyle;
                }

                ctx.beginPath();

                ctx.moveTo(x + btnRadius, y);
                ctx.lineTo(x + btnWidth - btnRadius, y);
                ctx.quadraticCurveTo(x + btnWidth, y, x + btnWidth, y + btnRadius);
                ctx.lineTo(x + btnWidth, y + btnWidth - btnRadius);
                ctx.quadraticCurveTo(x + btnWidth, y + btnWidth, x + btnWidth - btnRadius, y + btnWidth);
                ctx.lineTo(x + btnRadius, y + btnWidth);
                ctx.quadraticCurveTo(x, y + btnWidth, x, y + btnWidth - btnRadius);
                ctx.lineTo(x, y + btnRadius);
                ctx.quadraticCurveTo(x, y, x + btnRadius, y);

                ctx.closePath();

                ctx.fill();

                if (strokeStyle) {
                    ctx.lineWidth = 2;
                    ctx.stroke();
                }
            };
            const drawAcceptBtn = () => {
                const acceptBtnX = topRightResizeHandler.x - 90;
                const acceptBtnY = topRightResizeHandler.y >= 40 ? topRightResizeHandler.y - 40 : topRightResizeHandler.y + 20;

                drawRoundedRect(acceptBtnX, acceptBtnY, POSITIVE_COLOR);

                ctx.strokeStyle = '#ffffff';
                ctx.lineWidth = 2;

                ctx.beginPath();

                ctx.moveTo(acceptBtnX + 10, acceptBtnY + 17);
                ctx.lineTo(acceptBtnX + 17, acceptBtnY + 27);
                ctx.lineTo(acceptBtnX + 30, acceptBtnY + 10);

                ctx.stroke();

                acceptBtnPosition.x = acceptBtnX;
                acceptBtnPosition.y = acceptBtnY;
            };
            const drawCancelBtn = () => {
                const cancelBtnX = topRightResizeHandler.x - 40;
                const cancelBtnY = topRightResizeHandler.y >= 40 ? topRightResizeHandler.y - 40 : topRightResizeHandler.y + 20;

                drawRoundedRect(cancelBtnX, cancelBtnY, '#ffffff', NEGATIVE_COLOR);

                ctx.strokeStyle = NEGATIVE_COLOR;
                ctx.lineWidth = 2;

                ctx.beginPath();

                ctx.moveTo(cancelBtnX + 10, cancelBtnY + 10);
                ctx.lineTo(cancelBtnX + 30, cancelBtnY + 30);
                ctx.moveTo(cancelBtnX + 10, cancelBtnY + 30);
                ctx.lineTo(cancelBtnX + 30, cancelBtnY + 10);

                ctx.stroke();

                cancelBtnPosition.x = cancelBtnX;
                cancelBtnPosition.y = cancelBtnY;
            };

            drawAcceptBtn();
            drawCancelBtn();
        };

        clearTechnicalCanvas();

        ctx.save();

        ctx.beginPath();

        drawBackdrop();
        drawBorder();
        drawGrid();
        drawResizeHandlers();
        drawActionsBtns();

        ctx.restore();
    };
    const isOnGrid = (offsetX, offsetY) => {
        const diffX = offsetX - (cropPosition.x - cropSize.width / 2);
        const diffY = offsetY - (cropPosition.y - cropSize.height / 2);

        return diffX >= 0 && diffX <= cropSize.width && diffY >= 0 && diffY <= cropSize.height;
    };
    const isOnAcceptBtn = (offsetX, offsetY) => {
        const diffX = offsetX - acceptBtnPosition.x;
        const diffY = offsetY - acceptBtnPosition.y;

        return diffX >= 0 && diffX <= btnWidth && diffY >= 0 && diffY <= btnWidth;
    };
    const isOnCancelBtn = (offsetX, offsetY) => {
        const diffX = offsetX - cancelBtnPosition.x;
        const diffY = offsetY - cancelBtnPosition.y;

        return diffX >= 0 && diffX <= btnWidth && diffY >= 0 && diffY <= btnWidth;
    };
    const findResizeHandlerUnderMouse = (offsetX, offsetY) => {
        return resizeHandlers.find((resizeHandler) => {
            const diffX = offsetX - resizeHandler.x;
            const diffY = offsetY - resizeHandler.y;

            return diffX >= 0 && diffX <= resizeHandlerWidth && diffY >= 0 && diffY <= resizeHandlerWidth;
        });
    };
    const handleMouseMove = ({ offsetX, offsetY }) => {
        const elementUnderCursor = findResizeHandlerUnderMouse(offsetX, offsetY);

        if (elementUnderCursor) {
            technicalCanvas.current.style.cursor = elementUnderCursor.cursor;
        } else if (isOnAcceptBtn(offsetX, offsetY) || isOnCancelBtn(offsetX, offsetY)) {
            technicalCanvas.current.style.cursor = 'pointer';
        } else if (isOnGrid(offsetX, offsetY)) {
            technicalCanvas.current.style.cursor = 'move';
        } else {
            technicalCanvas.current.style.cursor = 'default';
        }
    };
    const handleMouseDown = ({ offsetX, offsetY }) => {
        const elementUnderCursor = findResizeHandlerUnderMouse(offsetX, offsetY);
        const resizeCropHandler = resizeCrop.bind(null, elementUnderCursor);
        const acceptBtnClicked = isOnAcceptBtn(offsetX, offsetY);
        const cancelBtnClicked = isOnCancelBtn(offsetX, offsetY);

        if (acceptBtnClicked) {
            cropImage();
        } else if (cancelBtnClicked) {
            cancelCrop();
        } else if (elementUnderCursor) {
            technicalCanvas.current.addEventListener('mousemove', resizeCropHandler, false);
            technicalCanvas.current.addEventListener(
                'mouseup',
                () => {
                    technicalCanvas.current.removeEventListener('mousemove', resizeCropHandler, false);
                },
                false,
            );
        } else if (isOnGrid(offsetX, offsetY)) {
            technicalCanvas.current.addEventListener('mousemove', moveCrop, false);
            technicalCanvas.current.addEventListener(
                'mouseup',
                () => {
                    technicalCanvas.current.removeEventListener('mousemove', moveCrop, false);
                },
                false,
            );
        }
    };
    const resizeCrop = (elementUnderCursor, event) => {
        const { zoom } = technicalCanvas.current.dataset;
        const movementX = event.movementX / zoom;
        const movementY = event.movementY / zoom;

        if (elementUnderCursor && resizeMethods[elementUnderCursor.id]) {
            resizeMethods[elementUnderCursor.id](movementX, movementY);
        }
    };
    const moveCrop = (event) => {
        const { zoom } = technicalCanvas.current.dataset;
        const movementX = event.movementX / zoom;
        const movementY = event.movementY / zoom;

        setCropPosition((previousCropPosition) => {
            const newPositionX =
                previousCropPosition.x + movementX + cropSize.width / 2 > technicalCanvas.current.width ||
                previousCropPosition.x + movementX - cropSize.width / 2 < 0
                    ? previousCropPosition.x
                    : previousCropPosition.x + movementX;
            const newPositionY =
                previousCropPosition.y + movementY + cropSize.height / 2 > technicalCanvas.current.height ||
                previousCropPosition.y + movementY - cropSize.height / 2 < 0
                    ? previousCropPosition.y
                    : previousCropPosition.y + movementY;

            return { x: newPositionX, y: newPositionY };
        });
    };
    const renderAlert = () => {
        if (activeButton === '' || !additionalData.focalPointX || !additionalData.focalPointX) {
            return null;
        }

        return (
            <div className="ibexa-alert ibexa-alert--info">
                <div className="ibexa-alert__icon-wrapper">
                    <Icon name="system-information" extraClasses="ibexa-icon--small" />
                </div>
                <div className="ibexa-alert__message-container">
                    {Translator.trans(
                        /*@Desc("Adjust the focal point, crop might change its parameters")*/ 'image_editor.actions.crop.warning',
                        {},
                        'ibexa_image_editor',
                    )}
                </div>
            </div>
        );
    };

    useEffect(() => {
        if (activeAction === IDENTIFIER) {
            if (technicalCanvas) {
                technicalCanvas.current.addEventListener('mousemove', handleMouseMove, false);
                technicalCanvas.current.addEventListener('mousedown', handleMouseDown, false);
            }

            window.document.addEventListener('keydown', handleKeyDown, false);
            window.document.addEventListener('click', handleClickOutside, false);
        }

        return () => {
            if (technicalCanvas && technicalCanvas.current) {
                technicalCanvas.current.removeEventListener('mousemove', handleMouseMove, false);
                technicalCanvas.current.removeEventListener('mousedown', handleMouseDown, false);
            }

            window.document.removeEventListener('keydown', handleKeyDown, false);
            window.document.removeEventListener('click', handleClickOutside, false);
        };
    });

    useEffect(() => {
        resetCropSize();
    }, [canvas]);

    useEffect(() => {
        if (!activeButton || activeButton === 'custom') {
            resetCropSize();

            return;
        }

        const cropRatio = ratioButtons.find(([ratioButtonId]) => ratioButtonId === activeButton)[1].ratio;

        if (!cropRatio) {
            return;
        }

        const isCropHorizontal =
            canvas.current.width >= canvas.current.height && (canvas.current.height * cropRatio.x) / cropRatio.y <= canvas.current.width;
        const cropWidth = isCropHorizontal ? (canvas.current.height * cropRatio.x) / cropRatio.y : canvas.current.width;
        const cropHeight = isCropHorizontal ? canvas.current.height : (canvas.current.width * cropRatio.y) / cropRatio.x;

        setCropSize({
            width: cropWidth,
            height: cropHeight,
        });

        setCropPosition({
            x: canvas ? canvas.current.width / 2 : null,
            y: canvas ? canvas.current.height / 2 : null,
        });
    }, [activeButton]);

    useEffect(() => {
        if (!technicalCanvas || activeAction !== IDENTIFIER) {
            return;
        }

        drawCropRectangle();
    }, [activeButton, cropSize, cropPosition]);

    useEffect(() => {
        if (activeAction !== IDENTIFIER) {
            setActiveButton('');
        }
    }, [activeAction]);

    return (
        <div className="c-image-editor-crop">
            {renderAlert()}
            <div className="c-image-editor-crop__ratio">
                <span className="c-image-editor-crop__ratio-label">
                    {Translator.trans(/*@Desc("Ratio")*/ 'image_editor.actions.crop.ratio', {}, 'ibexa_image_editor')}
                </span>
                {ratioButtons.map(([ratioButtonId, ratioButton]) => {
                    const className = createCssClassNames({
                        btn: true,
                        'ibexa-btn': true,
                        'ibexa-btn--tertiary': true,
                        'ibexa-btn--small': true,
                        'c-image-editor-crop__btn-ratio': true,
                        'c-image-editor-crop__btn-ratio--active': ratioButtonId === activeButton,
                    });

                    return (
                        <button key={ratioButtonId} data-id={ratioButtonId} type="button" onClick={selectRatio} className={className}>
                            {ratioButton.label}
                        </button>
                    );
                })}
            </div>
            <div className="c-image-editor-crop__size">
                <span className="c-image-editor-crop__size-label">
                    {Translator.trans(/*@Desc("Crop size")*/ 'image_editor.actions.crop.crop_size', {}, 'ibexa_image_editor')}
                </span>
                <div className="c-image-editor-crop__input-wrapper">
                    <input
                        type="number"
                        min="1"
                        max={canvas ? canvas.current.width : null}
                        value={Math.round(cropSize.width)}
                        readOnly={activeAction !== IDENTIFIER}
                        onChange={(event) => {
                            const value =
                                event.currentTarget.value === '' || event.currentTarget.value === '0' ? 1 : event.currentTarget.value;

                            setCropSizeFromInputs(Math.min(parseInt(value, 10), parseInt(event.currentTarget.max, 10)), null);
                        }}
                        className="form-control c-image-editor-crop__input c-image-editor-crop__input--width"
                    />
                    <div className="c-image-editor-crop__input-label">
                        {Translator.trans(/*@Desc("W")*/ 'image_editor.actions.crop.abbreviation.width', {}, 'ibexa_image_editor')}
                    </div>
                </div>
                <div className="c-image-editor-crop__icon-wrapper">
                    <Icon name={lockIcon} extraClasses="ibexa-icon--small" />
                </div>
                <div className="c-image-editor-crop__input-wrapper">
                    <input
                        type="number"
                        min="1"
                        max={canvas ? canvas.current.height : null}
                        value={Math.round(cropSize.height)}
                        readOnly={activeAction !== IDENTIFIER}
                        onChange={(event) => {
                            const value =
                                event.currentTarget.value === '' || event.currentTarget.value === '0' ? 1 : event.currentTarget.value;

                            setCropSizeFromInputs(null, Math.min(parseInt(value, 10), parseInt(event.currentTarget.max, 10)));
                        }}
                        className="form-control c-image-editor-crop__input c-image-editor-crop__input--height"
                    />
                    <div className="c-image-editor-crop__input-label">
                        {Translator.trans(/*@Desc("H")*/ 'image_editor.actions.crop.abbreviation.height', {}, 'ibexa_image_editor')}
                    </div>
                </div>
            </div>
        </div>
    );
};

Crop.propTypes = {
    config: PropTypes.object.isRequired,
};

Crop.defaultProps = {};

export default Crop;

window.ibexa.addConfig(
    'imageEditor.actions.crop',
    {
        label: Translator.trans(/*@Desc("Crop")*/ 'image_editor.actions.crop.label', {}, 'ibexa_image_editor'),
        component: Crop,
        icon: window.ibexa.helpers.icon.getIconPath('crop'),
        identifier: IDENTIFIER,
    },
    true,
);
