import React, { useState, useRef } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';

import CalendarHeader from './calendar/components/calendar-header/calendar.header';
import CalendarView from './calendar/components/calendar-view/calendar.view';
import ListView from './calendar/components/list-view/list.view';
import SelectionSidebar from './calendar/components/selection-sidebar/selection.sidebar';
import SelectionConfirmationPopup from './calendar/components/selection-confirmation-popup/selection.confirmation.popup';
import { useSelectionReducer } from './calendar/hooks/useSelectionReducer';
import { useFiltersReducer } from './calendar/hooks/useFiltersReducer';

import SidebarFilters from './calendar/components/sidebar/sidebar.filters';
import SidebarDayPicker from './calendar/components/sidebar/sidebar.day.picker';

const { ibexa } = window;
const { convertDateToTimezone } = ibexa.helpers.timezone;

export const MODULE_VIEWS = {
    CALENDAR: 'CALENDAR',
    LIST: 'LIST',
};

export const SelectedDateContext = React.createContext(null);
export const RestInfoContext = React.createContext(null);
export const EventsSelectionContext = React.createContext(null);
export const EventsConfigContext = React.createContext(null);
export const EventsTooltipRefContext = React.createContext();
export const CalendarFiltersContext = React.createContext(null);

const CalendarModule = ({ restInfo, eventsConfig }) => {
    const [selectedDate, setSelectedDate] = useState(convertDateToTimezone(new Date(), undefined, true));
    const [currentView, setCurrentView] = useState(MODULE_VIEWS.CALENDAR);
    const [eventsSelection, dispatchSelectEventAction] = useSelectionReducer();
    const [calendarFilters, dispatchCalendarFiltersAction] = useFiltersReducer(eventsConfig);
    const eventsTooltipContainerRef = useRef(null);
    const renderView = () => {
        switch (currentView) {
            case MODULE_VIEWS.CALENDAR:
                return <CalendarView />;
            case MODULE_VIEWS.LIST:
                return <ListView />;
        }
    };

    return (
        <RestInfoContext.Provider value={restInfo}>
            <EventsSelectionContext.Provider value={[eventsSelection, dispatchSelectEventAction]}>
                <CalendarFiltersContext.Provider value={[calendarFilters, dispatchCalendarFiltersAction]}>
                    <EventsConfigContext.Provider value={eventsConfig}>
                        <div className="m-calendar">
                            <div className="m-calendar__header">
                                <CalendarHeader currentView={currentView} setCurrentView={setCurrentView} />
                            </div>
                            <SelectedDateContext.Provider value={[selectedDate, setSelectedDate]}>
                                <div className="m-calendar__sidebar">
                                    <SidebarDayPicker />
                                    <SidebarFilters />
                                </div>
                                <EventsTooltipRefContext.Provider value={eventsTooltipContainerRef}>
                                    <div className="m-calendar__view">{renderView()}</div>
                                </EventsTooltipRefContext.Provider>
                            </SelectedDateContext.Provider>
                            {ReactDOM.createPortal(<SelectionSidebar />, document.querySelector('.ibexa-calendar__sidebar-wrapper'))}
                            <SelectionConfirmationPopup />
                        </div>
                        <div className="ibexa-calendar-tooltips-container" ref={eventsTooltipContainerRef} />
                    </EventsConfigContext.Provider>
                </CalendarFiltersContext.Provider>
            </EventsSelectionContext.Provider>
        </RestInfoContext.Provider>
    );
};

CalendarModule.propTypes = {
    restInfo: PropTypes.shape({
        token: PropTypes.string.isRequired,
        siteaccess: PropTypes.string.isRequired,
    }).isRequired,
    eventsConfig: PropTypes.object.isRequired,
};

CalendarModule.defaultProps = {};

ibexa.addConfig('modules.Calendar', CalendarModule);

export default CalendarModule;
