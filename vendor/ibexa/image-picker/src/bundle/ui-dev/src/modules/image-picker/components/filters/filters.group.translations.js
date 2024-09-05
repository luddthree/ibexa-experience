import React, { useContext } from 'react';

import FiltersGroup from './filters.group';
import { FiltersContext } from '../../image.picker.tab.module';
import { FILTERS_ACTION_FILTER_SET, FILTERS_DATA_LANGUAGES } from '../../hooks/useFilters';
import { getTranslator, getAdminUiConfig } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';

const FiltersGroupTranslations = () => {
    const Translator = getTranslator();
    const adminUiConfig = getAdminUiConfig();
    const { filters, dispatchFiltersAction } = useContext(FiltersContext);
    const translationsTitle = Translator.trans(/*@Desc("Translations")*/ 'filters.translations.title', {}, 'ibexa_image_picker');
    const languageLabel = Translator.trans(/*@Desc("Language")*/ 'filters.language.label', {}, 'ibexa_image_picker');
    const languages = Object.values(adminUiConfig.languages.mappings);
    const filterLanguages = languages
        .filter((language) => language.enabled)
        .map((language) => ({
            languageCode: language.languageCode,
            label: language.name,
        }));
    const handleLanguageRadioClick = (event) => {
        const radio = event.currentTarget;
        const languageCode = radio.value;
        const isChecked = filters[FILTERS_DATA_LANGUAGES] === languageCode;

        dispatchFiltersAction({
            type: FILTERS_ACTION_FILTER_SET,
            filterName: FILTERS_DATA_LANGUAGES,
            value: isChecked ? null : languageCode,
        });
    };

    return (
        <FiltersGroup id="translations" title={translationsTitle}>
            <label className="ibexa-label">{languageLabel}</label>
            <div className="form-group">
                {filterLanguages.map(({ languageCode, label }) => {
                    const inputId = `FiltersGroupTranslations-checkbox-${languageCode}`;

                    return (
                        <div key={languageCode} className="form-check">
                            <input
                                className="ibexa-input ibexa-input--radio form-check-input"
                                type="radio"
                                value={languageCode}
                                id={inputId}
                                checked={filters[FILTERS_DATA_LANGUAGES] === languageCode}
                                onClick={handleLanguageRadioClick}
                                onChange={() => {}}
                            />
                            <label className="ibexa-label ibexa-label--checkbox-radio form-check-label" htmlFor={inputId}>
                                {label}
                            </label>
                        </div>
                    );
                })}
            </div>
        </FiltersGroup>
    );
};

export default FiltersGroupTranslations;
