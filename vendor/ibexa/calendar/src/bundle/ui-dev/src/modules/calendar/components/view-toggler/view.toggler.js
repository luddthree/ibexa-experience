import React from 'react';
import PropTypes from 'prop-types';

import SimpleDropdown from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/simple-dropdown/simple.dropdown';
import { MODULE_VIEWS } from '../../../calendar.module';

const { Translator } = window;

const ViewToggler = ({ currentView, setCurrentView }) => {
    const label = Translator.trans(/*@Desc("View")*/ 'view_toggler.view', {}, 'ibexa_calendar_widget');
    const viewOptions = [
        {
            iconName: 'view-list',
            label: Translator.trans(/*@Desc("List view")*/ 'view_toggler.list_view', {}, 'ibexa_calendar_widget'),
            value: MODULE_VIEWS.LIST,
        },
        {
            iconName: 'date',
            label: Translator.trans(/*@Desc("Calendar view")*/ 'view_toggler.calendar_view', {}, 'ibexa_calendar_widget'),
            value: MODULE_VIEWS.CALENDAR,
        },
    ];
    const selectedOption = viewOptions.find((option) => option.value === currentView);
    const updateViewType = ({ value }) => {
        setCurrentView(value);
    };

    return (
        <div className="c-view-toggler">
            <SimpleDropdown
                options={viewOptions}
                selectedOption={selectedOption}
                onOptionClick={updateViewType}
                selectedItemLabel={label}
                isSwitcher={true}
                extraClasses="c-view-toggler__dropdown"
            />
        </div>
    );
};

ViewToggler.propTypes = {
    currentView: PropTypes.string.isRequired,
    setCurrentView: PropTypes.func.isRequired,
};

export default ViewToggler;
