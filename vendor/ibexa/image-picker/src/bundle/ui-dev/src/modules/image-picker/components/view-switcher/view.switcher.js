import React from 'react';
import PropTypes from 'prop-types';

import SimpleDropdown from '@ibexa-admin-ui-modules/common/simple-dropdown/simple.dropdown';
import { getTranslator } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';

import { GRID_VIEW, LIST_VIEW } from '../items-view/items.view';

const ViewSwitcher = ({ onViewChange, activeView }) => {
    const Translator = getTranslator();
    const viewLabel = Translator.trans(/*@Desc("View")*/ 'view_switcher.view', {}, 'ibexa_image_picker');
    const switchView = ({ value }) => {
        onViewChange(value);
    };
    const viewOptions = [
        {
            iconName: 'view-list',
            label: Translator.trans(/*@Desc("List view")*/ 'view_switcher.list_view', {}, 'ibexa_image_picker'),
            value: LIST_VIEW,
        },
        {
            iconName: 'view-grid',
            label: Translator.trans(/*@Desc("Grid view")*/ 'view_switcher.grid_view', {}, 'ibexa_image_picker'),
            value: GRID_VIEW,
        },
    ];
    const selectedOption = viewOptions.find((option) => option.value === activeView);

    return (
        <SimpleDropdown
            options={viewOptions}
            selectedOption={selectedOption}
            onOptionClick={switchView}
            selectedItemLabel={viewLabel}
            isSwitcher={true}
        />
    );
};

ViewSwitcher.propTypes = {
    onViewChange: PropTypes.func.isRequired,
    activeView: PropTypes.string.isRequired,
};

export default ViewSwitcher;
