import React, { useContext } from 'react';
import PropTypes from 'prop-types';

import ActionItem from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/components/action-list-item/action.list.item';
import { isItemEmpty } from '../../../../../../../../../tree-builder/src/bundle/ui-dev/src/modules/tree-builder/helpers/item';
import { SelectedContext } from '../../../../../../../../../tree-builder/src/bundle/ui-dev/src/modules/tree-builder/components/selected-provider/selected.provider';
import { STORED_ITEMS_CLEAR } from '../../../../../../../../../tree-builder/src/bundle/ui-dev/src/modules/tree-builder/hooks/useStoredItemsReducer';

import { addToBookmarks as addToBookmarksService } from '../../../common/services/content.tree.service';
import { RestInfoContext } from '../../content.tree.module';

const { Translator, document, ibexa } = window;

const AddToBookmarks = ({ item }) => {
    const restInfo = useContext(RestInfoContext);
    const { selectedData: contextSelectedData, dispatchSelectedData } = useContext(SelectedContext);
    const isMultiItemOperation = isItemEmpty(item);
    const selectedData = isMultiItemOperation ? contextSelectedData : [item];
    const selectedDataToBookmark = selectedData.filter((selectedItem) => !selectedItem.internalItem?.isBookmarked);
    const isDisabled = selectedDataToBookmark.length === 0;
    const itemLabelWithoutCount = Translator.trans(/*@Desc("Add to bookmarks")*/ 'actions.add_to_bookmarks', {}, 'ibexa_content_tree_ui');
    const itemLabelWithCount = Translator.trans(
        /*@Desc("Add to bookmarks (%count%)")*/ 'actions.add_to_bookmarks.with_count',
        { count: selectedDataToBookmark.length },
        'ibexa_content_tree_ui',
    );
    const isLabelWithCount = isMultiItemOperation && selectedDataToBookmark.length !== 0;
    const itemLabel = isLabelWithCount ? itemLabelWithCount : itemLabelWithoutCount;
    const addToBookmarks = () => {
        const items = selectedDataToBookmark.map(({ internalItem }) => ({ id: internalItem.locationId, ...internalItem }));

        addToBookmarksService(items, { ...restInfo }).then((response) => {
            const { success: bookmarkedItems, fail: notBookmarkedItems } = response;

            if (notBookmarkedItems.length) {
                const totalCount = bookmarkedItems.length + notBookmarkedItems.length;
                let message;

                if (totalCount === 1) {
                    message = Translator.trans(
                        /*@Desc("Selected item could not be bookmarked.")*/ 'bulk_bookmark.error.message.single',
                        {},
                        'ibexa_content_tree_ui',
                    );
                } else {
                    message = Translator.trans(
                        /*@Desc("%notBookmarkedCount% of the %totalCount% selected item(s) could not be bookmarked.")*/ 'bulk_bookmark.error.message.multi',
                        {
                            notBookmarkedCount: notBookmarkedItems.length,
                            totalCount,
                        },
                        'ibexa_content_tree_ui',
                    );
                }

                ibexa.helpers.notification.showWarningNotification(message);
            }

            if (bookmarkedItems.length) {
                const message = Translator.trans(
                    /*@Desc("Content item(s) bookmarked.")*/ 'bulk_bookmark.success.message',
                    {},
                    'ibexa_content_tree_ui',
                );

                ibexa.helpers.notification.showSuccessNotification(message);

                bookmarkedItems.forEach(({ locationId }) => {
                    document.body.dispatchEvent(
                        new CustomEvent('ibexa-bookmark-change', {
                            detail: {
                                bookmarked: true,
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

    return <ActionItem label={itemLabel} isDisabled={isDisabled} onClick={addToBookmarks} />;
};

AddToBookmarks.propTypes = {
    item: PropTypes.object,
};

AddToBookmarks.defaultProps = {
    item: {},
};

export default AddToBookmarks;
