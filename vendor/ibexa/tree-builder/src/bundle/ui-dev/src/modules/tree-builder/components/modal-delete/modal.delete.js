import React, { useEffect, useMemo, useRef } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';

import { getTranslator, getRootDOMElement } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';
import Popup from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/popup/popup.component';

const ModalDelete = ({
    visible,
    confirmationTitle,
    confirmationBody,
    onConfirm,
    onClose,
    confirmBtnLabel,
    closeBtnLabel,
    size,
    confirmBtnAttrs,
}) => {
    const rootDOMElement = getRootDOMElement();
    const Translator = getTranslator();
    const modalContainer = useRef();
    const actionBtnsConfig = useMemo(() => {
        const defaultConfirmBtnAttrs = {
            label: confirmBtnLabel ?? Translator.trans(/*@Desc("Confirm")*/ 'modal.delete.confirm', {}, 'ibexa_tree_builder_ui'),
            onClick: onConfirm,
            className: 'ibexa-btn--primary ibexa-btn--trigger',
        };
        const closeBtnAttrs = {
            label: closeBtnLabel ?? Translator.trans(/*@Desc("Cancel")*/ 'modal.delete.cancel', {}, 'ibexa_tree_builder_ui'),
            'data-bs-dismiss': 'modal',
            className: 'ibexa-btn--secondary',
        };

        return [confirmBtnAttrs ?? defaultConfirmBtnAttrs, closeBtnAttrs];
    }, [onConfirm, onClose, confirmBtnAttrs]);

    useEffect(() => {
        if (!modalContainer.current) {
            modalContainer.current = document.createElement('div');

            modalContainer.current.classList.add('c-tb-modal-delete-container');
            rootDOMElement.appendChild(modalContainer.current);
        }

        return () => {
            if (modalContainer.current) {
                modalContainer.current.remove();
            }
        };
    }, []);

    if (!modalContainer.current) {
        return null;
    }

    return ReactDOM.createPortal(
        <Popup
            onClose={onClose}
            isVisible={visible}
            size={size}
            actionBtnsConfig={actionBtnsConfig}
            noHeader={!confirmationTitle}
            title={confirmationTitle}
        >
            <div>{confirmationBody}</div>
        </Popup>,
        modalContainer.current,
    );
};

ModalDelete.propTypes = {
    confirmationTitle: PropTypes.string,
    confirmationBody: PropTypes.node.isRequired,
    onClose: PropTypes.func.isRequired,
    onConfirm: PropTypes.func.isRequired,
    confirmBtnLabel: PropTypes.string,
    closeBtnLabel: PropTypes.string,
    visible: PropTypes.bool,
    confirmBtnAttrs: PropTypes.object,
    size: PropTypes.string,
};

ModalDelete.defaultProps = {
    visible: false,
    confirmBtnAttrs: null,
    size: 'medium',
    confirmationTitle: null,
    confirmBtnLabel: null,
    closeBtnLabel: null,
};

export default ModalDelete;
