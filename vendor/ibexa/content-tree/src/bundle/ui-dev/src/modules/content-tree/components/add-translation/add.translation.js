import React, { useContext } from 'react';
import PropTypes from 'prop-types';

import ActionItem from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/components/action-list-item/action.list.item';

import { findLocationsById } from '../../../common/services/content.tree.service';
import { getPermissions } from '../../../common/helpers/getters';
import { RestInfoContext } from '../../content.tree.module';

const { Translator, document } = window;

const AddTranslation = ({ item, isLoading, fetchedData }) => {
    const restInfo = useContext(RestInfoContext);
    const itemLabel = Translator.trans(/*@Desc("Add translation")*/ 'actions.add_translation', {}, 'ibexa_content_tree_ui');
    const permissions = getPermissions(fetchedData[0]?.permissions, 'create');
    const hasAccess = permissions?.hasAccess ?? false;
    const isDisabled = isLoading || !hasAccess;
    const addTranslation = () => {
        findLocationsById({
            ...restInfo,
            id: item.id,
        }).then(([response]) => {
            const contentLanguages = response.ContentInfo.Content.CurrentVersion.Version.VersionInfo.VersionTranslationInfo.Language;

            document.body.dispatchEvent(
                new CustomEvent('ibexa-content-tree:open-language-modal', {
                    detail: {
                        item,
                        contentLanguages,
                        permissions,
                    },
                }),
            );
        });
    };

    return <ActionItem label={itemLabel} isLoading={isLoading} isDisabled={isDisabled} onClick={addTranslation} />;
};

AddTranslation.propTypes = {
    item: PropTypes.object,
    isLoading: PropTypes.bool,
    fetchedData: PropTypes.arrayOf(PropTypes.object),
};

AddTranslation.defaultProps = {
    item: {},
    isLoading: false,
    fetchedData: [],
};

export default AddTranslation;
