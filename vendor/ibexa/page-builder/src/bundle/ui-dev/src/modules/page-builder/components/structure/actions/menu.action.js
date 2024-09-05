import React from 'react';
import PropTypes from 'prop-types';
import ActionItem from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/components/action-list-item/action.list.item';
import { isItemEmpty } from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/helpers/item';

const StructureViewMenuAction = ({ item, itemLabel, eventType, action }) => {
    const isDisabled = isItemEmpty(item);
    const handleOnClick = () => {
        item.onItemClick(item.id);
        item.onItemHoverOut(item.id);
        document.body.dispatchEvent(
            new CustomEvent(`ibexa-pb-blocks:${eventType}`, {
                detail: { blockId: item.id, action },
            }),
        );
    };

    return <ActionItem label={itemLabel} isDisabled={isDisabled} onClick={handleOnClick} />;
};

StructureViewMenuAction.propTypes = {
    item: PropTypes.shape({
        id: PropTypes.string.isRequired,
        onItemHoverOut: PropTypes.func.isRequired,
        onItemClick: PropTypes.func.isRequired,
    }).isRequired,
    itemLabel: PropTypes.string.isRequired,
    eventType: PropTypes.string.isRequired,
    action: PropTypes.string.isRequired,
};

StructureViewMenuAction.defaultProps = {};

export default StructureViewMenuAction;
