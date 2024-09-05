import React from 'react';
import PropTypes from 'prop-types';

import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

/**
 * @deprecated This component is deprecated and will be removed in 5.0 use IndentationHorizontal instead
 */
const IndentationVertical = ({ itemDepth, isHidden, hasSubitems }) => {
    console.warn('Component IndentationVertical is deprecated and will be removed in 5.0 use IndentationHorizontal instead');

    const indentationClass = createCssClassNames({
        'c-tb-list-item-single__indentation-line': true,
        'c-tb-list-item-single__indentation-line--vertical': true,
        'c-tb-list-item-single__indentation-line--hidden': isHidden,
        'c-tb-list-item-single__indentation-line--no-sub-items': !hasSubitems,
    });

    return (
        <div className="c-tb-list-item-single__indentation" style={{ '--indent': itemDepth }}>
            <div className={indentationClass} style={{ '--indent': itemDepth }} />
        </div>
    );
};

IndentationVertical.propTypes = {
    itemDepth: PropTypes.number.isRequired,
    isHidden: PropTypes.bool.isRequired,
    hasSubitems: PropTypes.bool.isRequired,
};

export default IndentationVertical;
