import React, { Component, createRef } from 'react';
import PropTypes from 'prop-types';
import Thumb from '../thumb/thumb';
import EventMarker, { EVENT_MARKER_INSIDE_TIMELINE } from '../event-marker/event.marker';
import getEventInDayLeftPosition from '../../../helpers/event.in.day.position';

class Slider extends Component {
    constructor(props) {
        super(props);

        this._refSlider = createRef();

        this.renderMarker = this.renderMarker.bind(this);
        this.setTime = this.setTime.bind(this);
        this.groupEventsByDate = this.groupEventsByDate.bind(this);
        this.updateSliderRect = this.updateSliderRect.bind(this);

        const eventsToDisplay = this.filterEvents(props.events, props.selectedTimestamp);

        this.state = {
            sliderRect: {},
            eventsToDisplay,
            selectedTimestamp: props.selectedTimestamp,
            currentTimestamp: this.generateCurrentTimestamp(),
        };
    }

    generateCurrentTimestamp() {
        const { convertDateToTimezone } = window.ibexa.helpers.timezone;

        return convertDateToTimezone(new Date()).valueOf();
    }

    componentDidMount() {
        this.updateSliderRect();

        window.addEventListener('resize', this.updateSliderRect, false);

        setInterval(() => this.setState({ currentTimestamp: this.generateCurrentTimestamp() }), 1000);
    }

    componentWillUnmount() {
        window.removeEventListener('resize', this.updateSliderRect);
    }

    componentDidUpdate(prevProps) {
        const { selectedTimestamp, events } = this.props;
        const areSameEvents = events.every((event, index) => prevProps.events[index] && prevProps.events[index].date === event.date);

        if (prevProps.selectedTimestamp === selectedTimestamp && areSameEvents) {
            return;
        }

        const eventsToDisplay = this.filterEvents(events, selectedTimestamp);

        this.setState(() => ({ selectedTimestamp: this.props.selectedTimestamp, eventsToDisplay }));
    }

    updateSliderRect() {
        this.setState(() => ({ sliderRect: this._refSlider.current.getBoundingClientRect() }));
    }

    groupEventsByDate(now, total, event) {
        const { convertDateToTimezone } = window.ibexa.helpers.timezone;
        const eventDate = convertDateToTimezone(event.date * 1000, undefined, true);
        const isSameDate = eventDate.isSame(now, 'day');
        const timestamp = new Date(
            eventDate.year(),
            eventDate.month(),
            eventDate.date(),
            eventDate.hour(),
            eventDate.minute() + 1,
        ).getTime();

        if (!isSameDate) {
            return total;
        }

        if (total[timestamp]) {
            total[timestamp] = {
                descriptions: [...total[timestamp].descriptions, event.description],
            };
        } else {
            total[timestamp] = {
                icon: event.icon,
                type: event.type,
                descriptions: [event.description],
            };
        }

        return total;
    }

    filterEvents(events, selectedTimestamp) {
        const { convertDateToTimezone } = window.ibexa.helpers.timezone;
        const now = convertDateToTimezone(selectedTimestamp);
        const groupedEvents = events.reduce(this.groupEventsByDate.bind(this, now), {});

        return groupedEvents;
    }

    setTime(selectedTimestamp) {
        this.props.onSelectedTimestampChange(selectedTimestamp);
    }

    renderCurrentTimeBtn() {
        const { convertDateToTimezone } = window.ibexa.helpers.timezone;
        const { selectedTimestamp, currentTimestamp } = this.state;

        const selectedDate = convertDateToTimezone(selectedTimestamp);
        const currentDate = convertDateToTimezone(currentTimestamp);
        const isSameDate = currentDate.isSame(selectedDate, 'day');
        const isSameMinute = currentDate.isSame(selectedDate, 'minute');

        if (!isSameDate || isSameMinute) {
            return null;
        }

        const leftPosition = getEventInDayLeftPosition(currentTimestamp);

        return (
            <button
                type="button"
                className="c-pb-slider__current-time-btn"
                style={{ left: `${leftPosition}%` }}
                onClick={() => this.props.onSelectedTimestampChange(currentTimestamp)}
            />
        );
    }

    renderMarker([timestamp, event], index) {
        const key = `${index}-${timestamp}`;

        return <EventMarker key={key} timestamp={timestamp} event={event} onSetTime={this.setTime} inside={EVENT_MARKER_INSIDE_TIMELINE} />;
    }

    renderHours() {
        return (
            <div className="c-pb-slider__hours">
                <div className="c-pb-slider__hour" data-hour="12:00 AM" />
                <div className="c-pb-slider__hour" />
                <div className="c-pb-slider__hour" />
                <div className="c-pb-slider__hour" />
                <div className="c-pb-slider__hour" data-hour="4:00 AM" />
                <div className="c-pb-slider__hour" />
                <div className="c-pb-slider__hour" />
                <div className="c-pb-slider__hour" />
                <div className="c-pb-slider__hour" data-hour="8:00 AM" />
                <div className="c-pb-slider__hour" />
                <div className="c-pb-slider__hour" />
                <div className="c-pb-slider__hour" />
                <div className="c-pb-slider__hour" data-hour="12:00 PM" />
                <div className="c-pb-slider__hour" />
                <div className="c-pb-slider__hour" />
                <div className="c-pb-slider__hour" />
                <div className="c-pb-slider__hour" data-hour="4:00 PM" />
                <div className="c-pb-slider__hour" />
                <div className="c-pb-slider__hour" />
                <div className="c-pb-slider__hour" />
                <div className="c-pb-slider__hour" data-hour="8:00 PM" />
                <div className="c-pb-slider__hour" />
                <div className="c-pb-slider__hour" />
                <div className="c-pb-slider__hour" data-hour="11:59 PM" />
            </div>
        );
    }

    render() {
        const { eventsToDisplay, selectedTimestamp } = this.state;

        return (
            <div className="c-pb-slider" ref={this._refSlider}>
                {this.renderHours()}
                {this.renderCurrentTimeBtn()}
                {Object.entries(eventsToDisplay).map(this.renderMarker)}
                <Thumb sliderRect={this.state.sliderRect} selectedTimestamp={selectedTimestamp} onSetTime={this.setTime} />
            </div>
        );
    }
}

Slider.propTypes = {
    events: PropTypes.array.isRequired,
    onSelectedTimestampChange: PropTypes.func.isRequired,
    selectedTimestamp: PropTypes.number.isRequired,
};

export default Slider;
