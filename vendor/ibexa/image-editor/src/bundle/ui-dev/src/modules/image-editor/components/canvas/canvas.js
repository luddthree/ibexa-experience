import React, { useEffect, useContext, useRef } from 'react';
import PropTypes from 'prop-types';

import { CanvasContext, ImageHistoryContext, TechnicalCanvasContext, AdditionalDataContext } from '../../image.editor.modules';

const CANVAS_PADDING_X = 460;
const CANVAS_PADDING_Y = 200;

const Canvas = (props) => {
    const refCanvas = useRef();
    const refTechnicalCanvas = useRef();
    const refContainer = useRef();
    const [, setCanvas] = useContext(CanvasContext);
    const [, setTechnicalCanvas] = useContext(TechnicalCanvasContext);
    const [, dispatchImageHistoryAction] = useContext(ImageHistoryContext);
    const [additionalData] = useContext(AdditionalDataContext);
    const canvasMaxWidth = window.innerWidth - CANVAS_PADDING_X;
    const canvasMaxHeight = window.innerHeight - CANVAS_PADDING_Y;
    const canvasWidth = refCanvas.current ? refCanvas.current.width : 0;
    const canvasHeight = refCanvas.current ? refCanvas.current.height : 0;
    let zoom = canvasWidth > canvasMaxWidth ? canvasMaxWidth / canvasWidth : 1;

    if (zoom * canvasHeight > canvasMaxHeight) {
        zoom = canvasMaxHeight / canvasHeight;
    }

    const canvasStyle = {
        transform: `scale(${zoom})`,
    };
    const canvasContainerStyle = {
        height: `${canvasMaxHeight}px`,
        width: `${canvasMaxWidth - 60}px`,
    };

    useEffect(() => {
        const ctx = refCanvas.current.getContext('2d');
        const image = new Image();

        image.onload = () => {
            if (ctx) {
                refCanvas.current.width = image.width;
                refCanvas.current.height = image.height;

                refTechnicalCanvas.current.width = image.width;
                refTechnicalCanvas.current.height = image.height;

                ctx.drawImage(image, 0, 0, image.width, image.height);

                setCanvas(refCanvas);
                setTechnicalCanvas(refTechnicalCanvas);

                dispatchImageHistoryAction({ type: 'ADD_TO_HISTORY', image, additionalData: { ...additionalData } });
            }
        };

        image.src = props.imageUrl;
    }, []);

    return (
        <div className="c-image-editor-canvas" style={canvasContainerStyle} ref={refContainer}>
            <canvas ref={refCanvas} style={canvasStyle} data-zoom={zoom} />
            <canvas ref={refTechnicalCanvas} style={canvasStyle} className="c-image-editor-canvas__technical" data-zoom={zoom} />
        </div>
    );
};

Canvas.propTypes = {
    imageUrl: PropTypes.string.isRequired,
};

Canvas.defaultProps = {};

export default Canvas;
