import React, { useContext } from 'react';

import FiltersGroup from './filters.group';
import RangeNumberInput from './range.number.input';
import { FiltersContext } from '../../image.picker.tab.module';
import {
    FILTERS_ACTION_FILTER_ERROR_TOGGLE,
    FILTERS_ACTION_FILTER_SET,
    FILTERS_DATA_MIME_TYPES,
    FILTERS_DATA_SIZE_MAX,
    FILTERS_DATA_SIZE_MIN,
} from '../../hooks/useFilters';
import { getTranslator } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';

const MIME_TYPES = [
    { label: 'JPG', mimeType: 'image/jpeg' },
    { label: 'PNG', mimeType: 'image/png' },
    { label: 'WEBP', mimeType: 'image/webp' },
    { label: 'GIF', mimeType: 'image/gif' },
];

const FILE_SIZE_ERROR = 'FILE_SIZE_ERROR';

const FiltersGroupFormatSize = () => {
    const Translator = getTranslator();
    const { filters, filtersErrors, dispatchFiltersAction } = useContext(FiltersContext);
    const hasError = filtersErrors.includes(FILE_SIZE_ERROR);
    const formatAndSizeTitle = Translator.trans(/*@Desc("Format and size")*/ 'filters.format_and_size.title', {}, 'ibexa_image_picker');
    const fileFormatLabel = Translator.trans(/*@Desc("File format")*/ 'filters.file_format.label', {}, 'ibexa_image_picker');
    const fileSizeLabel = Translator.trans(/*@Desc("File size")*/ 'filters.file_size.label', {}, 'ibexa_image_picker');
    const handleMimeTypeCheckboxChange = (event) => {
        const checkbox = event.currentTarget;
        const mimeType = checkbox.value;
        const isChecked = checkbox.checked;

        if (isChecked) {
            dispatchFiltersAction({
                type: FILTERS_ACTION_FILTER_SET,
                filterName: FILTERS_DATA_MIME_TYPES,
                updateFunction: (oldMimeTypes) => [...(oldMimeTypes ?? []), mimeType],
            });
        } else {
            dispatchFiltersAction({
                type: FILTERS_ACTION_FILTER_SET,
                filterName: FILTERS_DATA_MIME_TYPES,
                updateFunction: (oldMimeTypes) => [...(oldMimeTypes ?? [])].filter((value) => value !== mimeType),
            });
        }
    };
    const handleMinSizeChange = (event) => {
        const { value } = event.currentTarget;
        const parsedValue = parseFloat(value);

        dispatchFiltersAction({
            type: FILTERS_ACTION_FILTER_SET,
            filterName: FILTERS_DATA_SIZE_MIN,
            value: isNaN(parsedValue) ? null : parsedValue,
        });
    };
    const handleMaxSizeChange = (event) => {
        const { value } = event.currentTarget;
        const parsedValue = parseFloat(value);

        dispatchFiltersAction({
            type: FILTERS_ACTION_FILTER_SET,
            filterName: FILTERS_DATA_SIZE_MAX,
            value: isNaN(parsedValue) ? null : parsedValue,
        });
    };
    const handleFileSizeErrorChange = (isInvalid) => {
        dispatchFiltersAction({
            type: FILTERS_ACTION_FILTER_ERROR_TOGGLE,
            errorName: FILE_SIZE_ERROR,
            isPresent: isInvalid,
        });
    };

    return (
        <FiltersGroup id="format-and-size" title={formatAndSizeTitle} hasError={hasError}>
            <label className="ibexa-label">{fileFormatLabel}</label>
            <div className="form-group">
                {MIME_TYPES.map(({ label, mimeType }) => {
                    const inputId = `FiltersGroupFormatSize-checkbox-${mimeType}`;

                    return (
                        <div key={mimeType} className="form-check">
                            <input
                                className="ibexa-input ibexa-input--checkbox form-check-input"
                                type="checkbox"
                                value={mimeType}
                                id={inputId}
                                checked={filters[FILTERS_DATA_MIME_TYPES]?.includes(mimeType) ?? false}
                                onChange={handleMimeTypeCheckboxChange}
                            />
                            <label className="ibexa-label ibexa-label--checkbox-radio form-check-label" htmlFor={inputId}>
                                {label}
                            </label>
                        </div>
                    );
                })}
            </div>
            <label className="ibexa-label">{fileSizeLabel}</label>
            <RangeNumberInput
                valueLeft={filters[FILTERS_DATA_SIZE_MIN]}
                valueRight={filters[FILTERS_DATA_SIZE_MAX]}
                onValueLeftChange={handleMinSizeChange}
                onValueRightChange={handleMaxSizeChange}
                onErrorChange={handleFileSizeErrorChange}
                min={0}
                step={1}
                labelAfter="MB"
            />
        </FiltersGroup>
    );
};

export default FiltersGroupFormatSize;
