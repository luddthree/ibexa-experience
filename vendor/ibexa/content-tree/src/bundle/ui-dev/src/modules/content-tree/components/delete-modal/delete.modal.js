import React, { useState } from 'react';
import PropTypes from 'prop-types';

import { getAdminUiConfig } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';
import TreeBuilderModalDelete from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/components/modal-delete/modal.delete';
import DeleteBodyModal from '../delete-modal-body/delete.modal.body';

const DeleteModal = ({ confirmBtnLabel, item, ...props }) => {
    const { onConfirm } = props;
    const adminUiConfig = getAdminUiConfig();
    const { reverseRelationsCount, totalSubitemsCount, name } = item.internalItem;
    const isDeletingUser = adminUiConfig.userContentTypes.includes(item.internalItem.contentTypeIdentifier);
    const [confirmationChecked, setConfirmationChecked] = useState(reverseRelationsCount === 0 && totalSubitemsCount === 0);
    const renderModalBody = () => {
        return (
            <DeleteBodyModal
                confirmationChecked={confirmationChecked}
                setConfirmationChecked={setConfirmationChecked}
                reverseRelationsCount={reverseRelationsCount}
                totalSubitemsCount={totalSubitemsCount}
                isDeletingUser={isDeletingUser}
                name={name}
            />
        );
    };
    const confirmBtnAttrs = {
        label: confirmBtnLabel,
        onClick: onConfirm,
        className: 'ibexa-btn--primary ibexa-btn--trigger',
        disabled: !confirmationChecked,
    };

    return <TreeBuilderModalDelete confirmationBody={renderModalBody()} confirmBtnAttrs={confirmBtnAttrs} {...props} />;
};

DeleteModal.propTypes = {
    confirmBtnLabel: PropTypes.string.isRequired,
    item: PropTypes.object.isRequired,
    onConfirm: PropTypes.func.isRequired,
};

export default DeleteModal;
