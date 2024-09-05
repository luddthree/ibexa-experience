import React, { useContext, useEffect } from 'react';
import PropTypes from 'prop-types';
import { EventsSelectionContext } from '../../../calendar.module';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';

const EventsActionBtn = ({ iconUrl, actionEvent, label }) => {
    const [{ eventsType, selectedEvents }] = useContext(EventsSelectionContext);
    const handleClick = () => {
        const event = new CustomEvent(`${eventsType}:${actionEvent}`, { detail: { events: selectedEvents } });

        window.document.body.dispatchEvent(event);
    };

    useEffect(() => {
        window.ibexa.helpers.tooltips.parse();
    }, []);

    return (
        <button
            className={`btn ibexa-btn ibexa-btn--small ibexa-btn--ghost c-events-action-btn c-events-action-btn--${actionEvent}`}
            title={label}
            onClick={handleClick}
            type="button"
        >
            <Icon customPath={iconUrl} extraClasses="ibexa-icon--small" />
            <span className="ibexa-btn__label">{label}</span>
        </button>
    );
};

EventsActionBtn.propTypes = {
    iconUrl: PropTypes.string.isRequired,
    label: PropTypes.string.isRequired,
    actionEvent: PropTypes.string.isRequired,
};

EventsActionBtn.defaultProps = {};

export default EventsActionBtn;
