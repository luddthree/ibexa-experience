import React from 'react';
import PropTypes from 'prop-types';
import CalendarViewSwitcher from '../calendar-view-switcher/calendar.view.switcher';
import DatePaginator from '../date-paginator/date.paginator';

const TopBar = ({ paginatorType, isCalendarViewSwitcherVisible }) => {
    let calendarViewSwitcher = null;

    if (isCalendarViewSwitcherVisible) {
        calendarViewSwitcher = <CalendarViewSwitcher />;
    }

    return (
        <div className="c-top-bar">
            <div className="c-top-bar__menu">
                <div className="c-top-bar__date-paginator-wrapper">
                    <DatePaginator type={paginatorType} />
                </div>
                <div className="c-top-bar__view-switcher-wrapper">{calendarViewSwitcher}</div>
            </div>
        </div>
    );
};

TopBar.propTypes = {
    paginatorType: PropTypes.string.isRequired,
    isCalendarViewSwitcherVisible: PropTypes.bool.isRequired,
};

export default TopBar;
