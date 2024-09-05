import React, { useContext } from 'react';
import { EventsSelectionContext, EventsConfigContext } from '../../../calendar.module';
import EventsActionBtn from './events.action.btn';

const { Translator } = window;

const EventsActions = () => {
    const [{ eventsType }, dispatchSelectEventAction] = useContext(EventsSelectionContext);
    const eventsConfig = useContext(EventsConfigContext);
    const selectedEventsConfig = eventsConfig[eventsType];
    const deselectAllEvents = () => {
        dispatchSelectEventAction({ type: 'DESELECT_ALL_EVENTS' });
    };

    if (!selectedEventsConfig) {
        return null;
    }

    const selectedEventsActions = selectedEventsConfig.actions;

    return (
        <div className="c-events-actions">
            {Object.entries(selectedEventsActions).map(([actionEvent, { icon, label }]) => (
                <EventsActionBtn key={actionEvent} actionEvent={actionEvent} iconUrl={icon} label={label} />
            ))}
            <button
                type="button"
                onClick={deselectAllEvents}
                className="btn ibexa-btn ibexa-btn--small ibexa-btn--secondary c-events-action-btn c-events-action-btn--deselect-all"
            >
                {Translator.trans(/*@Desc("Deselect all")*/ 'calendar.deselect_all', {}, 'ibexa_calendar_widget')}
            </button>
        </div>
    );
};

export default EventsActions;
