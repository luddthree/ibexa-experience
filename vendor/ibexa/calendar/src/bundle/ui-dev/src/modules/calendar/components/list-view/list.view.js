import React, { useContext, Fragment } from 'react';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

import { SelectedDateContext, EventsSelectionContext, EventsConfigContext } from '../../../calendar.module';
import { getAmPmTimeLabel, getAdminPanelLanguageCode, forceBrowserTimezone } from '../../helpers/date.formatter.helper';
import { useEventsLoadMore } from '../../hooks/useEventsLoadMore';
import { DATE_PAGINATOR_TYPE } from '../date-paginator/date.paginator';
import LoadingSpinner from '../spinner/spinner';
import TopBar from '../top-bar/top.bar';
import LoadMoreBtn from '../load-more-bnt/load.more.btn';

const { Translator, ibexa } = window;
const { convertDateToTimezone } = ibexa.helpers.timezone;
const { types } = ibexa.calendar.config;

const ListView = () => {
    const [selectedDate] = useContext(SelectedDateContext);
    const [eventsSelection, dispatchSelectEventAction] = useContext(EventsSelectionContext);
    const eventsConfig = useContext(EventsConfigContext);
    const today = convertDateToTimezone().startOf('day');
    const monthStartDate = selectedDate.clone().startOf('month');
    const fromDate = monthStartDate.isBefore(today, 'day') ? today : monthStartDate;
    const toDate = selectedDate.clone().endOf('month');
    const [events, isLoading, remainingEventsCount, loadMore] = useEventsLoadMore(fromDate, toDate, 'day');
    const isInitialLoad = isLoading && events === null;
    const listClassName = createCssClassNames({
        'c-list-view__list': true,
        'c-list-view__list--loading': isInitialLoad,
    });
    const renderEventDetails = (event) => {
        return (
            <ul className="list-unstyled mb-0">
                {Object.entries(event.attributes).map(([attributeKey, { label, value }]) => (
                    <li key={attributeKey}>{`${label}: ${value}`}</li>
                ))}
            </ul>
        );
    };
    const renderEvent = (event) => {
        const eventConfig = eventsConfig[event.type];
        const isSelected = !!eventsSelection.selectedEvents[event.id];
        const handleChange = () => {
            if (isSelected) {
                dispatchSelectEventAction({ type: 'DESELECT_EVENT', eventId: event.id });
            } else {
                dispatchSelectEventAction({ type: 'SELECT_EVENT', event });
            }
        };
        let input = null;

        if (eventConfig.isSelectable) {
            input = <input type="checkbox" className="ibexa-input ibexa-input--checkbox" checked={isSelected} onChange={handleChange} />;
        }

        const eventDate = convertDateToTimezone(parseInt(event.dateTime, 10) * 1000);

        return (
            <div className="c-list-view__event">
                <div className="c-list-view__event-actions-col">{input}</div>
                <div className="c-list-view__event-data-col">
                    <div className="c-list-view__event-name">{event.name}</div>
                    <div className="c-list-view__event-type">{types[event.type].label}</div>
                    <div className="c-list-view__event-details">{renderEventDetails(event)}</div>
                    <div className="c-list-view__event-date">{getAmPmTimeLabel(forceBrowserTimezone(eventDate).toDate())}</div>
                </div>
            </div>
        );
    };
    const renderDaySeparator = (dayTimestamp) => {
        const languageCode = getAdminPanelLanguageCode();
        const separatorDate = convertDateToTimezone(parseInt(dayTimestamp, 10) * 1000);
        const separatorDateFormated = separatorDate
            .toDate()
            .toLocaleDateString(languageCode, { day: 'numeric', year: 'numeric', month: 'long' });

        return <div className="c-list-view__day-separator">{separatorDateFormated}</div>;
    };
    const renderNoEventsToShowInfo = () => {
        if (isLoading || Object.entries(events).length) {
            return null;
        }

        const noEventsMessage = Translator.trans(
            /*@Desc("No events to show.")*/ 'calendar.list_view.no_events',
            {},
            'ibexa_calendar_widget',
        );

        return <div className="c-list-view__no-events">{noEventsMessage}</div>;
    };
    const renderAllEvents = () => {
        if (!events) {
            return null;
        }

        return Object.entries(events).map(([dayTimestamp, dayEvents]) => (
            <Fragment key={dayTimestamp}>
                {renderDaySeparator(dayTimestamp)}
                {dayEvents.map(renderEvent)}
            </Fragment>
        ));
    };
    const renderLoadMoreButton = () => {
        const canLoadMore = remainingEventsCount > 0;

        if (!canLoadMore) {
            return null;
        }

        return (
            <div className="c-list-view__load-more-wrapper">
                <LoadMoreBtn onLoadMore={loadMore} isLoading={isLoading} />
            </div>
        );
    };

    return (
        <div className="c-list-view">
            <TopBar isCalendarViewSwitcherVisible={false} paginatorType={DATE_PAGINATOR_TYPE.MONTH} />
            <div className={listClassName}>
                <LoadingSpinner isLoading={isInitialLoad} />
                {renderAllEvents()}
                {renderNoEventsToShowInfo()}
            </div>
            {renderLoadMoreButton()}
        </div>
    );
};

export default ListView;
