import React from 'react';
import PropTypes from 'prop-types';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

console.warn('You are using deprecated component. Use ibexa.core.Backdrop instead.');

/**
 * @deprecated This component is deprecated now you can use ibexa.core.Backdrop instead.
 */
const Backdrop = ({ extraClasses, isOpen }) => {
    const className = createCssClassNames({
        'ibexa-backdrop': true,
        [extraClasses]: true,
    });

    if (!isOpen) {
        return null;
    }

    return <div className={className} />;
};

Backdrop.propTypes = {
    extraClasses: PropTypes.string,
    isOpen: PropTypes.bool,
};

Backdrop.defaultProps = {
    extraClasses: '',
    isOpen: true,
};

export default Backdrop;
