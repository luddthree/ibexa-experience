import React from 'react';
import PropTypes from 'prop-types';

const Toggler = ({ onClick, totalSubitemsCount }) => {
    const togglerAttrs = {
        className: 'c-tb-toggler',
        tabIndex: -1,
    };

    if (totalSubitemsCount > 0) {
        togglerAttrs.onClick = onClick;
    }

    return <div {...togglerAttrs} />;
};

Toggler.propTypes = {
    onClick: PropTypes.func.isRequired,
    totalSubitemsCount: PropTypes.number.isRequired,
};

export default Toggler;
