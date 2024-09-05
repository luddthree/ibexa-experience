import React from 'react';
import PropTypes from 'prop-types';

import ViewToggler from '../view-toggler/view.toggler';

const { Translator } = window;

const CalendarHeader = ({ currentView, setCurrentView }) => {
    const headline = Translator.trans(/*@Desc("Scheduled content")*/ 'calendar.headline', {}, 'ibexa_calendar_widget');

    return (
        <div className="c-calendar-header">
            <div className="c-calendar-header__title">{headline}</div>
            <div className="c-calendar-header__btns">
                <ViewToggler currentView={currentView} setCurrentView={setCurrentView} />
            </div>
        </div>
    );
};

CalendarHeader.propTypes = {
    currentView: PropTypes.string.isRequired,
    setCurrentView: PropTypes.func.isRequired,
};

export default CalendarHeader;
