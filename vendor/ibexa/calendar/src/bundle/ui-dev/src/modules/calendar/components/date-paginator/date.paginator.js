import React, { useContext, useEffect } from 'react';
import PropTypes from 'prop-types';

import {
    getPreviousWeekDate,
    getNextWeekDate,
    getPreviousMonthDate,
    getNextMonthDate,
    getPreviousDayDate,
    getNextDayDate,
} from '../../helpers/date.helper';

import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import { SelectedDateContext } from '../../../calendar.module';
import { getMonthName, getMonthShortName } from '../../helpers/date.formatter.helper';

const { Translator, ibexa } = window;
const { convertDateToTimezone } = ibexa.helpers.timezone;

export const DATE_PAGINATOR_TYPE = {
    MONTH: 'MONTH',
    WEEK: 'WEEK',
    DAY: 'DAY',
};

const DatePaginator = ({ type }) => {
    const [selectedDate, setSelectedDate] = useContext(SelectedDateContext);
    const today = convertDateToTimezone().startOf('day');
    const isToday = selectedDate.isSame(today, 'day');
    const yearString = selectedDate.format('YYYY');
    const selectPreviousDate = () => {
        if (previousDate) {
            setSelectedDate(previousDate);
        }
    };
    const selectNextDate = () => setSelectedDate(nextDate);
    const todayLabel = Translator.trans(/*@Desc("Today")*/ 'calendar.date_paginator.today', {}, 'ibexa_calendar_widget');
    const backwardTooltipLabel = Translator.trans(/*@Desc("Backward")*/ 'calendar.tooltips.backward', {}, 'ibexa_calendar_widget');
    const forwardTooltipLabel = Translator.trans(/*@Desc("Forward")*/ 'calendar.tooltips.forward', {}, 'ibexa_calendar_widget');
    let monthName = null;
    let daysString = null;
    let previousDate = null;
    let nextDate = null;

    useEffect(() => {
        window.ibexa.helpers.tooltips.parse();
    }, []);

    useEffect(() => {
        window.ibexa.helpers.tooltips.hideAll();
    }, [selectedDate]);

    switch (type) {
        case DATE_PAGINATOR_TYPE.DAY: {
            previousDate = getPreviousDayDate(selectedDate);
            nextDate = getNextDayDate(selectedDate);
            daysString = '';
            monthName = getMonthName(selectedDate);

            break;
        }
        case DATE_PAGINATOR_TYPE.WEEK: {
            const firstDayOfWeek = selectedDate.clone().startOf('week');
            const lastDayOfWeek = selectedDate.clone().endOf('week');
            const isWeekAcrossTwoMonths = firstDayOfWeek.get('month') !== lastDayOfWeek.get('month');

            previousDate = getPreviousWeekDate(selectedDate);
            nextDate = getNextWeekDate(selectedDate);
            daysString = isWeekAcrossTwoMonths ? '' : `${firstDayOfWeek.format('D')}-${lastDayOfWeek.format('D')}`;
            monthName = isWeekAcrossTwoMonths
                ? `${getMonthShortName(firstDayOfWeek)}-${getMonthShortName(lastDayOfWeek)}`
                : getMonthName(firstDayOfWeek);

            break;
        }
        case DATE_PAGINATOR_TYPE.MONTH: {
            previousDate = getPreviousMonthDate(selectedDate);
            nextDate = getNextMonthDate(selectedDate);
            daysString = '';
            monthName = getMonthName(selectedDate);

            break;
        }
        default:
            throw new Error();
    }

    return (
        <div className="c-date-paginator">
            <button
                type="button"
                className="btn ibexa-btn ibexa-btn--secondary c-date-paginator__btn c-date-paginator__btn--today"
                disabled={isToday}
                onClick={() => setSelectedDate(convertDateToTimezone())}
            >
                {todayLabel}
            </button>
            <button
                type="button"
                disabled={previousDate === null}
                className="btn ibexa-btn ibexa-btn--tertiary ibexa-btn--no-text c-date-paginator__btn c-date-paginator__btn--decrease"
                onClick={selectPreviousDate}
                title={backwardTooltipLabel}
            >
                <Icon name="caret-back" extraClasses="ibexa-icon--small" />
            </button>
            <button
                type="button"
                className="btn ibexa-btn ibexa-btn--tertiary ibexa-btn--no-text c-date-paginator__btn c-date-paginator__btn--increase"
                onClick={selectNextDate}
                title={forwardTooltipLabel}
            >
                <Icon name="caret-next" extraClasses="ibexa-icon--small" />
            </button>
            <div className="c-date-paginator__date">
                <span className="c-date-paginator__date-main">{`${monthName} ${daysString} `}</span>
                {yearString}
            </div>
        </div>
    );
};

DatePaginator.propTypes = {
    type: PropTypes.string.isRequired,
};

export default DatePaginator;
