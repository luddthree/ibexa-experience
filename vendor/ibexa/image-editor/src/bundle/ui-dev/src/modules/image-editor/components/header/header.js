import React, { useCallback, useContext, useEffect, useRef, useState } from 'react';
import PropTypes from 'prop-types';

import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

import { CancelContext, ConfirmContext } from '../../image.editor.modules';
import { createPortal } from 'react-dom';

const { Translator, Popper } = window;

const Header = ({ imageName, saveAsNewPossible }) => {
    const onCancel = useContext(CancelContext);
    const onConfirm = useContext(ConfirmContext);
    const splitBtnRef = useRef(null);
    const popupRef = useRef(null);
    const togglerBtnRef = useRef(null);
    const [isSavePopupOpened, setIsSavePopupOpened] = useState(false);
    const saveLabel = Translator.trans(/*@Desc("Save")*/ 'image_editor.save', {}, 'ibexa_image_editor');
    const saveAsLabel = Translator.trans(/*@Desc("Save as...")*/ 'image_editor.save_as', {}, 'ibexa_image_editor');
    const saveAndCloseLabel = Translator.trans(/*@Desc("Save and close")*/ 'image_editor.save_and_close', {}, 'ibexa_image_editor');
    const toggleBtnClassName = createCssClassNames({
        'btn ibexa-btn ibexa-btn--no-text ibexa-btn--primary': true,
        'ibexa-split-btn__toggle-btn': true,
        'ibexa-split-btn__toggle-btn--subitems-opened': isSavePopupOpened,
    });
    const popupClassName = createCssClassNames({
        'c-image-editor-header__save-menu-popup': true,
        'ibexa-popup-menu': true,
        'ibexa-popup-menu--hidden': !isSavePopupOpened,
    });
    const closePopupOnClickOutside = useCallback(
        (event) => {
            if (popupRef.current.contains(event.target) || togglerBtnRef.current.contains(event.target)) {
                return;
            }

            setIsSavePopupOpened(false);
        },
        [popupRef, setIsSavePopupOpened],
    );

    useEffect(() => {
        if (!isSavePopupOpened) {
            return;
        }

        window.document.addEventListener('click', closePopupOnClickOutside, false);

        return () => {
            window.document.removeEventListener('click', closePopupOnClickOutside, false);
        };
    }, [isSavePopupOpened, closePopupOnClickOutside]);

    useEffect(() => {
        if (!popupRef.current || !splitBtnRef.current) {
            return;
        }

        const popperInstance = Popper.createPopper(splitBtnRef.current, popupRef.current, {
            placement: 'bottom-start',
            modifiers: [
                {
                    name: 'flip',
                    enabled: true,
                    options: {
                        fallbackPlacements: ['bottom-end', 'top-end', 'top-start'],
                    },
                },
                {
                    name: 'offset',
                    options: {
                        offset: [0, 2],
                    },
                },
            ],
        });

        return () => {
            popperInstance.destroy();
        };
    }, [popupRef.current, splitBtnRef.current]);

    return (
        <div className="c-image-editor-header">
            <div className="c-image-editor-header__title-wrapper">
                <div className="c-image-editor-header__title">
                    {Translator.trans(/*@Desc("Image Editor")*/ 'ibexa_image_editor', {}, 'ibexa_image_editor')}
                </div>
                <div className="c-image-editor-header__subtitle">{imageName}</div>
            </div>
            <div className="c-image-editor-header__actions-wrapper">
                {saveAsNewPossible ? (
                    <button
                        className="btn ibexa-btn ibexa-btn--primary"
                        type="button"
                        onClick={() => onConfirm({ closeEditorAfterSave: false })}
                    >
                        {saveAsLabel}
                    </button>
                ) : (
                    <div ref={splitBtnRef} className="ibexa-split-btn ibexa-split-btn--primary">
                        <button
                            className="btn ibexa-btn ibexa-split-btn__main-btn ibexa-btn--primary"
                            type="button"
                            onClick={() => onConfirm({ closeEditorAfterSave: true })}
                        >
                            {saveAndCloseLabel}
                        </button>
                        <div className="ibexa-split-btn__split" />
                        <button
                            ref={togglerBtnRef}
                            className={toggleBtnClassName}
                            type="button"
                            onClick={() => setIsSavePopupOpened((isOpened) => !isOpened)}
                        >
                            <Icon name="caret-down" extraClasses="ibexa-icon--small ibexa-split-btn__toggler-icon" />
                        </button>
                        {createPortal(
                            <div ref={popupRef} className={popupClassName}>
                                <div key="default" className="ibexa-popup-menu__group">
                                    <div className="ibexa-popup-menu__item">
                                        <button
                                            className="ibexa-popup-menu__item-content"
                                            type="button"
                                            onClick={() => {
                                                setIsSavePopupOpened(false);
                                                onConfirm({ closeEditorAfterSave: false });
                                            }}
                                        >
                                            <span className="ibexa-btn__label">{saveLabel}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>,
                            document.body,
                        )}
                    </div>
                )}
                <button type="button" className="btn ibexa-btn ibexa-btn--secondary-light" onClick={() => onCancel()}>
                    <span className="ibexa-btn__label">
                        {Translator.trans(/*@Desc("Discard")*/ 'image_editor.back', {}, 'ibexa_image_editor')}
                    </span>
                </button>
            </div>
        </div>
    );
};

Header.propTypes = {
    imageName: PropTypes.string.isRequired,
    saveAsNewPossible: PropTypes.bool,
};

Header.defaultProps = {
    saveAsNewPossible: false,
};

export default Header;
