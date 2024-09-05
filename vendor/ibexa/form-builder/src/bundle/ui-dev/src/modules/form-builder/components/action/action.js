import React from 'react';
import PropTypes from 'prop-types';

const Action = ({ icon, onClick, title }) => {
    return (
        <button
            className="btn ibexa-btn ibexa-btn--ghost ibexa-btn--small ibexa-btn--no-text c-action"
            type="button"
            onClick={onClick}
            title={title}
        >
            {icon}
        </button>
    );
};

Action.propTypes = {
    icon: PropTypes.element.isRequired,
    onClick: PropTypes.func.isRequired,
    title: PropTypes.string.isRequired,
};

export default Action;
