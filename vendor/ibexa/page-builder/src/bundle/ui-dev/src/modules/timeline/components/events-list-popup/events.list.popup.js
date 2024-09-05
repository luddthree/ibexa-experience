import React from 'react';
import PropTypes from 'prop-types';
import EventsList from '../events-list/events.list';
import TimelinePopup, { TimelinePopupContext } from '../timeline-popup/timeline.popup';
import TimelinePopupContainer from '../timeline-popup/timeline.popup.container';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';

const { Translator } = window;

const EventsListPopup = (props) => {
    const isDisabled = props.events.length === 0;
    const togglerLabel = Translator.trans(/*@Desc("Jump to event")*/ 'timeline.events_list_popup.toggler.label', {}, 'ibexa_timeline');

    return (
        <TimelinePopup extraClasses="c-pb-events-list-popup">
            <TimelinePopupContext.Consumer>
                {({ togglePopup }) => (
                    <button
                        className="btn ibexa-btn ibexa-btn--ghost c-pb-events-list-popup__toggler"
                        onClick={togglePopup}
                        disabled={isDisabled}
                        type="button"
                    >
                        {togglerLabel}
                        <Icon name="caret-down" />
                    </button>
                )}
            </TimelinePopupContext.Consumer>
            <TimelinePopupContainer extraClasses="c-pb-events-list-popup__container">
                <EventsList {...props} />
            </TimelinePopupContainer>
        </TimelinePopup>
    );
};

EventsListPopup.propTypes = {
    events: PropTypes.array.isRequired,
    selectedTimestamp: PropTypes.number.isRequired,
};

export default EventsListPopup;
