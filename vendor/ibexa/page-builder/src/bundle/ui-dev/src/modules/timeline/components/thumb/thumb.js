import React, { Component } from 'react';
import PropTypes from 'prop-types';
import getEventInDayLeftPosition, { MINUTES_IN_DAY, MINUTES_IN_HOUR } from '../../../helpers/event.in.day.position';

const KEYCODE_RIGHT_BTN = 3;

class Thumb extends Component {
    constructor(props) {
        super(props);

        this.attachMouseMoveEvent = this.attachMouseMoveEvent.bind(this);
        this.handleMouseMove = this.handleMouseMove.bind(this);
        this.removeListeners = this.removeListeners.bind(this);

        this.onTimeChangeTimeout = null;
        this.backdrop = new window.ibexa.core.Backdrop({ isTransparent: true });

        const leftPosition = getEventInDayLeftPosition(props.selectedTimestamp);

        this.state = {
            leftPosition,
        };
    }

    componentDidUpdate(prevProps) {
        if (prevProps.selectedTimestamp === this.props.selectedTimestamp) {
            return;
        }

        const leftPosition = getEventInDayLeftPosition(this.props.selectedTimestamp);

        this.setState(() => ({ leftPosition }));
    }

    removeListeners() {
        document.body.removeEventListener('mousemove', this.handleMouseMove);
        document.body.removeEventListener('mouseup', this.removeListeners);

        this.removeBackdrop();
    }

    handleMouseMove(event) {
        const { sliderRect } = this.props;
        const isMovedOutside = event.clientX > sliderRect.right || event.clientX < sliderRect.left;

        if (isMovedOutside) {
            return;
        }

        const { convertDateToTimezone } = window.ibexa.helpers.timezone;
        const diff = event.clientX - sliderRect.left;
        const leftPosition = (diff / sliderRect.width) * 100;
        const minutesCount = (MINUTES_IN_DAY * leftPosition) / 100;
        const hour = Math.floor(minutesCount / MINUTES_IN_HOUR);
        const minutes = Math.floor(minutesCount % MINUTES_IN_HOUR);
        const date = convertDateToTimezone(parseInt(this.props.selectedTimestamp, 10));

        date.hour(hour);
        date.minutes(minutes);

        window.clearTimeout(this.onTimeChangeTimeout);
        this.onTimeChangeTimeout = window.setTimeout(this.setTime.bind(this, date.valueOf()), 300);

        this.setState(() => ({ leftPosition }));
    }

    setTime(timestamp) {
        this.props.onSetTime(timestamp);
    }

    attachMouseMoveEvent({ nativeEvent }) {
        const keyCode = nativeEvent.keyCode || nativeEvent.which;

        if (keyCode === KEYCODE_RIGHT_BTN) {
            return;
        }

        this.addBackdrop();

        document.body.addEventListener('mousemove', this.handleMouseMove, false);
        document.body.addEventListener('mouseup', this.removeListeners, false);
    }

    addBackdrop() {
        this.backdrop.show();
    }

    removeBackdrop() {
        this.backdrop.hide();
    }

    render() {
        const { leftPosition } = this.state;
        const style = {
            left: `${leftPosition}%`,
        };

        return (
            <div className="c-pb-thumb" onMouseDown={this.attachMouseMoveEvent} style={style}>
                <img className="c-pb-thumb__bottom-arrow" draggable={false} src="/bundles/ibexapagebuilder/img/timeline-thumb.svg" />
            </div>
        );
    }
}

Thumb.propTypes = {
    sliderRect: PropTypes.object.isRequired,
    selectedTimestamp: PropTypes.number.isRequired,
    onSetTime: PropTypes.func.isRequired,
};

export default Thumb;
