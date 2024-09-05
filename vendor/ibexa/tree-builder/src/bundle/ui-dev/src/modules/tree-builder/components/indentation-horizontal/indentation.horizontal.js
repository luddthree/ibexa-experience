import React from 'react';
import PropTypes from 'prop-types';

import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

const IndentationHorizontal = ({ itemDepth }) => {
    const indentationClass = createCssClassNames({
        'c-tb-list-item-single__indentation': true,
    });

    return <div className={indentationClass} style={{ '--indent': itemDepth }} />;
};

IndentationHorizontal.propTypes = {
    itemDepth: PropTypes.number.isRequired,
};

export default IndentationHorizontal;
