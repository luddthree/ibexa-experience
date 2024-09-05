import React, { useContext } from 'react';
import PropTypes from 'prop-types';

import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';
import { MultipleConfigContext } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/universal-discovery/universal.discovery.module';

const SelectionToggler = ({ isHidden, isSelected }) => {
    const [multiple] = useContext(MultipleConfigContext);
    const className = createCssClassNames({
        'c-udw-toggle-selection ibexa-input': true,
        'ibexa-input--checkbox': multiple,
        'ibexa-input--radio': !multiple,
    });
    const inputType = multiple ? 'checkbox' : 'radio';

    return <input type={inputType} className={className} checked={isSelected} disabled={isHidden} readOnly={true} />;
};

SelectionToggler.propTypes = {
    isSelected: PropTypes.bool.isRequired,
    isHidden: PropTypes.bool,
};

SelectionToggler.defaultProps = {
    isHidden: false,
};

export default SelectionToggler;
