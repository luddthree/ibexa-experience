import React from 'react';
import PropTypes from 'prop-types';

import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';
import Thumbnail from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/thumbnail/thumbnail';

import SelectionToggler from '../selection.toggler/selection.toggler';

const GridViewItem = ({ thumbnail, iconPath, title, detailA, detailB, isSelected, onClick, onDoubleClick }) => {
    const className = createCssClassNames({
        'ibexa-grid-view-item': true,
        'ibexa-grid-view-item--selected': isSelected,
    });
    const shouldRenderImage = thumbnail.mimeType !== 'image/svg+xml';

    return (
        <div className={className} onClick={onClick} onDoubleClick={onDoubleClick}>
            <div className="ibexa-grid-view-item__image-wrapper">
                {shouldRenderImage ? (
                    <img className="ibexa-grid-view-item__image" src={thumbnail.resource} />
                ) : (
                    <Thumbnail thumbnailData={thumbnail} iconExtraClasses="ibexa-icon--extra-large" contentTypeIconPath={iconPath} />
                )}
            </div>
            <div className="ibexa-grid-view-item__footer">
                <div className="ibexa-grid-view-item__title" title={title}>
                    {title}
                </div>
                {detailA && <div className="ibexa-grid-view-item__detail-a">{detailA}</div>}
                {detailB && <div className="ibexa-grid-view-item__detail-b">{detailB}</div>}
            </div>
            <div className="ibexa-grid-view-item__checkbox">
                <SelectionToggler isSelected={isSelected} isHidden={false} />
            </div>
        </div>
    );
};

GridViewItem.propTypes = {
    thumbnail: PropTypes.object.isRequired,
    iconPath: PropTypes.string.isRequired,
    title: PropTypes.string.isRequired,
    detailA: PropTypes.string,
    detailB: PropTypes.string,
    isSelected: PropTypes.bool,
    onClick: PropTypes.func,
    onDoubleClick: PropTypes.func,
};

GridViewItem.defaultProps = {
    detailA: null,
    detailB: null,
    isSelected: false,
    onClick: () => {},
    onDoubleClick: () => {},
};

export default GridViewItem;
