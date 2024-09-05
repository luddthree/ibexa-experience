import React from 'react';
import PropTypes from 'prop-types';

const EventsList = ({ events, onSelectedTimestampChange }) => {
    const handleClick = (date) => onSelectedTimestampChange(date * 1000);

    return (
        <div className="c-pb-events-list">
            <ul className="c-pb-events-list__list">
                {events.map((event, index) => (
                    <li
                        key={index} // eslint-disable-line react/no-array-index-key
                        className="c-pb-events-list__item"
                        onClick={() => handleClick(event.date)}
                        dangerouslySetInnerHTML={{ __html: event.description }}
                    />
                ))}
            </ul>
        </div>
    );
};

EventsList.propTypes = {
    events: PropTypes.array.isRequired,
    onSelectedTimestampChange: PropTypes.func.isRequired,
};

export default EventsList;
