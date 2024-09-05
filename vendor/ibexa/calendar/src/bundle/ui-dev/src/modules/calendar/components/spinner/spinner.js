import React from 'react';
import PropTypes from 'prop-types';

import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';

const LoadingSpinner = ({ isLoading }) => {
    if (!isLoading) {
        return null;
    }

    return (
        <div className="c-loading-spinner">
            <Icon name="spinner" extraClasses="c-loading-spinner__icon ibexa-icon--medium ibexa-spin" />
        </div>
    );
};

LoadingSpinner.propTypes = {
    isLoading: PropTypes.bool.isRequired,
};

export default LoadingSpinner;
