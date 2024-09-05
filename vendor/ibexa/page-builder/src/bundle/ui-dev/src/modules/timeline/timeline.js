import React from 'react';
import PropTypes from 'prop-types';
import Slider from './components/slider/slider';
import CalendarPopup from './components/calendar-popup/calendar.popup';
import EventsListPopup from './components/events-list-popup/events.list.popup';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';

const { Translator } = window;

const Timeline = (props) => {
    const { closeTimeline } = props;
    const closeBtnTitle = Translator.trans(/*@Desc("Close timeline")*/ 'timeline.close_btn', {}, 'ibexa_timeline');

    return (
        <div className="c-pb-timeline">
            <div className="c-pb-timeline__close-btn-wrapper">
                <button
                    className="btn ibexa-btn ibexa-btn--tertiary ibexa-btn--no-text c-pb-timeline__close-btn"
                    onClick={closeTimeline}
                    title={closeBtnTitle}
                    type="button"
                >
                    <Icon name="discard" extraClasses="ibexa-icon ibexa-icon--small" />
                </button>
            </div>
            <div className="c-pb-timeline__timeline-calendar-wrapper">
                <CalendarPopup {...props} />
            </div>
            <div className="c-pb-timeline__list-wrapper">
                <EventsListPopup {...props} />
            </div>
            <div className="c-pb-timeline__slider-wrapper">
                <Slider {...props} />
            </div>
        </div>
    );
};

Timeline.propTypes = {
    selectedTimestamp: PropTypes.number.isRequired,
    events: PropTypes.array.isRequired,
    onSelectedTimestampChange: PropTypes.func.isRequired,
    closeTimeline: PropTypes.func.isRequired,
};

export default Timeline;
