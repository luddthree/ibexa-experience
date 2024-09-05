import React, { useContext, Fragment } from 'react';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

import { SelectedDateContext } from '../../../calendar.module';
import { useEventsLoadMore } from '../../hooks/useEventsLoadMore';
import { getAdminPanelLanguageCode, getAmPmTimeLabel, forceBrowserTimezone } from '../../helpers/date.formatter.helper';
import LoadingSpinner from '../spinner/spinner';
import EventsView from '../events-view/events.view';
import LoadMoreBtn from '../load-more-bnt/load.more.btn';

const { Translator, ibexa } = window;
const { convertDateToTimezone } = ibexa.helpers.timezone;

const DayView = () => {
    const [selectedDate] = useContext(SelectedDateContext);
    const fromDate = selectedDate.clone().startOf('day');
    const toDate = selectedDate.clone().endOf('day');
    const [events, isLoading, remainingEventsCount, loadMore] = useEventsLoadMore(fromDate, toDate, 'hour');
    const browserTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    const selectedDateInBrowserTimezone = new Date(convertDateToTimezone(selectedDate, browserTimezone, true));
    const day = selectedDate.clone().startOf('day');
    const dayHours = [];
    const isInitialLoad = isLoading && events === null;
    const lastHourWithEvent = events === null ? null : Math.max(...Object.keys(events).map((timestamp) => parseInt(timestamp, 10)));
    const isEverythingLoaded = remainingEventsCount === 0;
    const timePMsymbol = Translator.trans(/*@Desc("PM")*/ 'calendar.time_pm_symbol', {}, 'ibexa_calendar_widget');

    for (let i = 0; i < 24; i++) {
        dayHours.push(day.clone().add(i, 'hour'));
    }

    return (
        <div className="c-day-view">
            <div className="c-day-view__header c-day-view__header--day">
                <span className="c-day-view__header-label c-day-view__header-label--date">{selectedDate.format('D')}</span>
                <span className="c-day-view__header-label c-day-view__header-label--day">
                    {selectedDateInBrowserTimezone.toLocaleDateString(getAdminPanelLanguageCode(), { weekday: 'long' })}
                </span>
            </div>
            <div className="c-day-view__day">
                <LoadingSpinner isLoading={isInitialLoad} />
                <div className="c-day-view__agenda">
                    {dayHours.map((hour) => {
                        const hourInBrowserTimezone = forceBrowserTimezone(hour);
                        const hourIn24Format = hourInBrowserTimezone.format('HH');
                        const hourLabel = getAmPmTimeLabel(hourInBrowserTimezone.toDate());
                        const hourEventsData = events === null ? [] : events[hour.unix()];
                        const hourViewClassName = createCssClassNames({
                            'c-day-view__hour': true,
                            'c-day-view__hour--first-pm': hourIn24Format === '12',
                        });
                        const hourSeparatorClassName = createCssClassNames({
                            'c-day-view__hour-separator': true,
                        });
                        const hourLabelClassName = createCssClassNames({
                            'c-day-view__hour-label': true,
                        });
                        let loadMoreComponent = null;

                        if (hour.unix() === lastHourWithEvent && !isEverythingLoaded) {
                            loadMoreComponent = (
                                <div className="c-day-view__load-more-wrapper">
                                    <LoadMoreBtn onLoadMore={loadMore} isLoading={isLoading} />
                                </div>
                            );
                        }

                        return (
                            <Fragment key={hour.unix()}>
                                <div className={hourViewClassName} data-time-pm-symbol={timePMsymbol}>
                                    <div className={hourSeparatorClassName} />
                                    <div className={hourLabelClassName}>{hourLabel}</div>
                                    <EventsView events={hourEventsData} />
                                    {loadMoreComponent}
                                </div>
                            </Fragment>
                        );
                    })}
                </div>
            </div>
        </div>
    );
};

export default DayView;
