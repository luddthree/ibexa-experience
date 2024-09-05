import React from 'react';

const { Translator } = window;

const ScheduleConfigPanelNoEvents = () => {
    const title = Translator.trans(/*@Desc("No events")*/ 'timeline.schedule.config_panel.no_events.title', {}, 'ibexa_timeline');
    const subtitle = Translator.trans(
        /*@Desc("Your events will show up here")*/ 'timeline.schedule.config_panel.no_events.subtitle',
        {},
        'ibexa_timeline',
    );

    return (
        <div className="c-pb-schedule-config-panel-no-events">
            <img className="c-pb-schedule-config-panel-no-events__image" src="/bundles/ibexapagebuilder/img/no-events.svg" />
            <div className="c-pb-schedule-config-panel-no-events__text">
                <strong className="c-pb-schedule-config-panel-no-events__title">{title}</strong>
                <span className="c-pb-schedule-config-panel-no-events__subtitle">{subtitle}</span>
            </div>
        </div>
    );
};

ScheduleConfigPanelNoEvents.propTypes = {};

export default ScheduleConfigPanelNoEvents;
