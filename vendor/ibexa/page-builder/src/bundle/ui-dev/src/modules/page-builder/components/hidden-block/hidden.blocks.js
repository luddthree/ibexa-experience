import React from 'react';
import PropTypes from 'prop-types';

import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';

const HiddenBlocks = ({ blocksToRender }) => (
    <div className="c-pb-hidden-blocks">
        {blocksToRender.map(({ type, name, thumbnail }) => (
            <div key={name} className="c-pb-hidden-block" data-ibexa-toolbox-block-type={type} hidden={true}>
                <div className="c-pb-hidden-block__content">
                    <div className="c-pb-hidden-block__drag">
                        <Icon name="drag" extraClasses="c-pb-hidden-block__drag-icon ibexa-icon--tiny-small" />
                    </div>
                    <div className="c-pb-hidden-block__type">
                        <Icon customPath={thumbnail} extraClasses="ibexa-icon--small" />
                    </div>
                    <div className="c-pb-hidden-block__label">{name}</div>
                </div>
            </div>
        ))}
    </div>
);

HiddenBlocks.propTypes = {
    blocksToRender: PropTypes.arrayOf(
        PropTypes.shape({
            type: PropTypes.string.isRequired,
            name: PropTypes.string.isRequired,
            thumbnail: PropTypes.string.isRequired,
        }).isRequired,
    ).isRequired,
};

export default HiddenBlocks;
