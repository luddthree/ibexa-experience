import React, { Component, createRef } from 'react';
import PropTypes from 'prop-types';
import EventTooltip from '../event-tooltip/event.tooltip';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';
import getEventInDayLeftPosition from '../../../helpers/event.in.day.position';

export const EVENT_MARKER_INSIDE_CALENDAR = 'EVENT_MARKER_INSIDE_CALENDAR';
export const EVENT_MARKER_INSIDE_TIMELINE = 'EVENT_MARKER_INSIDE_TIMELINE';

class EventMarker extends Component {
    constructor(props) {
        super(props);

        this.handleClick = this.handleClick.bind(this);
        this.renderDot = this.renderDot.bind(this);
        this.showTooltip = this.showTooltip.bind(this);
        this.hideTooltip = this.hideTooltip.bind(this);

        this._refMarker = createRef();
        this._refTooltip = createRef();

        this.hideTooltipTimeout = null;

        this.state = {
            isTooltipVisible: false,
        };
    }

    handleClick() {
        const { onSetTime, timestamp } = this.props;

        onSetTime(parseInt(timestamp, 10));
    }

    showTooltip() {
        this.toggleTooltipVisibility(true);

        window.document.body.addEventListener('mousemove', this.hideTooltip, false);
    }

    hideTooltip(event) {
        const eventMarker = event.target.closest('.c-pb-event-marker');
        const eventTooltip = event.target.closest('.c-pb-event-tooltip');
        const isCurrentMarker = eventMarker === this._refMarker.current;
        const isCurrentMarkerTooltip = eventTooltip === this._refTooltip.current;

        window.clearTimeout(this.hideTooltipTimeout);

        if (isCurrentMarker || isCurrentMarkerTooltip) {
            return;
        }

        this.hideTooltipTimeout = window.setTimeout(() => {
            this.toggleTooltipVisibility(false);

            window.document.body.removeEventListener('mousemove', this.hideTooltip, false);
        }, 200);
    }

    toggleTooltipVisibility(isTooltipVisible) {
        this.setState(() => ({ isTooltipVisible }));
    }

    renderDot() {
        const { event } = this.props;
        const label = event.descriptions.length;

        return <span className="c-pb-event-marker__dot">{label}</span>;
    }

    renderEventTooltip() {
        const { event } = this.props;
        const { isTooltipVisible } = this.state;

        if (!isTooltipVisible) {
            return null;
        }

        return <EventTooltip descriptions={event.descriptions} markerRef={this._refMarker} ref={this._refTooltip} />;
    }

    render() {
        const { inside, event, timestamp } = this.props;
        const isInsideCalendar = inside === EVENT_MARKER_INSIDE_CALENDAR;
        const isInsideTimeline = inside === EVENT_MARKER_INSIDE_TIMELINE;
        const className = createCssClassNames({
            'c-pb-event-marker': true,
            'c-pb-event-marker--in-calendar': isInsideCalendar,
            'c-pb-event-marker--in-timeline': isInsideTimeline,
        });
        const attrs = {
            className,
            ref: this._refMarker,
            onMouseEnter: this.showTooltip,
        };

        if (event.type) {
            attrs['data-event-type'] = event.type;
        }

        if (isInsideTimeline) {
            const leftPosition = getEventInDayLeftPosition(timestamp);

            attrs.onClick = this.handleClick;
            attrs.style = {
                left: `${leftPosition}%`,
            };
        }

        return (
            <div {...attrs}>
                <button className="c-pb-event-marker__btn" type="button">
                    {this.renderDot()}
                </button>
                {this.renderEventTooltip()}
            </div>
        );
    }
}

EventMarker.propTypes = {
    event: PropTypes.object.isRequired,
    onSetTime: PropTypes.func.isRequired,
    timestamp: PropTypes.number.isRequired,
    inside: PropTypes.string,
};

EventMarker.defaultProps = {
    inside: '',
};

export default EventMarker;
