import React, { useContext, useState, useRef } from 'react';

import { EventsConfigContext, CalendarFiltersContext } from '../../../calendar.module';
import { TOGGLE_EVENT_TYPE, SELECT_LANGUAGE } from '../../hooks/useFiltersReducer';
import Dropdown from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/dropdown/dropdown';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

const { Translator, ibexa } = window;

const SidebarFilters = () => {
    const languageListRef = useRef(null);
    const eventsConfig = useContext(EventsConfigContext);
    const [isBodyCollapsed, setIsBodyCollapsed] = useState(false);
    const [calendarFilters, dispatchCalendarFiltersAction] = useContext(CalendarFiltersContext);
    const typesLabel = Translator.trans(/*@Desc("Types")*/ 'calendar.filters.types', {}, 'ibexa_calendar_widget');
    const modifiedLanguageLabel = Translator.trans(
        /*@Desc("Modified language")*/ 'calendar.filters.modified_langauge',
        {},
        'ibexa_calendar_widget',
    );
    const filtersLabel = Translator.trans(/*@Desc("Filters")*/ 'calendar.filters.label', {}, 'ibexa_calendar_widget');
    const filtersClass = createCssClassNames({
        'c-calendar-filters': true,
        'c-calendar-filters--collapsed': isBodyCollapsed,
    });
    const languageChange = (langValue) => {
        dispatchCalendarFiltersAction({ type: SELECT_LANGUAGE, language: langValue });
    };
    const renderTypesFilters = () =>
        Object.entries(eventsConfig).map(([eventType, { label, color }]) => {
            const isChecked = calendarFilters.eventTypes[eventType];
            const toggleEventTypeFilter = () => {
                dispatchCalendarFiltersAction({ type: TOGGLE_EVENT_TYPE, eventType });
            };
            const checkboxClass = createCssClassNames({
                'ibexa-input': true,
                'ibexa-input--checkbox': true,
            });
            const checkboxStyle = {
                borderColor: color,
                backgroundColor: isChecked ? color : '#fff',
            };

            return (
                <li key={eventType} className="c-calendar-filters__type" onClick={toggleEventTypeFilter}>
                    <input className={checkboxClass} style={checkboxStyle} type="checkbox" checked={isChecked} readOnly={true} />
                    <label className="c-calendar-filters__type-label form-check-label">{label}</label>
                </li>
            );
        });
    const renderLanguagesFilter = () => {
        const allLanguagesLabel = Translator.trans(
            /*@Desc("All languages")*/ 'calendar.filters.all_langauges',
            {},
            'ibexa_calendar_widget',
        );
        const languagesMappings = ibexa.adminUiConfig.languages.mappings;
        const languagesOptions = [{ value: '', label: allLanguagesLabel }];

        Object.values(languagesMappings).forEach(({ languageCode, name }) => {
            languagesOptions.push({
                value: languageCode,
                label: name,
            });
        });

        return (
            <Dropdown
                single={true}
                value={calendarFilters.language}
                options={languagesOptions}
                dropdownListRef={languageListRef}
                onChange={languageChange}
            />
        );
    };

    return (
        <div className={filtersClass}>
            <div className="c-calendar-filters__header">
                <div className="c-calendar-filters__title">{filtersLabel}</div>
                <button
                    type="button"
                    className="btn ibexa-btn ibexa-btn--no-text ibexa-btn--small c-calendar-filters__toggler"
                    onClick={() => setIsBodyCollapsed((isBodyCollapsedPrev) => !isBodyCollapsedPrev)}
                >
                    <Icon name="caret-down" extraClasses="ibexa-icon--tiny-small" />
                </button>
            </div>
            <div className="c-calendar-filters__body">
                <div className="c-calendar-filters__filter c-calendar-filters__filter--languages">
                    <div className="c-calendar-filters__filter-label ibexa-label">{modifiedLanguageLabel}</div>
                    {renderLanguagesFilter()}
                    <div ref={languageListRef} className="c-calendar-filters__language-list" />
                </div>
                <div className="c-calendar-filters__filter c-calendar-filters__filter--types">
                    <div className="c-calendar-filters__filter-label ibexa-label">{typesLabel}</div>
                    <ul className="c-calendar-filters__types-list">{renderTypesFilters()}</ul>
                </div>
            </div>
        </div>
    );
};

export default SidebarFilters;
