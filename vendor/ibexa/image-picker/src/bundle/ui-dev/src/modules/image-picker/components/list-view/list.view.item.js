import React from 'react';
import PropTypes from 'prop-types';

import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';
import Thumbnail from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/thumbnail/thumbnail';
import { formatShortDateTime } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/timezone.helper';

import SelectionToggler from '../selection.toggler/selection.toggler';

const ListViewItem = ({ item }) => {
    const className = createCssClassNames({
        'ibexa-table__row ibexa-table__row--selectable': true,
        'ibexa-table__row--selected': item.isSelected,
    });
    const shouldRenderImage = item.thumbnail.mimeType !== 'image/svg+xml';

    return (
        <tr className={className} onClick={item.onClick}>
            <td className="ibexa-table__cell ibexa-table__cell--has-icon">
                <SelectionToggler itemId={item.id} isSelected={item.isSelected} />
            </td>
            <td className="ibexa-table__cell">
                <div className="ibexa-table__thumbnail">
                    {shouldRenderImage ? (
                        <img className="ibexa-grid-view-item__image" src={item.thumbnail.resource} />
                    ) : (
                        <Thumbnail
                            thumbnailData={item.thumbnail}
                            iconExtraClasses="ibexa-icon--extra-large"
                            contentTypeIconPath={item.iconPath}
                        />
                    )}
                </div>
            </td>
            <td className="ibexa-table__cell">{item.name}</td>
            <td className="ibexa-table__cell">{item.fileFormat}</td>
            <td className="ibexa-table__cell">{item.fileSize}</td>
            <td className="ibexa-table__cell">
                {item.imageWidth} x {item.imageHeight}
            </td>
            <td className="ibexa-table__cell">{formatShortDateTime(item.created)}</td>
            <td className="ibexa-table__cell">{formatShortDateTime(item.updated)}</td>
        </tr>
    );
};

ListViewItem.propTypes = {
    item: PropTypes.object.isRequired,
};

export default ListViewItem;
