import React from 'react';
import PropTypes from 'prop-types';

import ActionItem from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/components/action-list-item/action.list.item';
import { getPermissions } from '../../../common/helpers/getters';

const { Translator, document } = window;

const HideContent = ({ item, isLoading, fetchedData }) => {
    const itemHideLabel = Translator.trans(/*@Desc("Hide")*/ 'actions.hide_content', {}, 'ibexa_content_tree_ui');
    const itemRevealLabel = Translator.trans(/*@Desc("Reveal")*/ 'actions.reveal_content', {}, 'ibexa_content_tree_ui');
    const permissions = getPermissions(fetchedData[0]?.permissions, 'hide');
    const hasAccess = permissions?.hasAccess ?? false;
    const isDisabled = isLoading || !hasAccess;
    const isInvisible = item.internalItem?.isInvisible ?? false;
    const itemLabel = isInvisible ? itemRevealLabel : itemHideLabel;
    const hideContent = () => {
        if (isInvisible) {
            document.body.dispatchEvent(
                new CustomEvent('ibexa-content-tree:reveal', {
                    detail: {
                        item,
                    },
                }),
            );
        } else {
            document.body.dispatchEvent(
                new CustomEvent('ibexa-content-tree:hide', {
                    detail: {
                        item,
                    },
                }),
            );
        }
    };

    return <ActionItem label={itemLabel} isLoading={isLoading} isDisabled={isDisabled} onClick={hideContent} />;
};

HideContent.propTypes = {
    item: PropTypes.object,
    isLoading: PropTypes.bool,
    fetchedData: PropTypes.arrayOf(PropTypes.object),
};

HideContent.defaultProps = {
    item: {},
    isLoading: false,
    fetchedData: [],
};

export default HideContent;
