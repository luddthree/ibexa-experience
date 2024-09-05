import React, { useEffect, useRef } from 'react';
import PropTypes from 'prop-types';

import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

import { clearInstance } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/object.instances';
import { getAdminUiConfig } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';
import { convertDateToTimezone, getBrowserTimezone } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/timezone.helper';
import { DateTimePicker } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/core/date.time.picker';

const DatePicker = ({ onDateChange, value, isInvalid }) => {
    const adminUiConfig = getAdminUiConfig();
    const pickerContainerRef = useRef();
    const dateTimeComponentRef = useRef();
    const handleDateTimeChange = ([timestamp]) => {
        onDateChange(timestamp);
    };
    const clearDateTime = () => {
        dateTimeComponentRef.current.flatpickrInstance.clear();
    };
    const inputClassName = createCssClassNames({
        'c-pb-date-picker__input ibexa-input ibexa-input--date ibexa-date-time-picker__input': true,
        'flatpickr flatpickr-date-input': true,
        'form-control': true,
        'is-invalid': isInvalid,
    });

    useEffect(() => {
        dateTimeComponentRef.current = new DateTimePicker({
            container: pickerContainerRef.current,
            onChange: handleDateTimeChange,
            flatpickrConfig: {
                enableTime: true,
                position: 'left',
            },
        });

        dateTimeComponentRef.current.init();

        return () => {
            clearInstance(dateTimeComponentRef.current.container);
        };
    }, []);

    useEffect(() => {
        if (!value) {
            dateTimeComponentRef.current?.flatpickrInstance.clear();

            return;
        }

        const userTimezone = adminUiConfig.timezone;
        const date = new Date(value * 1000);
        const dateWithUserTimezone = convertDateToTimezone(date, userTimezone);
        const localTimezone = getBrowserTimezone();
        const convertedDate = convertDateToTimezone(dateWithUserTimezone, localTimezone, true).format();

        dateTimeComponentRef.current?.flatpickrInstance.setDate(convertedDate);
    }, [value]);

    return (
        <div className="wrapper" ref={pickerContainerRef}>
            <div className="c-pb-date-picker__input-wrapper ibexa-date-time-picker ibexa-input-text-wrapper">
                <input type="text" className={inputClassName} readOnly={true} />
                <div className="ibexa-input-text-wrapper__actions">
                    <button
                        type="button"
                        className="btn ibexa-btn ibexa-btn--ghost ibexa-btn--no-text ibexa-input-text-wrapper__action-btn ibexa-input-text-wrapper__action-btn--clear"
                        tabIndex="-1"
                        onClick={clearDateTime}
                    >
                        <Icon name="discard" extraClasses="ibexa-icon--tiny" />
                    </button>
                    <button
                        type="button"
                        className="btn ibexa-btn ibexa-btn--ghost ibexa-btn--no-text ibexa-input-text-wrapper__action-btn ibexa-input-text-wrapper__action-btn--calendar"
                        tabIndex="-1"
                    >
                        <Icon name="date" extraClasses="ibexa-icon--small" />
                    </button>
                </div>
            </div>
        </div>
    );
};

DatePicker.propTypes = {
    onDateChange: PropTypes.func.isRequired,
    value: PropTypes.number,
    isInvalid: PropTypes.bool,
};

DatePicker.defaultProps = {
    value: null,
    isInvalid: false,
};

export default DatePicker;
