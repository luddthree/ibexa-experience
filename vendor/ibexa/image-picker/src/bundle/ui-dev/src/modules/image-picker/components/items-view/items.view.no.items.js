import React from 'react';

import { getTranslator, getRestInfo } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';
import EmptyTableImage from '../../../../../../Resources/public/img/upload-images.svg';

const ItemsViewNoItems = () => {
    const Translator = getTranslator();
    const noItemInfoText = Translator.trans(/*@Desc("No images yet")*/ 'items_view.no_items.info_text', {}, 'ibexa_image_picker');
    const noItemActionText = Translator.trans(
        /*@Desc("Drag and drop files here to upload or click the Upload button.")*/ 'items_view.no_items.action_text',
        {},
        'ibexa_image_picker',
    );
    const { instanceUrl } = getRestInfo();

    return (
        <table className="ibexa-table table">
            <tbody className="ibexa-table__body">
                <tr className="ibexa-table__row">
                    <td className="ibexa-table__empty-table-cell">
                        {instanceUrl !== window.origin ? (
                            <EmptyTableImage className="ibexa-table__empty-table-image" />
                        ) : (
                            <img
                                className="ibexa-table__empty-table-image"
                                src="/bundles/ibexaimagepicker/img/upload-images.svg"
                                alt={noItemInfoText}
                            />
                        )}
                        <div className="ibexa-table__empty-table-text">
                            <div className="ibexa-table__empty-table-info-text">{noItemInfoText}</div>
                            <div className="ibexa-table__empty-table-action-text">{noItemActionText}</div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    );
};

export default ItemsViewNoItems;
