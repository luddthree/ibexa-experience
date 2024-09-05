import React, { useContext, useEffect } from 'react';

import FiltersGroup from './filters.group';
import DatePicker from './date.picker';
import { FiltersContext } from '../../image.picker.tab.module';
import {
    FILTERS_ACTION_FILTER_ERROR_TOGGLE,
    FILTERS_ACTION_FILTER_SET,
    FILTERS_DATA_CREATED_FROM,
    FILTERS_DATA_CREATED_TO,
} from '../../hooks/useFilters';
import { getTranslator } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';

const CREATED_ERROR = 'CREATED_ERROR';

const FiltersGroupCreated = () => {
    const Translator = getTranslator();
    const { filters, filtersErrors, dispatchFiltersAction } = useContext(FiltersContext);
    const hasError = filtersErrors.includes(CREATED_ERROR);
    const createdFromValue = filters[FILTERS_DATA_CREATED_FROM];
    const createdToValue = filters[FILTERS_DATA_CREATED_TO];
    const createdFromValueParsed = parseInt(createdFromValue, 10);
    const createdToValueParsed = parseInt(createdToValue, 10);
    const isInvalid = createdFromValueParsed > createdToValueParsed;
    const createdTitle = Translator.trans(/*@Desc("Created")*/ 'filters.created.title', {}, 'ibexa_image_picker');
    const createdFromLabel = Translator.trans(/*@Desc("From")*/ 'filters.created.from.label', {}, 'ibexa_image_picker');
    const createdToLabel = Translator.trans(/*@Desc("To")*/ 'filters.created.to.label', {}, 'ibexa_image_picker');
    const handleCreatedFromChange = (dateTime) => {
        const parsedDateTime = parseInt(dateTime, 10);

        dispatchFiltersAction({
            type: FILTERS_ACTION_FILTER_SET,
            filterName: FILTERS_DATA_CREATED_FROM,
            value: isNaN(parsedDateTime) ? null : parsedDateTime,
        });
    };
    const handleCreatedToChange = (dateTime) => {
        const parsedDateTime = parseInt(dateTime, 10);

        dispatchFiltersAction({
            type: FILTERS_ACTION_FILTER_SET,
            filterName: FILTERS_DATA_CREATED_TO,
            value: isNaN(parsedDateTime) ? null : parsedDateTime,
        });
    };

    useEffect(() => {
        dispatchFiltersAction({
            type: FILTERS_ACTION_FILTER_ERROR_TOGGLE,
            errorName: CREATED_ERROR,
            isPresent: isInvalid,
        });
    }, [isInvalid]);

    return (
        <FiltersGroup id="created" title={createdTitle} hasError={hasError}>
            <label className="ibexa-label">{createdFromLabel}</label>
            <DatePicker value={createdFromValue} isInvalid={isInvalid} onDateChange={handleCreatedFromChange} />
            <label className="ibexa-label">{createdToLabel}</label>
            <DatePicker value={createdToValue} isInvalid={isInvalid} onDateChange={handleCreatedToChange} />
        </FiltersGroup>
    );
};

export default FiltersGroupCreated;
