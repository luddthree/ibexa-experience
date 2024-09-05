import React, { useContext } from 'react';

import FiltersGroup from './filters.group';
import RangeNumberInput from './range.number.input';
import { FiltersContext } from '../../image.picker.tab.module';
import {
    FILTERS_ACTION_FILTER_ERROR_TOGGLE,
    FILTERS_ACTION_FILTER_SET,
    FILTERS_DATA_HEIGHT_MAX,
    FILTERS_DATA_HEIGHT_MIN,
    FILTERS_DATA_ORIENTATIONS,
    FILTERS_DATA_WIDTH_MAX,
    FILTERS_DATA_WIDTH_MIN,
} from '../../hooks/useFilters';
import { getTranslator } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';

const WIDTH_SIZE_ERROR = 'WIDTH_SIZE_ERROR';
const HEIGHT_SIZE_ERROR = 'HEIGHT_SIZE_ERROR';

const FiltersGroupOrientationDimensions = () => {
    const Translator = getTranslator();
    const { filters, filtersErrors, dispatchFiltersAction } = useContext(FiltersContext);
    const hasError = filtersErrors.includes(WIDTH_SIZE_ERROR) || filtersErrors.includes(HEIGHT_SIZE_ERROR);
    const orientationAndDimensionsTitle = Translator.trans(
        /*@Desc("Orientation and dimensions")*/ 'filters.orientation_and_dimensions.title',
        {},
        'ibexa_image_picker',
    );
    const orientationLabel = Translator.trans(
        /*@Desc("Orientation")*/ 'filters.orientation_and_dimensions.orientation.label',
        {},
        'ibexa_image_picker',
    );
    const landscapeLabel = Translator.trans(
        /*@Desc("Landscape")*/ 'filters.orientation_and_dimensions.landscape.label',
        {},
        'ibexa_image_picker',
    );
    const portraitLabel = Translator.trans(
        /*@Desc("Portrait")*/ 'filters.orientation_and_dimensions.portrait.label',
        {},
        'ibexa_image_picker',
    );
    const squarishLabel = Translator.trans(
        /*@Desc("Squarish")*/ 'filters.orientation_and_dimensions.squarish.label',
        {},
        'ibexa_image_picker',
    );
    const widthLabel = Translator.trans(/*@Desc("Width")*/ 'filters.orientation_and_dimensions.width.label', {}, 'ibexa_image_picker');
    const heightLabel = Translator.trans(/*@Desc("Height")*/ 'filters.orientation_and_dimensions.height.label', {}, 'ibexa_image_picker');
    const orientations = [
        { label: landscapeLabel, orientation: 'landscape' },
        { label: portraitLabel, orientation: 'portrait' },
        { label: squarishLabel, orientation: 'square' },
    ];
    const handleOrientationCheckboxChange = (event) => {
        const checkbox = event.currentTarget;
        const orientation = checkbox.value;
        const isChecked = checkbox.checked;

        if (isChecked) {
            dispatchFiltersAction({
                type: FILTERS_ACTION_FILTER_SET,
                filterName: FILTERS_DATA_ORIENTATIONS,
                updateFunction: (oldOrientations) => [...(oldOrientations ?? []), orientation],
            });
        } else {
            dispatchFiltersAction({
                type: FILTERS_ACTION_FILTER_SET,
                filterName: FILTERS_DATA_ORIENTATIONS,
                updateFunction: (oldOrientations) => [...(oldOrientations ?? [])].filter((value) => value !== orientation),
            });
        }
    };
    const handleWidthMinChange = (event) => {
        dispatchFiltersAction({
            type: FILTERS_ACTION_FILTER_SET,
            filterName: FILTERS_DATA_WIDTH_MIN,
            value: parseInt(event.currentTarget.value, 10),
        });
    };
    const handleWidthMaxChange = (event) => {
        dispatchFiltersAction({
            type: FILTERS_ACTION_FILTER_SET,
            filterName: FILTERS_DATA_WIDTH_MAX,
            value: parseInt(event.currentTarget.value, 10),
        });
    };
    const handleHeightMinChange = (event) => {
        dispatchFiltersAction({
            type: FILTERS_ACTION_FILTER_SET,
            filterName: FILTERS_DATA_HEIGHT_MIN,
            value: parseInt(event.currentTarget.value, 10),
        });
    };
    const handleHeightMaxChange = (event) => {
        dispatchFiltersAction({
            type: FILTERS_ACTION_FILTER_SET,
            filterName: FILTERS_DATA_HEIGHT_MAX,
            value: parseInt(event.currentTarget.value, 10),
        });
    };
    const handleWidthErrorChange = (isInvalid) => {
        dispatchFiltersAction({
            type: FILTERS_ACTION_FILTER_ERROR_TOGGLE,
            errorName: WIDTH_SIZE_ERROR,
            isPresent: isInvalid,
        });
    };
    const handleHeightErrorChange = (isInvalid) => {
        dispatchFiltersAction({
            type: FILTERS_ACTION_FILTER_ERROR_TOGGLE,
            errorName: HEIGHT_SIZE_ERROR,
            isPresent: isInvalid,
        });
    };

    return (
        <FiltersGroup id="orientation-and-dimension" title={orientationAndDimensionsTitle} hasError={hasError}>
            <label className="ibexa-label">{orientationLabel}</label>
            <div className="form-group">
                {orientations.map(({ label, orientation }) => {
                    const inputId = `FiltersGroupOrientationDimensions-checkbox-${orientation}`;

                    return (
                        <div key={orientation} className="form-check">
                            <input
                                className="ibexa-input ibexa-input--checkbox form-check-input"
                                type="checkbox"
                                value={orientation}
                                id={inputId}
                                checked={filters[FILTERS_DATA_ORIENTATIONS]?.includes(orientation) ?? false}
                                onChange={handleOrientationCheckboxChange}
                            />
                            <label className="ibexa-label ibexa-label--checkbox-radio form-check-label" htmlFor={inputId}>
                                {label}
                            </label>
                        </div>
                    );
                })}
            </div>
            <label className="ibexa-label">{widthLabel}</label>
            <RangeNumberInput
                valueLeft={filters[FILTERS_DATA_WIDTH_MIN]}
                valueRight={filters[FILTERS_DATA_WIDTH_MAX]}
                onValueLeftChange={handleWidthMinChange}
                onValueRightChange={handleWidthMaxChange}
                onErrorChange={handleWidthErrorChange}
                min={0}
                step={1}
                labelAfter={'px'}
            />
            <label className="ibexa-label">{heightLabel}</label>
            <RangeNumberInput
                valueLeft={filters[FILTERS_DATA_HEIGHT_MIN]}
                valueRight={filters[FILTERS_DATA_HEIGHT_MAX]}
                onValueLeftChange={handleHeightMinChange}
                onValueRightChange={handleHeightMaxChange}
                onErrorChange={handleHeightErrorChange}
                min={0}
                step={1}
                labelAfter={'px'}
            />
        </FiltersGroup>
    );
};

export default FiltersGroupOrientationDimensions;
