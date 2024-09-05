import React, { useContext } from 'react';
import PropTypes from 'prop-types';

import Canvas from '../canvas/canvas';

import { CanvasContext } from '../../image.editor.modules';

const { Translator } = window;

const Workspace = (props) => {
    const [canvas] = useContext(CanvasContext);

    return (
        <div className="c-image-editor-workspace">
            <div className="c-image-editor-workspace__canvas-wrapper">
                <Canvas imageUrl={props.imageUrl} />
            </div>
            <div className="c-image-editor-workspace__image-info">
                {Translator.trans(/*@Desc("Dimension")*/ 'image_editor.dimension', {}, 'ibexa_image_editor')} (px):{' '}
                {canvas ? canvas.current.width : ''} x {canvas ? canvas.current.height : ''}
            </div>
        </div>
    );
};

Workspace.propTypes = {
    imageUrl: PropTypes.string.isRequired,
};

Workspace.defaultProps = {};

export default Workspace;
