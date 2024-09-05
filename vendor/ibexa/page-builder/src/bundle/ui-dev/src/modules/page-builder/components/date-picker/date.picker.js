import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';

import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';

const { Translator, ibexa } = window;
const { convertDateToTimezone } = ibexa.helpers.timezone;

class DatePicker extends PureComponent {
    constructor(props) {
        super(props);

        this.setPickerRef = this.setPickerRef.bind(this);
        this.updateAirtime = this.updateAirtime.bind(this);
        this.clearAirtime = this.clearAirtime.bind(this);
    }

    componentDidMount() {
        const { airtime } = this.props;
        const userTimezoneCurrentTime = convertDateToTimezone(new Date());
        const selectedDateWithUserTimezone = airtime ? convertDateToTimezone(airtime * 1000) : userTimezoneCurrentTime;
        const browserTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        const minDate = new Date(this.roundMinutes(convertDateToTimezone(userTimezoneCurrentTime, browserTimezone, true)));
        let defaultDate = new Date(this.roundMinutes(convertDateToTimezone(selectedDateWithUserTimezone, browserTimezone, true)));

        if (defaultDate < minDate) {
            defaultDate = minDate;
        }

        minDate.setSeconds(0);

        this.dateTimePickerWidget = new ibexa.core.DateTimePicker({
            container: this._refPicker,
            onChange: this.updateAirtime,
            flatpickrConfig: {
                enableTime: true,
                minDate,
                defaultDate,
                inline: true,
            },
        });

        this.dateTimePickerWidget.init();
    }

    roundMinutes(date) {
        const roundDiff = date.minute() % 5;

        if (roundDiff) {
            return date.add(5 - roundDiff, 'minutes');
        }

        return date;
    }

    updateAirtime([timestamp]) {
        this.props.onDateChange(timestamp);
    }

    clearAirtime() {
        this.dateTimePickerWidget.clear();
    }

    setPickerRef(ref) {
        this._refPicker = ref;
    }

    render() {
        const label = Translator.trans(/*@Desc("Date and time")*/ 'date_picker.label', {}, 'ibexa_page_builder');

        return (
            <div className="c-pb-date-picker" ref={this.setPickerRef}>
                <span className="c-pb-date-picker__label ibexa-label">{label}</span>
                <div className="c-pb-date-picker__input-wrapper ibexa-date-time-picker ibexa-input-text-wrapper">
                    <input
                        type="text"
                        className="c-pb-date-picker__input ibexa-input ibexa-input--date form-control ibexa-date-time-picker__input flatpickr flatpickr-date-input"
                        readOnly={true}
                    />
                    <div className="ibexa-input-text-wrapper__actions">
                        <button
                            type="button"
                            className="btn ibexa-btn ibexa-btn--ghost ibexa-btn--no-text ibexa-input-text-wrapper__action-btn ibexa-input-text-wrapper__action-btn--clear"
                            tabIndex="-1"
                            onClick={this.clearAirtime}
                        >
                            <Icon name="discard" extraClasses="ibexa-icon--tiny" />
                        </button>
                        <button
                            type="button"
                            className="btn ibexa-btn ibexa-btn--ghost ibexa-btn--no-text ibexa-input-text-wrapper__action-btn ibexa-input-text-wrapper__action-btn--calendar"
                            tabIndex="-1"
                        >
                            <Icon name="date" extraClasses="ibexa-icon--small" />
                        </button>
                    </div>
                </div>
            </div>
        );
    }
}

DatePicker.propTypes = {
    onDateChange: PropTypes.func.isRequired,
    airtime: PropTypes.number.isRequired,
};

export default DatePicker;
