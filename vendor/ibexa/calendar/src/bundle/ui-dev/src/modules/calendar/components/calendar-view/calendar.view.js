import React, { useState } from 'react';

import { DATE_PAGINATOR_TYPE } from '../date-paginator/date.paginator';
import MonthView from '../month-view/month.view';
import WeekView from '../week-view/week.view';
import DayView from '../day-view/day.view';
import TopBar from '../top-bar/top.bar';

export const CALENDAR_VIEWS = {
    MONTH: 'MONTH',
    WEEK: 'WEEK',
    DAY: 'DAY',
};

export const CurrentCalendarViewContext = React.createContext(null);

const CalendarView = () => {
    const [currentCalendarView, setCurrentCalendarView] = useState(CALENDAR_VIEWS.MONTH);
    const paginatorType = DATE_PAGINATOR_TYPE[currentCalendarView];
    let calendar = null;

    switch (currentCalendarView) {
        case CALENDAR_VIEWS.MONTH:
            calendar = <MonthView />;
            break;
        case CALENDAR_VIEWS.WEEK:
            calendar = <WeekView />;
            break;
        case CALENDAR_VIEWS.DAY:
            calendar = <DayView />;
    }

    return (
        <CurrentCalendarViewContext.Provider value={[currentCalendarView, setCurrentCalendarView]}>
            <div className="c-calendar-view">
                <TopBar isCalendarViewSwitcherVisible={true} paginatorType={paginatorType} />
                {calendar}
            </div>
        </CurrentCalendarViewContext.Provider>
    );
};

export default CalendarView;
