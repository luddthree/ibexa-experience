import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import DayView from '../day-view/day.view';

const DAYS = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

class MonthView extends PureComponent {
    constructor(props) {
        super(props);

        const eventsByDay = this.filterEvents(props.events);

        this.state = {
            eventsByDay,
        };
    }

    componentDidUpdate({ events: prevEvents }) {
        const { events } = this.props;
        const isSameNumberOfEvents = prevEvents.length === events.length;
        const areSameEvents =
            isSameNumberOfEvents && events.every((event, index) => prevEvents[index] && prevEvents[index].date === event.date);

        if (areSameEvents) {
            return;
        }

        const eventsByDay = this.filterEvents(events);

        this.setState(() => ({ eventsByDay }));
    }

    groupEventsByDay(total, event) {
        const { convertDateToTimezone } = window.ibexa.helpers.timezone;
        const eventDate = convertDateToTimezone(event.date * 1000);
        const timestamp = convertDateToTimezone(new Date(eventDate.year(), eventDate.month(), eventDate.date(), 12))
            .hour(12)
            .valueOf();

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

    filterEvents(events) {
        const groupedEvents = events.reduce(this.groupEventsByDay, {});

        return groupedEvents;
    }

    getAdminPanelLanguageCode() {
        return document.querySelector('html').getAttribute('lang').replace(new RegExp('_', 'g'), '-');
    }

    renderHeaders() {
        return DAYS.map((day) => (
            <div className="c-pb-month-view__header" key={day}>
                {day}
            </div>
        ));
    }

    renderDaysBeforeMonth() {
        const { convertDateToTimezone } = window.ibexa.helpers.timezone;
        const displayedDate = convertDateToTimezone(this.props.displayedDate);
        const firstDayOfMonth = new Date(displayedDate.year(), displayedDate.month(), 1);
        const day = firstDayOfMonth.getDay();
        const days = [];
        const now = convertDateToTimezone(new Date());

        for (let i = 0; i !== day; i++) {
            days.push('');
        }

        return days.map((item, index) => {
            const date = convertDateToTimezone(new Date(displayedDate.year(), displayedDate.month(), index - day + 1, 12)).hour(12);
            const dayOfMonth = date.date();
            const timestamp = date.valueOf();
            const isNotSelectable = now.isAfter(date, 'day');
            const label = dayOfMonth;

            return (
                <DayView
                    key={timestamp}
                    timestamp={timestamp}
                    day={label}
                    event={this.state.eventsByDay[timestamp]}
                    isSelectable={!isNotSelectable}
                    onChangeSelectedTimestamp={this.props.onChangeSelectedTimestamp}
                />
            );
        });
    }

    renderDaysOfMonth() {
        const { convertDateToTimezone } = window.ibexa.helpers.timezone;
        const displayedDate = convertDateToTimezone(this.props.displayedDate);
        const daysInMonth = displayedDate.daysInMonth();
        const days = [];
        const selectedDate = convertDateToTimezone(this.props.selectedDate);
        const now = convertDateToTimezone(new Date());

        for (let i = 0; i !== daysInMonth; i++) {
            days.push('');
        }

        return days.map((item, index) => {
            const date = convertDateToTimezone(new Date(displayedDate.year(), displayedDate.month(), index + 1, 12)).hour(12);
            const dayOfMonth = date.date();
            const isSelected = selectedDate.isSame(date, 'day');
            const isNotSelectable = now.isAfter(date, 'day');
            const isToday = now.isSame(date, 'day');
            const dailyTimestamp = date.valueOf();
            const label = dayOfMonth === 1 ? date.locale(this.getAdminPanelLanguageCode()).format('D MMM') : dayOfMonth;

            if (isToday) {
                date.hour(now.hour());
                date.minute(now.minute());
            }

            const timestamp = date.valueOf();

            return (
                <DayView
                    key={timestamp}
                    timestamp={timestamp}
                    day={label}
                    event={this.state.eventsByDay[dailyTimestamp]}
                    isSelected={isSelected}
                    isSelectable={!isNotSelectable}
                    onChangeSelectedTimestamp={this.props.onChangeSelectedTimestamp}
                />
            );
        });
    }

    renderDaysAfterMonth() {
        const { convertDateToTimezone } = window.ibexa.helpers.timezone;
        const displayedDate = convertDateToTimezone(this.props.displayedDate);
        const daysInMonth = displayedDate.daysInMonth();
        const lastDayOfMonth = new Date(displayedDate.year(), displayedDate.month(), daysInMonth);
        const day = 6 - lastDayOfMonth.getDay();
        const days = [];
        const now = convertDateToTimezone(new Date());

        for (let i = 0; i !== day; i++) {
            days.push('');
        }

        return days.map((item, index) => {
            const date = convertDateToTimezone(new Date(displayedDate.year(), displayedDate.month() + 1, index + 1, 12)).hour(12);
            const dayOfMonth = date.date();
            const timestamp = date.valueOf();
            const isNotSelectable = now.isAfter(date, 'day');
            const label = dayOfMonth === 1 ? date.locale(this.getAdminPanelLanguageCode()).format('D MMM') : dayOfMonth;

            return (
                <DayView
                    key={timestamp}
                    timestamp={timestamp}
                    day={label}
                    event={this.state.eventsByDay[timestamp]}
                    isSelectable={!isNotSelectable}
                    onChangeSelectedTimestamp={this.props.onChangeSelectedTimestamp}
                />
            );
        });
    }

    render() {
        return (
            <div className="c-pb-month-view">
                <div className="c-pb-month-view__headers">{this.renderHeaders()}</div>
                <div className="c-pb-month-view__days">
                    {this.renderDaysBeforeMonth()}
                    {this.renderDaysOfMonth()}
                    {this.renderDaysAfterMonth()}
                </div>
            </div>
        );
    }
}

MonthView.propTypes = {
    events: PropTypes.array.isRequired,
    onChangeSelectedTimestamp: PropTypes.func.isRequired,
    displayedDate: PropTypes.oneOfType([PropTypes.instanceOf(Date).isRequired, PropTypes.number.isRequired]).isRequired,
    selectedDate: PropTypes.oneOfType([PropTypes.instanceOf(Date).isRequired, PropTypes.number.isRequired]).isRequired,
};

export default MonthView;
