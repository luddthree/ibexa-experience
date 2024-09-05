import React, { useContext } from 'react';

import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';

import { CanvasContext, ImageHistoryContext, AdditionalDataContext, ActiveActionContext } from '../../../image.editor.modules';

const { Translator, ibexa } = window;

const IDENTIFIER = 'flip';

const Flip = () => {
    const [canvas] = useContext(CanvasContext);
    const [, dispatchImageHistoryAction] = useContext(ImageHistoryContext);
    const [additionalData] = useContext(AdditionalDataContext);
    const [, setActiveAction] = useContext(ActiveActionContext);
    const flip = (scaleX, scaleY, posX, posY) => {
        const ctx = canvas.current.getContext('2d');
        const image = new Image();

        image.onload = () => {
            const { width } = image;
            const { height } = image;

            ctx.clearRect(0, 0, canvas.current.width, canvas.current.height);

            ctx.save();
            ctx.scale(scaleX, scaleY);
            ctx.drawImage(image, width * posX, height * posY, width, height);
            ctx.restore();

            const flippedImage = new Image();

            flippedImage.onload = () => {
                dispatchImageHistoryAction({ type: 'ADD_TO_HISTORY', image: flippedImage, additionalData });
            };

            flippedImage.src = canvas.current.toDataURL();
        };

        image.src = canvas.current.toDataURL();
    };
    const flipHorizontal = () => {
        flip(-1, 1, -1, 0);

        setActiveAction(IDENTIFIER);
    };
    const flipVertical = () => {
        flip(1, -1, 0, -1);

        setActiveAction(IDENTIFIER);
    };

    return (
        <div className="c-image-editor-flip">
            <button type="button" onClick={flipHorizontal} className="btn ibexa-btn ibexa-btn--tertiary ibexa-btn--small">
                <Icon name="flip-horizontal" extraClasses="ibexa-icon--small" />
                {Translator.trans(/*@Desc("Horizontal")*/ 'image_editor.actions.flip.horizontal', {}, 'ibexa_image_editor')}
            </button>
            <button type="button" onClick={flipVertical} className="btn ibexa-btn ibexa-btn--tertiary ibexa-btn--small">
                <Icon name="flip-vertical" extraClasses="ibexa-icon--small" />
                {Translator.trans(/*@Desc("Vertical")*/ 'image_editor.actions.flip.vertical', {}, 'ibexa_image_editor')}
            </button>
        </div>
    );
};

Flip.propTypes = {};

Flip.defaultProps = {};

export default Flip;

ibexa.addConfig(
    'imageEditor.actions.flip',
    {
        label: Translator.trans(/*@Desc("Flip")*/ 'image_editor.actions.flip.label', {}, 'ibexa_image_editor'),
        component: Flip,
        icon: ibexa.helpers.icon.getIconPath('flip'),
        identifier: IDENTIFIER,
    },
    true,
);
