import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';

const { Translator } = window;

class BackToCurrentTime extends PureComponent {
    constructor(props) {
        super(props);

        this.setCurrentTime = this.setCurrentTime.bind(this);
    }

    setCurrentTime() {
        const { convertDateToTimezone } = window.ibexa.helpers.timezone;
        const { onSelectedTimestampChange } = this.props;

        onSelectedTimestampChange(convertDateToTimezone(new Date()).valueOf());
    }

    render() {
        const warningText = Translator.trans(
            /*@Desc("Previewing in the future")*/ 'back_to_current_time.warning.text',
            {},
            'ibexa_timeline',
        );
        const btnText = Translator.trans(/*@Desc("Back to current time")*/ 'back_to_current_time.info.text', {}, 'ibexa_timeline');

        return (
            <div className="c-pb-back-to-current-time">
                <div className="c-pb-back-to-current-time__title">{warningText}</div>
                <button className="btn btn-link c-pb-back-to-current-time__btn" onClick={this.setCurrentTime} type="button">
                    {btnText}
                </button>
            </div>
        );
    }
}

BackToCurrentTime.propTypes = {
    onSelectedTimestampChange: PropTypes.func.isRequired,
};

export default BackToCurrentTime;
