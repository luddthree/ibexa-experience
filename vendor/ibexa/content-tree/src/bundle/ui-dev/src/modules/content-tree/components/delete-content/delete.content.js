import React from 'react';
import PropTypes from 'prop-types';

import {
    getTranslator,
    getAdminUiConfig,
    isExternalInstance,
} from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';

import Delete from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/actions/delete/delete';
import DeleteModal from '../delete-modal/delete.modal';
import { getPermissions } from '../../../common/helpers/getters';

const DeleteContent = (props) => {
    const Translator = getTranslator();
    const adminUiConfig = getAdminUiConfig();
    const { item, isLoading, fetchedData } = props;
    const permissions = getPermissions(fetchedData[0]?.permissions, 'delete');
    const isDeletingUser = adminUiConfig.userContentTypes.includes(item.internalItem.contentTypeIdentifier);
    const deleteLabel = Translator.trans(/*@Desc("Delete")*/ 'actions.delete.label.delete', {}, 'ibexa_content_tree_ui');
    const trashLabel = Translator.trans(/*@Desc("Send to trash")*/ 'actions.delete.label.trash', {}, 'ibexa_content_tree_ui');
    const label = isDeletingUser || isExternalInstance() ? deleteLabel : trashLabel;
    const hasAccess = permissions?.hasAccess ?? false;
    const locationDepth = item.path.split('/').length;
    const checkIsDisabled = () => isLoading || !hasAccess || locationDepth === 1;
    const renderModal = (treeBuilderDeleteModalProps) => {
        if (isLoading) {
            return null;
        }

        return <DeleteModal confirmBtnLabel={label} item={item} {...treeBuilderDeleteModalProps} />;
    };

    return <Delete label={label} isLoading={isLoading} checkIsDisabled={checkIsDisabled} renderModal={renderModal} {...props} />;
};

DeleteContent.propTypes = {
    item: PropTypes.object,
    isLoading: PropTypes.bool,
    fetchedData: PropTypes.arrayOf(PropTypes.object),
};

DeleteContent.defaultProps = {
    item: {},
    isLoading: false,
    fetchedData: [],
};

export default DeleteContent;
