import React, { useContext } from 'react';
import PropTypes from 'prop-types';

import ActionItem from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/components/action-list-item/action.list.item';
import { isItemEmpty } from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/helpers/item';
import { SelectedContext } from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/components/selected-provider/selected.provider';
import { STORED_ITEMS_CLEAR } from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/hooks/useStoredItemsReducer';
import { SubIdContext } from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/tree.builder.module';

import { assignContent as assignContentService } from '../../../common/services/taxonomy.tree.service';
import { RestInfoContext } from '../../taxonomy.tree.module';

const { document, Translator, ReactDOM, ibexa } = window;

const AssignContent = ({ item }) => {
    const taxonomyName = useContext(SubIdContext);
    const restInfo = useContext(RestInfoContext);
    const { selectedData: contextSelectedData, dispatchSelectedData } = useContext(SelectedContext);
    const itemLabel = Translator.trans(/*@Desc("Assign")*/ 'actions.assign', {}, 'ibexa_taxonomy_ui');
    const selectedData = isItemEmpty(item) ? contextSelectedData : [item];
    const isDisabled = selectedData.length === 0;
    const assignContent = () => {
        const udwContainer = document.getElementById('react-udw');
        const udwRoot = ReactDOM.createRoot(udwContainer);
        const configContainer = document.querySelector('div[data-assign-content-udw-config]');
        const closeUDW = () => udwRoot.unmount();
        const onConfirm = (contentItems) => {
            const entriesIds = selectedData.map((selectedItem) => selectedItem.id);
            const contentIds = contentItems.map((contentItem) => contentItem.ContentInfo.Content._id);

            assignContentService(contentIds, entriesIds, { restInfo, taxonomyName })
                .then(() => {
                    let message;

                    if (selectedData.length === 1) {
                        message = Translator.trans(
                            /*@Desc("Content has been assigned to taxonomy entry")*/ 'actions.assign_content.success.single',
                            {},
                            'ibexa_taxonomy_ui',
                        );
                    } else {
                        message = Translator.trans(
                            /*@Desc("Content has been assigned to taxonomy entries")*/ 'actions.assign_content.success.multi',
                            {},
                            'ibexa_taxonomy_ui',
                        );
                    }

                    ibexa.helpers.notification.showSuccessNotification(message);

                    window.location.reload(true);
                })
                .catch(() => {
                    const message = Translator.trans(
                        /*@Desc("An unexpected error occurred while assigning Content. Please try again later.")*/
                        'actions.error.message',
                        {},
                        'ibexa_taxonomy_ui',
                    );

                    ibexa.helpers.notification.showErrorNotification(message);
                });

            closeUDW();
            dispatchSelectedData({ type: STORED_ITEMS_CLEAR });
        };
        const onCancel = () => closeUDW();
        const openUDW = () => {
            const config = JSON.parse(configContainer.dataset.assignContentUdwConfig);
            const title = Translator.trans(
                /*@Desc("Select Content to assign")*/ 'actions.assign_content.udw_title',
                {},
                'ibexa_universal_discovery_widget',
            );

            udwRoot.render(
                React.createElement(ibexa.modules.UniversalDiscovery, {
                    onConfirm,
                    onCancel,
                    title,
                    ...config,
                }),
            );
        };

        openUDW();
    };

    return <ActionItem label={itemLabel} isDisabled={isDisabled} onClick={assignContent} />;
};

AssignContent.propTypes = {
    item: PropTypes.object,
};

AssignContent.defaultProps = {
    item: {},
};

export default AssignContent;
