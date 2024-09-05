import React from 'react';
import PropTypes from 'prop-types';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';

const CalendarPaginator = ({ displayedDate, onMonthChange }) => {
    const { convertDateToTimezone, formatFullDateTime } = window.ibexa.helpers.timezone;
    const paginatorBtnClassName = 'btn ibexa-btn ibexa-btn--ghost ibexa-btn--small ibexa-btn--no-text c-pb-calendar-paginator__caret';
    const date = convertDateToTimezone(displayedDate);
    const setPreviousMonth = () => {
        date.month(date.month() - 1);

        onMonthChange(date.valueOf());
    };
    const setNextMonth = () => {
        date.month(date.month() + 1);

        onMonthChange(date.valueOf());
    };
    const getFormattedDate = () => formatFullDateTime(date, null, 'MMMM YYYY');

    return (
        <div className="c-pb-calendar-paginator">
            <button className={paginatorBtnClassName} onClick={setPreviousMonth} type="button">
                <Icon name="caret-back" extraClasses="ibexa-icon--tiny-small" />
            </button>
            <div>{getFormattedDate()}</div>
            <button className={paginatorBtnClassName} onClick={setNextMonth} type="button">
                <Icon name="caret-next" extraClasses="ibexa-icon--tiny-small" />
            </button>
        </div>
    );
};

CalendarPaginator.propTypes = {
    displayedDate: PropTypes.number.isRequired,
    onMonthChange: PropTypes.func.isRequired,
};

export default CalendarPaginator;
