import React from 'react';
import PropTypes from 'prop-types';

const EventBadge = ({ event }) => {
    const renderEventDetailsRow = ([attributeKey, { label, value }]) => {
        return (
            <tr key={attributeKey} className="c-event-badge__property">
                <td className="c-event-badge__property-label">{label}:</td>
                <td className="c-event-badge__property-value">{value}</td>
            </tr>
        );
    };

    return (
        <div className="c-event-badge">
            <div className="c-event-badge__title">{event.name}</div>
            <div className="c-event-badge__subtitle">{event.subname}</div>
            <table className="c-event-badge__properties">
                <tbody>{Object.entries(event.attributes).map(renderEventDetailsRow)}</tbody>
            </table>
        </div>
    );
};

EventBadge.propTypes = {
    event: PropTypes.object,
};

EventBadge.defaultProps = {
    event: {},
};

export default EventBadge;
