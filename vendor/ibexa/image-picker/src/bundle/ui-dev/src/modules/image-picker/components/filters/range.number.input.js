import React, { useEffect } from 'react';
import PropTypes from 'prop-types';

import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

const RangeNumberInput = ({ valueLeft, valueRight, min, step, labelAfter, onValueLeftChange, onValueRightChange, onErrorChange }) => {
    const valueLeftParsed = parseInt(valueLeft, 10);
    const valueRightParsed = parseInt(valueRight, 10);
    const isInvalid = valueLeftParsed > valueRightParsed;
    const handleValueChange = (event, onValueChange) => {
        const newValue = parseInt(event.currentTarget.value, 10);

        if (min !== null && newValue < min) {
            event.currentTarget.value = min;
        }

        onValueChange(event);
    };
    const inputClassName = createCssClassNames({
        'ibexa-input ibexa-input--text': true,
        'form-control': true,
        'is-invalid': isInvalid,
    });

    useEffect(() => {
        onErrorChange(isInvalid);
    }, [isInvalid]);

    return (
        <div className="ibexa-double-input-range__inputs-wrapper">
            <div className="ibexa-input-text-wrapper ibexa-input-text-wrapper--type-number">
                <input
                    type="number"
                    className={inputClassName}
                    value={valueLeft ?? ''}
                    onChange={(event) => handleValueChange(event, onValueLeftChange)}
                    min={min}
                    step={step}
                />
            </div>
            <div className="ibexa-double-input-range__separator" />
            <div className="ibexa-input-text-wrapper ibexa-input-text-wrapper--type-number">
                <input
                    type="number"
                    className={inputClassName}
                    value={valueRight ?? ''}
                    onChange={(event) => handleValueChange(event, onValueRightChange)}
                    min={min}
                    step={step}
                />
            </div>
            {labelAfter && <div className="ibexa-double-input-range__label-after-wrapper">{labelAfter}</div>}
        </div>
    );
};

RangeNumberInput.propTypes = {
    valueLeft: PropTypes.number,
    valueRight: PropTypes.number,
    min: PropTypes.number,
    step: PropTypes.number.isRequired,
    labelAfter: PropTypes.string,
    onValueLeftChange: PropTypes.func.isRequired,
    onValueRightChange: PropTypes.func.isRequired,
    onErrorChange: PropTypes.func,
};

RangeNumberInput.defaultProps = {
    valueLeft: null,
    valueRight: null,
    labelAfter: null,
    min: null,
    onErrorChange: () => {},
};

export default RangeNumberInput;
