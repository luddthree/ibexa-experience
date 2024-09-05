import React, { useContext, useEffect, useRef } from 'react';

import { CALENDAR_VIEWS, CurrentCalendarViewContext } from '../calendar-view/calendar.view';
import Dropdown from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/dropdown/dropdown';

const { Translator } = window;

const CalendarViewSwitcher = () => {
    const viewTypeListRef = useRef(null);
    const [currentCalendarView, setCurrentCalendarView] = useContext(CurrentCalendarViewContext);
    const monthLabel = Translator.trans(/*@Desc("Month")*/ 'calendar.view_switcher.month', {}, 'ibexa_calendar_widget');
    const weekLabel = Translator.trans(/*@Desc("Week")*/ 'calendar.view_switcher.week', {}, 'ibexa_calendar_widget');
    const dayLabel = Translator.trans(/*@Desc("Day")*/ 'calendar.view_switcher.day', {}, 'ibexa_calendar_widget');
    const viewTypeOptions = [
        {
            label: monthLabel,
            value: CALENDAR_VIEWS.MONTH,
        },
        {
            label: weekLabel,
            value: CALENDAR_VIEWS.WEEK,
        },
        {
            label: dayLabel,
            value: CALENDAR_VIEWS.DAY,
        },
    ];

    useEffect(() => {
        window.ibexa.helpers.tooltips.hideAll();
    }, [currentCalendarView]);

    return (
        <div className="c-calendar-view-switcher">
            <Dropdown
                single={true}
                withoutSearch={true}
                value={currentCalendarView}
                options={viewTypeOptions}
                dropdownListRef={viewTypeListRef}
                onChange={setCurrentCalendarView}
            />
            <div ref={viewTypeListRef} className="c-calendar-view-switcher__view-type-list" />
        </div>
    );
};

export default CalendarViewSwitcher;
