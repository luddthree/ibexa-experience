import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import CalendarPaginator from '../calendar-paginator/calendar.paginator';
import MonthView from '../month-view/month.view';

class Calendar extends PureComponent {
    constructor(props) {
        super(props);

        this.changeMonth = this.changeMonth.bind(this);
        this.changeSelectedTimestamp = this.changeSelectedTimestamp.bind(this);

        this.state = {
            isCalendarExpanded: false,
            selectedDate: props.selectedTimestamp,
            displayedDate: props.selectedTimestamp,
        };
    }

    componentDidMount() {
        window.ibexa.helpers.tooltips.parse();
    }

    componentDidUpdate(prevProps) {
        if (prevProps.selectedTimestamp === this.props.selectedTimestamp) {
            return;
        }

        this.setState(() => ({ selectedDate: this.props.selectedTimestamp, displayedDate: this.props.selectedTimestamp }));
    }

    changeMonth(displayedDate) {
        this.setState(() => ({ displayedDate }));
    }

    changeSelectedTimestamp(selectedTimestamp) {
        this.props.onSelectedTimestampChange(selectedTimestamp);
    }

    render() {
        const { selectedDate, displayedDate } = this.state;

        return (
            <div className="c-pb-calendar">
                <CalendarPaginator displayedDate={displayedDate} onMonthChange={this.changeMonth} />
                <MonthView
                    {...this.props}
                    displayedDate={displayedDate}
                    selectedDate={selectedDate}
                    onChangeSelectedTimestamp={this.changeSelectedTimestamp}
                />
            </div>
        );
    }
}

Calendar.propTypes = {
    events: PropTypes.array.isRequired,
    onSelectedTimestampChange: PropTypes.func.isRequired,
    selectedTimestamp: PropTypes.number.isRequired,
};

export default Calendar;
