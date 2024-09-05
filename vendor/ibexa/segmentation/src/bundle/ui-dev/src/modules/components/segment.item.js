import React from 'react';
import PropTypes from 'prop-types';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

const SegmentItem = ({ id, name, isHidden, onChange }) => {
    const segmentItemClass = createCssClassNames({
        'c-segments__item': true,
        'c-segments__item--hidden': isHidden,
    });

    return (
        <li className={segmentItemClass}>
            <label className="c-segments__label">
                <input type="radio" name="segment" className="ibexa-input ibexa-input--radio" onChange={() => onChange(id)} />
                {name}
            </label>
        </li>
    );
};

SegmentItem.propTypes = {
    id: PropTypes.number.isRequired,
    name: PropTypes.string.isRequired,
    hidden: PropTypes.bool.isRequired,
    onChange: PropTypes.func.isRequired,
};

export default SegmentItem;
