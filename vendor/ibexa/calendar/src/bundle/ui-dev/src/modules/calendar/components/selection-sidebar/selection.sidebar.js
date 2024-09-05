import React, { useContext, useState, useEffect, useRef } from 'react';

import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';

import { EventsSelectionContext, EventsConfigContext } from '../../../calendar.module';
import EventsActions from '../events-actions/events.actions';
import EventBadge from '../event-badge/event.badge';

const { Translator, ibexa } = window;

const TRANSPARENT_VALUE = '7f';

const SelectionSidebar = () => {
    const refSidebar = useRef(null);
    const refExpandButton = useRef(null);
    const [isExpanded, setIsExpanded] = useState(false);
    const [shouldShowEmpty, setShouldShowEmpty] = useState(false);
    const eventsConfig = useContext(EventsConfigContext);
    const [eventsSelection, dispatchSelectEventAction] = useContext(EventsSelectionContext);
    const expandLabel = Translator.trans(/*@Desc("Expand sidebar")*/ 'calendar.expand.sidebar', {}, 'ibexa_calendar_widget');
    const collapseLabel = Translator.trans(/*@Desc("Collapse sidebar")*/ 'calendar.collapse.sidebar', {}, 'ibexa_calendar_widget');
    const removeLabel = Translator.trans(/*@Desc("Remove")*/ 'calendar.remove', {}, 'ibexa_calendar_widget');
    const { selectedEvents } = eventsSelection;
    const areEventsSelected = Object.keys(selectedEvents).length > 0;
    const className = createCssClassNames({
        'c-selection-sidebar': true,
        'c-selection-sidebar--expanded': isExpanded,
        'c-selection-sidebar--hidden': !areEventsSelected && !shouldShowEmpty,
    });
    const selectedEventsLabel = Translator.trans(
        /*@Desc("selected")*/ 'calendar.selection_sidebar.selected_info',
        {},
        'ibexa_calendar_widget',
    );
    const togglerLabel = isExpanded ? collapseLabel : expandLabel;
    const handleToggleSidebar = () => {
        setIsExpanded((isExpandedNext) => !isExpandedNext);
        setShouldShowEmpty(false);
    };
    const handleDeselectEvent = (eventId) => {
        ibexa.helpers.tooltips.hideAll(refSidebar.current);
        setShouldShowEmpty(true);
        dispatchSelectEventAction({ type: 'DESELECT_EVENT', eventId });
    };
    const renderEvents = () => {
        if (!isExpanded) {
            return null;
        }

        return Object.values(selectedEvents).map((event) => {
            const { color, icon } = eventsConfig[event.type];
            const iconWrapperStyle = {
                background: `${color}${TRANSPARENT_VALUE}`,
            };

            return (
                <div key={event.id} className="c-selection-sidebar__event">
                    <div className="c-selection-sidebar__event-icon-wrapper">
                        <div className="c-selection-sidebar__event-icon" style={iconWrapperStyle}>
                            <Icon name={icon ?? 'flag'} extraClasses="ibexa-icon--small" />
                        </div>
                    </div>
                    <div className="c-selection-sidebar__event-data-wrapper">
                        <EventBadge event={event} />
                    </div>
                    <div className="c-selection-sidebar__event-deselect-wrapper">
                        <button
                            type="button"
                            className="btn ibexa-btn ibexa-btn--ghost ibexa-btn--no-text c-selection-sidebar__deselect-event-btn"
                            onClick={handleDeselectEvent.bind(null, event.id)}
                            data-placement="left"
                            title={removeLabel}
                        >
                            <Icon name="discard" extraClasses="ibexa-icon--small" />
                        </button>
                    </div>
                </div>
            );
        });
    };

    useEffect(() => {
        ibexa.helpers.tooltips.parse(refSidebar.current);
    }, []);

    useEffect(() => {
        ibexa.helpers.tooltips.parse(refSidebar.current);

        if (refExpandButton.current) {
            refExpandButton.current.dataset.originalTitle = togglerLabel;
        }
    }, [isExpanded, selectedEvents]);

    return (
        <div className={className} ref={refSidebar}>
            <div className="c-selection-sidebar__header">
                <div className="c-selection-sidebar__header-selected-events-count">
                    <h2 className="c-selection-sidebar__header-selected-events-title">
                        {Object.keys(selectedEvents).length} {selectedEventsLabel}
                    </h2>
                </div>
                <button
                    type="button"
                    className="btn ibexa-btn ibexa-btn--ghost ibexa-btn--no-text c-selection-sidebar__expand-btn"
                    onClick={handleToggleSidebar}
                    title={togglerLabel}
                    ref={refExpandButton}
                >
                    <Icon name="caret-expanded" extraClasses="ibexa-icon--small" />
                </button>
            </div>
            <EventsActions />
            <div className="c-selection-sidebar__events-group">
                <div className="c-selection-sidebar__events-list">{renderEvents()}</div>
            </div>
        </div>
    );
};

SelectionSidebar.propTypes = {};

export default SelectionSidebar;
