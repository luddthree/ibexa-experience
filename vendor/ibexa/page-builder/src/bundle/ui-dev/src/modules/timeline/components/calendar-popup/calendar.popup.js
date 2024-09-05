import React from 'react';
import PropTypes from 'prop-types';
import Calendar from '../calendar/calendar';
import TimelinePopup, { TimelinePopupContext } from '../timeline-popup/timeline.popup';
import TimelinePopupContainer from '../timeline-popup/timeline.popup.container';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';

const { Translator, moment, ibexa } = window;

const CalendarPopup = (props) => {
    const { selectedTimestamp } = props;
    const { convertDateToTimezone } = window.ibexa.helpers.timezone;
    const calendarBtnTitle = Translator.trans(/*@Desc("Timeline calendar")*/ 'timeline.calendar', {}, 'ibexa_timeline');

    const selectedDate = convertDateToTimezone(selectedTimestamp, ibexa.adminUiConfig.timezone);
    const formattedSelectedTime = moment(selectedDate).formatICU('H:mm');
    const formattedSelectedDate = moment(selectedDate).formatICU('MMMM dd,yyyy');

    return (
        <TimelinePopup extraClasses="c-pb-calendar-popup">
            <TimelinePopupContext.Consumer>
                {({ togglePopup }) => (
                    <button
                        className="btn ibexa-btn ibexa-btn--ghost c-pb-calendar-popup__toggler"
                        onClick={togglePopup}
                        title={calendarBtnTitle}
                        type="button"
                    >
                        <Icon name="schedule" extraClasses="ibexa-icon--small" />
                        <span className="c-pb-calendar-popup__toggler-label-time">{formattedSelectedTime}</span>
                        <span className="c-pb-calendar-popup__toggler-label-date">{formattedSelectedDate}</span>
                    </button>
                )}
            </TimelinePopupContext.Consumer>
            <TimelinePopupContainer scrollWrapperExtraClasses="c-pb-calendar-popup__scroll-wrapper">
                <Calendar {...props} />
            </TimelinePopupContainer>
        </TimelinePopup>
    );
};

CalendarPopup.propTypes = {
    selectedTimestamp: PropTypes.number.isRequired,
};

export default CalendarPopup;
