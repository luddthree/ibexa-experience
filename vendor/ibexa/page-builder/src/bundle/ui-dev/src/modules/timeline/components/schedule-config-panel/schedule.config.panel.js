import React, { useState } from 'react';
import PropTypes from 'prop-types';
import SimpleDropdown from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/simple-dropdown/simple.dropdown';
import ConfigPanel, { CONFIG_PANEL_TYPE_LEFT, LEFT_PANEL_TYPES } from '../../../common/components/config-panel/config.panel';
import ConfigPanelBody from '../../../common/components/config-panel/config.panel.body';
import ConfigPanelFooter from '../../../common/components/config-panel/config.panel.footer';
import EventsList from '../events-list/events.list';
import Calendar from '../calendar/calendar';
import ScheduleConfigPanelNoEvents from './schedule.config.panel.no.events';

const { Translator } = window;

const VIEW_LIST = 'VIEW_LIST';
const VIEW_CALENDAR = 'VIEW_CALENDAR';
const VIEW_OPTIONS = [
    {
        value: VIEW_LIST,
        iconName: 'view-list',
        label: Translator.trans(/*@Desc("List")*/ 'timeline.schedule.config_panel.view_switcher.list_view', {}, 'ibexa_timeline'),
    },
    {
        value: VIEW_CALENDAR,
        iconName: 'date',
        label: Translator.trans(/*@Desc("Calendar")*/ 'timeline.schedule.config_panel.view_switcher.calendar_view', {}, 'ibexa_timeline'),
    },
];

const ScheduleConfigPanel = ({
    events,
    selectedTimestamp,
    isClosed,
    onSelectedTimestampChange,
    onCancel,
    onShowTimeline,
    onHideTimeline,
    isTimelineVisible,
}) => {
    const [viewMode, setViewMode] = useState(VIEW_LIST);
    const noEvents = events.length === 0;
    const configPanelTitle = Translator.trans(/*@Desc("Schedule")*/ 'timeline.schedule.config_panel.title', {}, 'ibexa_timeline');
    const showTimelineLabel = Translator.trans(
        /*@Desc("Show timeline")*/ 'timeline.schedule.config_panel.show_timeline',
        {},
        'ibexa_timeline',
    );
    const hideTimelineLabel = Translator.trans(
        /*@Desc("Hide timeline")*/ 'timeline.schedule.config_panel.hide_timeline',
        {},
        'ibexa_timeline',
    );
    const viewLabel = Translator.trans(/*@Desc("View")*/ 'timeline.schedule.config_panel.view', {}, 'ibexa_timeline');
    const closeBtnLabel = Translator.trans(/*@Desc("Close")*/ 'timeline.schedule.config_panel.close', {}, 'ibexa_timeline');
    const handleViewModeSwitch = ({ value }) => {
        setViewMode(value);
    };
    const selectedOption = VIEW_OPTIONS.find((option) => option.value === viewMode);
    const renderContentViews = () => (
        <>
            {viewMode === VIEW_LIST && <EventsList events={events} onSelectedTimestampChange={onSelectedTimestampChange} />}
            {viewMode === VIEW_CALENDAR && (
                <div className="c-pb-schedule-config-panel__calendar-wrapper">
                    <Calendar events={events} onSelectedTimestampChange={onSelectedTimestampChange} selectedTimestamp={selectedTimestamp} />
                </div>
            )}
        </>
    );

    return (
        <ConfigPanel
            extraClasses="c-pb-schedule-config-panel"
            type={CONFIG_PANEL_TYPE_LEFT}
            leftPanelType={LEFT_PANEL_TYPES.SCHEDULER}
            showCloseBtn={true}
            onCancel={onCancel}
            title={configPanelTitle}
            isClosed={isClosed}
        >
            <ConfigPanelBody extraClasses="c-pb-schedule-config-panel__body">
                <div className="c-pb-schedule-config-panel__top-panel">
                    <button
                        className="btn ibexa-btn ibexa-btn--small ibexa-btn--tertiary"
                        onClick={isTimelineVisible ? onHideTimeline : onShowTimeline}
                        type="button"
                    >
                        {isTimelineVisible ? hideTimelineLabel : showTimelineLabel}
                    </button>
                    {!noEvents && (
                        <SimpleDropdown
                            options={VIEW_OPTIONS}
                            selectedOption={selectedOption}
                            onOptionClick={handleViewModeSwitch}
                            selectedItemLabel={viewLabel}
                            isSwitcher={true}
                        />
                    )}
                </div>
                <div className="c-pb-schedule-config-panel__content">
                    {noEvents ? <ScheduleConfigPanelNoEvents /> : renderContentViews()}
                </div>
            </ConfigPanelBody>
            <ConfigPanelFooter isClosed={isClosed} extraClasses="c-pb-schedule-config-panel__footer">
                <button type="button" className="btn ibexa-btn ibexa-btn--filled-info" onClick={onCancel}>
                    {closeBtnLabel}
                </button>
            </ConfigPanelFooter>
        </ConfigPanel>
    );
};

ScheduleConfigPanel.propTypes = {
    events: PropTypes.array.isRequired,
    selectedTimestamp: PropTypes.number.isRequired,
    isClosed: PropTypes.bool.isRequired,
    onSelectedTimestampChange: PropTypes.func.isRequired,
    onCancel: PropTypes.func.isRequired,
    onShowTimeline: PropTypes.func.isRequired,
    onHideTimeline: PropTypes.func.isRequired,
    isTimelineVisible: PropTypes.bool.isRequired,
};

export default ScheduleConfigPanel;
