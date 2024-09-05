import React, { useContext } from 'react';
import PropTypes from 'prop-types';

import ActionItem from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/components/action-list-item/action.list.item';

import { findLocationsById } from '../../../common/services/content.tree.service';
import { getPermissions } from '../../../common/helpers/getters';
import { RestInfoContext } from '../../content.tree.module';

const { Translator, document } = window;

const EditContent = ({ item, isLoading, fetchedData }) => {
    const restInfo = useContext(RestInfoContext);
    const itemLabel = Translator.trans(/*@Desc("Edit")*/ 'actions.edit_content', {}, 'ibexa_content_tree_ui');
    const permissions = getPermissions(fetchedData[0]?.permissions, 'edit');
    const hasAccess = permissions?.hasAccess ?? false;
    const isDisabled = isLoading || !hasAccess;
    const editContent = () => {
        findLocationsById({
            ...restInfo,
            id: item.id,
        }).then(([response]) => {
            const contentLanguages = response.ContentInfo.Content.CurrentVersion.Version.VersionInfo.VersionTranslationInfo.Language;

            document.body.dispatchEvent(
                new CustomEvent('ibexa-content-tree:open-edit-widget', {
                    detail: {
                        item,
                        contentLanguages,
                        permissions,
                    },
                }),
            );
        });
    };

    return <ActionItem label={itemLabel} isLoading={isLoading} isDisabled={isDisabled} onClick={editContent} />;
};

EditContent.propTypes = {
    item: PropTypes.object,
    isLoading: PropTypes.bool,
    fetchedData: PropTypes.arrayOf(PropTypes.object),
};

EditContent.defaultProps = {
    item: {},
    isLoading: false,
    fetchedData: [],
};

export default EditContent;
