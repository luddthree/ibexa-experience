import React, { useContext } from 'react';
import PropTypes from 'prop-types';

import { getTranslator } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';
import { getPermissions } from '@ibexa-content-tree/src/bundle/ui-dev/src/modules/common/helpers/getters';

import ActionItem from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/components/action-list-item/action.list.item';
import {
    QUICK_ACTION_MODES,
    QuickActionsContext,
} from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/tree.builder.module';

const QuickCreateContent = ({ item, isLoading, fetchedData }) => {
    const Translator = getTranslator();
    const { setQuickActionMode, setQuickActionItemId } = useContext(QuickActionsContext);
    const permissions = getPermissions(fetchedData[0]?.permissions, 'create');
    const hasAccess = permissions?.hasAccess ?? false;
    const isDisabled = isLoading || !hasAccess;
    const itemLabel = Translator.trans(/*@Desc("Create")*/ 'actions.create_content', {}, 'ibexa_image_picker');

    return (
        <ActionItem
            isLoading={isLoading}
            isDisabled={isDisabled}
            label={itemLabel}
            onClick={() => {
                setQuickActionMode(QUICK_ACTION_MODES.CREATE);
                setQuickActionItemId(item.id);
            }}
        />
    );
};

QuickCreateContent.propTypes = {
    item: PropTypes.object,
    fetchedData: PropTypes.arrayOf(PropTypes.object),
    isLoading: PropTypes.bool,
};

QuickCreateContent.defaultProps = {
    item: {},
    fetchedData: [],
    isLoading: false,
};

export default QuickCreateContent;
