import React, { PureComponent } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';
import Timeline from './timeline/timeline';
import ScheduleToggler from './timeline/components/schedule-toggler/schedule.toggler';
import BackToCurrentTime from './timeline/components/back-to-current-time/back.to.current.time';
import ScheduleConfigPanel from './timeline/components/schedule-config-panel/schedule.config.panel';

const { ibexa } = window;

const CLASS_TIMELINE_VISIBLE = 'ibexa-pb-timeline-visible';
const CLOSE_CONFIG_PANEL_KEY = 'Escape';

class TimelineModule extends PureComponent {
    constructor(props) {
        super(props);

        this.pageBuilderConfigPanelWrapper = document.querySelector('.ibexa-pb-config-panels-wrapper');

        this.changeSelectedTimestamp = this.changeSelectedTimestamp.bind(this);
        this.toggleScheduleConfigPanel = this.toggleScheduleConfigPanel.bind(this);
        this.closeConfigPanelInternal = this.closeConfigPanelInternal.bind(this);
        this.closeTimelineBar = this.closeTimelineBar.bind(this);
        this.openTimelineBar = this.openTimelineBar.bind(this);
        this.closeConfigPanelExternal = this.closeConfigPanelExternal.bind(this);
        this.closeConfigPanelByClickOutside = this.closeConfigPanelByClickOutside.bind(this);
        this.closeConfigPanelByKeyboard = this.closeConfigPanelByKeyboard.bind(this);

        const { selectedTimestamp } = props;
        const { convertDateToTimezone } = window.ibexa.helpers.timezone;
        const now = convertDateToTimezone(new Date()).valueOf();
        const isFutureTimeSelected = selectedTimestamp > now;

        this.state = {
            selectedTimestamp: props.selectedTimestamp,
            isTimelineVisible: false,
            isConfigPanelVisible: false,
            isGoBackToCurrentTimeVisible: isFutureTimeSelected,
            canShowAgainGoBackToCurrentTime: !isFutureTimeSelected,
        };
    }

    componentDidUpdate() {
        const { isTimelineVisible } = this.state;

        document.body.classList.toggle(CLASS_TIMELINE_VISIBLE, isTimelineVisible);
        window.ibexa.helpers.tooltips.hideAll();

        if (this.state.isConfigPanelVisible) {
            document.body.addEventListener('click', this.closeConfigPanelByClickOutside, false);
            document.body.addEventListener('keyup', this.closeConfigPanelByKeyboard, false);
        } else {
            document.body.removeEventListener('click', this.closeConfigPanelByClickOutside);
            document.body.removeEventListener('keyup', this.closeConfigPanelByKeyboard);
        }
    }

    changeSelectedTimestamp(selectedTimestamp) {
        const { onTimelineEventSelect, events } = this.props;
        const { convertDateToTimezone } = window.ibexa.helpers.timezone;
        const oldTimestamp = this.state.selectedTimestamp;
        const now = convertDateToTimezone(new Date()).valueOf();
        const isFutureTimeSelected = selectedTimestamp > now;

        this.setState(
            ({
                isGoBackToCurrentTimeVisible: isGoBackToCurrentTimeVisiblePrev,
                canShowAgainGoBackToCurrentTime: canShowAgainGoBackToCurrentTimePrev,
            }) => ({
                selectedTimestamp,
                isGoBackToCurrentTimeVisible:
                    (isGoBackToCurrentTimeVisiblePrev && isFutureTimeSelected) ||
                    (isFutureTimeSelected && canShowAgainGoBackToCurrentTimePrev),
                canShowAgainGoBackToCurrentTime: !isFutureTimeSelected,
            }),
            () => {
                onTimelineEventSelect(oldTimestamp, selectedTimestamp, events);
            },
        );
    }

    toggleScheduleConfigPanel() {
        this.setState(({ isConfigPanelVisible: isConfigPanelVisiblePrev }) => {
            if (isConfigPanelVisiblePrev) {
                document.dispatchEvent(new CustomEvent('ibexa-pb-config-panel-close-itself'));

                return {
                    isConfigPanelVisible: false,
                };
            }
            const wasConfigPanelOpened = document.dispatchEvent(
                new CustomEvent('ibexa-pb-config-panel-open', {
                    cancelable: true,
                    detail: { settings: { onClose: this.closeConfigPanelExternal } },
                }),
            );

            return {
                isConfigPanelVisible: wasConfigPanelOpened,
            };
        });
    }

    closeConfigPanelByClickOutside(event) {
        const { target } = event;

        if (target.classList.contains('ibexa-backdrop')) {
            this.closeConfigPanelInternal();
        }
    }

    closeConfigPanelByKeyboard(event) {
        if (event.key === CLOSE_CONFIG_PANEL_KEY) {
            this.closeConfigPanelInternal();
        }
    }

    closeConfigPanelInternal() {
        this.setState(() => ({
            isConfigPanelVisible: !document.dispatchEvent(new CustomEvent('ibexa-pb-config-panel-close-itself')),
        }));
    }

    closeConfigPanelExternal() {
        this.setState(() => ({ isConfigPanelVisible: false }));

        return true;
    }

    openTimelineBar() {
        this.setState(() => ({
            isTimelineVisible: true,
        }));
    }

    closeTimelineBar() {
        this.setState(() => ({
            isTimelineVisible: false,
        }));
    }

    renderTimelineBtn() {
        const { isConfigPanelVisible } = this.state;
        const timelineTogglerContainer = document.querySelector('.ibexa-pb-action-bar__timeline-toggler');

        return ReactDOM.createPortal(
            <ScheduleToggler isVisible={isConfigPanelVisible} toggle={this.toggleScheduleConfigPanel} />,
            timelineTogglerContainer,
        );
    }

    renderBackToCurrentTime() {
        const alertsSideTray = document.querySelector('.ibexa-pb-back-to-current-time-wrapper');

        return ReactDOM.createPortal(<BackToCurrentTime onSelectedTimestampChange={this.changeSelectedTimestamp} />, alertsSideTray);
    }

    render() {
        const { events } = this.props;
        const { selectedTimestamp, isConfigPanelVisible, isGoBackToCurrentTimeVisible, isTimelineVisible } = this.state;

        return (
            <div className="m-timeline">
                {this.renderTimelineBtn()}
                {isGoBackToCurrentTimeVisible && this.renderBackToCurrentTime()}
                <Timeline
                    events={events}
                    selectedTimestamp={selectedTimestamp}
                    onSelectedTimestampChange={this.changeSelectedTimestamp}
                    closeTimeline={this.closeTimelineBar}
                />
                {ReactDOM.createPortal(
                    <ScheduleConfigPanel
                        events={events}
                        selectedTimestamp={selectedTimestamp}
                        isClosed={!isConfigPanelVisible}
                        isTimelineVisible={isTimelineVisible}
                        onSelectedTimestampChange={this.changeSelectedTimestamp}
                        onCancel={this.closeConfigPanelInternal}
                        onShowTimeline={this.openTimelineBar}
                        onHideTimeline={this.closeTimelineBar}
                    />,
                    this.pageBuilderConfigPanelWrapper,
                )}
            </div>
        );
    }
}

TimelineModule.propTypes = {
    events: PropTypes.array.isRequired,
    onTimelineEventSelect: PropTypes.func.isRequired,
    selectedTimestamp: PropTypes.number.isRequired,
};

export default TimelineModule;

ibexa.addConfig('modules.Timeline', TimelineModule);
