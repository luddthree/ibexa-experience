import React, { useRef, useContext, useEffect, useState } from 'react';
import PropTypes from 'prop-types';

import ActionItem from '../../components/action-list-item/action.list.item';
import ModalDelete from '../../components/modal-delete/modal.delete';
import { ExpandContext } from '../../components/local-storage-expand-connector/local.storage.expand.connector';
import { SelectedContext } from '../../components/selected-provider/selected.provider';
import { ContextualMenuContext } from '../../components/contextual-menu/contextual.menu';
import { BuildItemContext, CallbackContext } from '../../tree.builder.module';
import { STORED_ITEMS_CLEAR, STORED_ITEMS_REMOVE } from '../../hooks/useStoredItemsReducer';
import { getAllChildren } from '../../helpers/tree';
import { isItemEmpty } from '../../helpers/item';

const ACTION_TIMEOUT = 250;
const { Translator } = window;

const Delete = ({
    item,
    label,
    useIconAsLabel,
    modalConfirmationBody,
    modalSize,
    isLoading,
    checkIsDisabled,
    isModalDisabled,
    fetchedData,
    renderModal,
}) => {
    const actionTimeout = useRef();
    const [showModal, setShowModal] = useState(false);
    const buildItem = useContext(BuildItemContext);
    const { dispatchExpandedData } = useContext(ExpandContext);
    const { setIsClosable, setIsExpanded } = useContext(ContextualMenuContext);
    const { selectedData: contextSelectedData, dispatchSelectedData } = useContext(SelectedContext);
    const { callbackDeleteElements } = useContext(CallbackContext);
    const itemLabel =
        label ||
        Translator.trans(
            /*@Desc("Delete")*/
            'actions.delete',
            {},
            'ibexa_tree_builder_ui',
        );
    const selectedData = isItemEmpty(item) ? contextSelectedData : [item];
    const isDisabled = isLoading || selectedData.length === 0 || checkIsDisabled(selectedData, fetchedData);
    const hasSubitems = ({ subitems }) => !!subitems && subitems.length;
    const showDeleteModal = () => {
        setShowModal(true);
    };
    const deleteNodes = () => {
        clearTimeout(actionTimeout.current);

        actionTimeout.current = setTimeout(() => {
            setShowModal(false);
            setIsExpanded(false);

            callbackDeleteElements({ selectedData }).then(() => {
                const itemsToRemoveFromStorage = selectedData.reduce((storedData, { internalItem: data }) => {
                    const childrenItems = getAllChildren({ data, buildItem, condition: hasSubitems });

                    return [...storedData, ...childrenItems];
                }, []);

                dispatchExpandedData({ items: itemsToRemoveFromStorage, type: STORED_ITEMS_REMOVE });
                dispatchSelectedData({ type: STORED_ITEMS_CLEAR });
            });
        }, ACTION_TIMEOUT);
    };
    const closeModal = () => {
        setShowModal(false);
    };
    const renderModalWrapper = () => {
        if (isModalDisabled) {
            return;
        }

        if (renderModal) {
            return renderModal({ visible: showModal, onConfirm: deleteNodes, onClose: closeModal, size: modalSize });
        }

        return (
            <ModalDelete
                visible={showModal}
                onConfirm={deleteNodes}
                confirmationBody={modalConfirmationBody}
                onClose={closeModal}
                size={modalSize}
            />
        );
    };

    useEffect(() => {
        setIsClosable(!showModal);
    }, [showModal]);

    return (
        <>
            <ActionItem
                label={itemLabel}
                labelIcon="trash"
                useIconAsLabel={useIconAsLabel}
                onClick={isModalDisabled ? deleteNodes : showDeleteModal}
                isLoading={isLoading}
                isDisabled={isDisabled}
                isCustomClose={true}
            />
            {renderModalWrapper()}
        </>
    );
};

Delete.propTypes = {
    item: PropTypes.object,
    label: PropTypes.node,
    useIconAsLabel: PropTypes.bool,
    isLoading: PropTypes.bool,
    checkIsDisabled: PropTypes.func,
    fetchedData: PropTypes.any,
    isModalDisabled: PropTypes.bool,
    modalConfirmationBody: PropTypes.node,
    modalSize: PropTypes.string,
    renderModal: PropTypes.func,
};

Delete.defaultProps = {
    item: {},
    label: null,
    useIconAsLabel: false,
    isLoading: false,
    checkIsDisabled: () => false,
    fetchedData: null,
    isModalDisabled: false,
    modalConfirmationBody: null,
    modalSize: 'large',
    renderModal: null,
};

export default Delete;
