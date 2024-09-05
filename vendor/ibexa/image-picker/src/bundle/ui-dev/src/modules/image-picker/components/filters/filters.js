import React, { useContext } from 'react';

import FiltersGroupTranslations from './filters.group.translations';
import { FiltersContext } from '../../image.picker.tab.module';
import { FILTERS_ACTION_FILTERS_CLEAR, FILTERS_DATA_SEARCH_TEXT } from '../../hooks/useFilters';
import FiltersGroupFormatSize from './filters.group.format.size';
import FiltersGroupOrientationDimensions from './filters.group.orientation.dimensions';
import FiltersGroupTags from './filters.group.tags';
import FiltersGroupCreated from './filters.group.created';

import { getTranslator, getAdminUiConfig } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';

const Filters = () => {
    const Translator = getTranslator();
    const adminUiConfig = getAdminUiConfig();
    const { filters, filtersErrors, lastAppliedFilters, performSearch, dispatchFiltersAction } = useContext(FiltersContext);
    const haveFiltersErrors = filtersErrors.length > 0;
    const filtersLabel = Translator.trans(/*@Desc("Filters")*/ 'filters.title', {}, 'ibexa_image_picker');
    const clearLabel = Translator.trans(/*@Desc("Clear")*/ 'filters.clear', {}, 'ibexa_image_picker');
    const applyLabel = Translator.trans(/*@Desc("Apply")*/ 'filters.apply', {}, 'ibexa_image_picker');
    const languages = Object.values(adminUiConfig.languages.mappings);
    const enabledLanguagesCount = languages.filter((language) => language.enabled).length;
    const showTranslationsFilters = enabledLanguagesCount > 1;
    const { showImageFilters } = adminUiConfig.damWidget.image;
    const checkIsClearBtnDisabled = () => {
        const filtersWithoutSearchText = { ...filters };

        delete filtersWithoutSearchText[FILTERS_DATA_SEARCH_TEXT];

        return Object.values(filtersWithoutSearchText).every((value) => !value || (value instanceof Array && value.length === 0));
    };
    const clearFilters = () => {
        dispatchFiltersAction({ type: FILTERS_ACTION_FILTERS_CLEAR });
    };

    if (!showTranslationsFilters && !showImageFilters) {
        return null;
    }

    return (
        <div className="c-ip-filters">
            <div className="c-ip-filters__header">
                <div className="c-ip-filters__header-content">{filtersLabel}</div>
                <div className="c-ip-filters__header-actions">
                    <button
                        className="btn ibexa-btn ibexa-btn--ghost ibexa-btn--small"
                        type="button"
                        onClick={clearFilters}
                        disabled={checkIsClearBtnDisabled()}
                    >
                        {clearLabel}
                    </button>
                    <button
                        type="submit"
                        className="btn ibexa-btn ibexa-btn--secondary ibexa-btn--small ibexa-btn--apply"
                        onClick={performSearch}
                        disabled={filters === lastAppliedFilters || haveFiltersErrors}
                    >
                        {applyLabel}
                    </button>
                </div>
            </div>
            {showTranslationsFilters && <FiltersGroupTranslations />}
            {showImageFilters && (
                <>
                    <FiltersGroupFormatSize />
                    <FiltersGroupOrientationDimensions />
                    <FiltersGroupTags />
                    <FiltersGroupCreated />
                </>
            )}
        </div>
    );
};

export default Filters;
