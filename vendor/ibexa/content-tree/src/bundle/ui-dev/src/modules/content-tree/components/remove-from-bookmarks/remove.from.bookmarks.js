import React, { useContext } from 'react';
import PropTypes from 'prop-types';

import ActionItem from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/components/action-list-item/action.list.item';
import { isItemEmpty } from '../../../../../../../../../tree-builder/src/bundle/ui-dev/src/modules/tree-builder/helpers/item';
import { SelectedContext } from '../../../../../../../../../tree-builder/src/bundle/ui-dev/src/modules/tree-builder/components/selected-provider/selected.provider';
import { STORED_ITEMS_CLEAR } from '../../../../../../../../../tree-builder/src/bundle/ui-dev/src/modules/tree-builder/hooks/useStoredItemsReducer';

import { removeFromBookmarks as removeFromBookmarksService } from '../../../common/services/content.tree.service';
import { RestInfoContext } from '../../content.tree.module';

const { Translator, document, ibexa } = window;

const RemoveFromBookmarks = ({ item }) => {
    const restInfo = useContext(RestInfoContext);
    const { selectedData: contextSelectedData, dispatchSelectedData } = useContext(SelectedContext);
    const isMultiItemOperation = isItemEmpty(item);
    const selectedData = isMultiItemOperation ? contextSelectedData : [item];
    const selectedDataToUnbookmark = selectedData.filter((selectedItem) => selectedItem.internalItem?.isBookmarked);
    const isDisabled = selectedDataToUnbookmark.length === 0;
    const itemLabelWithoutCount = Translator.trans(
        /*@Desc("Remove from bookmarks")*/ 'actions.remove_from_bookmarks',
        {},
        'ibexa_content_tree_ui',
    );
    const itemLabelWithCount = Translator.trans(
        /*@Desc("Remove from bookmarks (%count%)")*/ 'actions.remove_from_bookmarks.with_count',
        { count: selectedDataToUnbookmark.length },
        'ibexa_content_tree_ui',
    );
    const isLabelWithCount = isMultiItemOperation && selectedDataToUnbookmark.length !== 0;
    const itemLabel = isLabelWithCount ? itemLabelWithCount : itemLabelWithoutCount;
    const removeFromBookmarks = () => {
        const items = selectedDataToUnbookmark.map(({ internalItem }) => ({ id: internalItem.locationId, ...internalItem }));

        removeFromBookmarksService(items, { ...restInfo }).then((response) => {
            const { success: bookmarkedItems, fail: notBookmarkedItems } = response;

            if (notBookmarkedItems.length) {
                const totalCount = bookmarkedItems.length + notBookmarkedItems.length;
                let message;

                if (totalCount === 1) {
                    message = Translator.trans(
                        /*@Desc("Selected item could not be removed from bookmarks.")*/ 'bulk_unbookmark.error.message.single',
                        {},
                        'ibexa_content_tree_ui',
                    );
                } else {
                    message = Translator.trans(
                        /*@Desc("%notBookmarkedCount% of the %totalCount% selected item(s) could not be removed from bookmarks.")*/ 'bulk_unbookmark.error.message.multi',
                        {
                            notBookmarkedCount: notBookmarkedItems.length,
                            totalCount: bookmarkedItems.length + notBookmarkedItems.length,
                        },
                        'ibexa_content_tree_ui',
                    );
                }

                ibexa.helpers.notification.showWarningNotification(message);
            }

            if (bookmarkedItems.length) {
                const message = Translator.trans(
                    /*@Desc("Content item(s) removed from bookmarks.")*/ 'bulk_unbookmark.success.message',
                    {},
                    'ibexa_content_tree_ui',
                );

                ibexa.helpers.notification.showSuccessNotification(message);

                bookmarkedItems.forEach(({ locationId }) => {
                    document.body.dispatchEvent(
                        new CustomEvent('ibexa-bookmark-change', {
                            detail: {
                                bookmarked: false,
                                locationId,
                            },
                        }),
                    );
                });
                document.body.dispatchEvent(new CustomEvent('ibexa-content-tree-refresh'));
            }

            dispatchSelectedData({ type: STORED_ITEMS_CLEAR });

            return Promise.resolve();
        });
    };

    return <ActionItem label={itemLabel} isDisabled={isDisabled} onClick={removeFromBookmarks} />;
};

RemoveFromBookmarks.propTypes = {
    item: PropTypes.object,
};

RemoveFromBookmarks.defaultProps = {
    item: {},
};

export default RemoveFromBookmarks;
