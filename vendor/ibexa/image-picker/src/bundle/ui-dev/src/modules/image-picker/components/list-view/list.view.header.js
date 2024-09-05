import React from 'react';

import { getTranslator } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';

const ListViewHeader = () => {
    const Translator = getTranslator();
    const thumbnailLabel = Translator.trans(/*@Desc("Thumbnail")*/ 'list_view.header.thumbnail', {}, 'ibexa_image_picker');
    const nameLabel = Translator.trans(/*@Desc("Name")*/ 'list_view.header.name', {}, 'ibexa_image_picker');
    const fileFormatLabel = Translator.trans(/*@Desc("File format")*/ 'list_view.header.file_format', {}, 'ibexa_image_picker');
    const sizeLabel = Translator.trans(/*@Desc("Size")*/ 'list_view.header.size', {}, 'ibexa_image_picker');
    const dimensionsLabel = Translator.trans(/*@Desc("Dimensions")*/ 'list_view.header.dimensions', {}, 'ibexa_image_picker');
    const createdLabel = Translator.trans(/*@Desc("Created")*/ 'list_view.header.created', {}, 'ibexa_image_picker');
    const updatedLabel = Translator.trans(/*@Desc("Updated")*/ 'list_view.header.updated', {}, 'ibexa_image_picker');

    return (
        <thead>
            <tr className="ibexa-table__head-row">
                <th className="ibexa-table__header-cell" />
                <th className="ibexa-table__header-cell">
                    <span className="ibexa-table__header-cell-text-wrapper">{thumbnailLabel}</span>
                </th>
                <th className="ibexa-table__header-cell">
                    <span className="ibexa-table__header-cell-text-wrapper">{nameLabel}</span>
                </th>
                <th className="ibexa-table__header-cell">
                    <span className="ibexa-table__header-cell-text-wrapper">{fileFormatLabel}</span>
                </th>
                <th className="ibexa-table__header-cell">
                    <span className="ibexa-table__header-cell-text-wrapper">{sizeLabel}</span>
                </th>
                <th className="ibexa-table__header-cell">
                    <span className="ibexa-table__header-cell-text-wrapper">{dimensionsLabel}</span>
                </th>
                <th className="ibexa-table__header-cell">
                    <span className="ibexa-table__header-cell-text-wrapper">{createdLabel}</span>
                </th>
                <th className="ibexa-table__header-cell">
                    <span className="ibexa-table__header-cell-text-wrapper">{updatedLabel}</span>
                </th>
            </tr>
        </thead>
    );
};

export default ListViewHeader;
