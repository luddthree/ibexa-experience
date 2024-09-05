import React, { useContext } from 'react';
import PropTypes from 'prop-types';

import ActionItem from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/components/action-list-item/action.list.item';
import { getPermissions } from '../../../common/helpers/getters';
import { findSuggestions } from '../../../common/services/content.tree.service';
import { RestInfoContext } from '../../content.tree.module';

const { Translator, document } = window;

const CreateContent = ({ item, isLoading, fetchedData }) => {
    const restInfo = useContext(RestInfoContext);
    const itemLabel = Translator.trans(/*@Desc("Create")*/ 'actions.create_content', {}, 'ibexa_content_tree_ui');
    const permissions = getPermissions(fetchedData[0]?.permissions, 'create');
    const hasAccess = permissions?.hasAccess ?? false;
    const isDisabled = !item.internalItem.isContainer || isLoading || !hasAccess;
    const createContent = () => {
        findSuggestions(item.id, { ...restInfo }).then((suggestions) => {
            document.body.dispatchEvent(
                new CustomEvent('ibexa-content-tree:open-create-widget', {
                    detail: {
                        item,
                        suggestions,
                        permissions,
                    },
                }),
            );
        });
    };

    return <ActionItem label={itemLabel} isLoading={isLoading} isDisabled={isDisabled} onClick={createContent} />;
};

CreateContent.propTypes = {
    item: PropTypes.object,
    isLoading: PropTypes.bool,
    fetchedData: PropTypes.arrayOf(PropTypes.object),
};

CreateContent.defaultProps = {
    item: {},
    isLoading: false,
    fetchedData: [],
};

export default CreateContent;
