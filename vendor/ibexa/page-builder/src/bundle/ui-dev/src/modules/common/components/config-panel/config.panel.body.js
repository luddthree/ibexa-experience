import React from 'react';
import PropTypes from 'prop-types';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

const ConfigPanelBody = ({ children, extraClasses }) => {
    const className = createCssClassNames({
        'ibexa-pb-config-panel__body': true,
        [extraClasses]: true,
    });

    return <div className={className}>{children}</div>;
};

ConfigPanelBody.propTypes = {
    children: PropTypes.node,
    extraClasses: PropTypes.string,
};

ConfigPanelBody.defaultProps = {
    children: null,
    extraClasses: '',
};

export default ConfigPanelBody;
