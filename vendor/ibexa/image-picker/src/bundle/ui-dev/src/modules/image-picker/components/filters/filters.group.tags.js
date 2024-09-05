import React, { useContext, useEffect, useState } from 'react';

import Dropdown from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/dropdown/dropdown';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import { DropdownPortalRefContext } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/universal-discovery/universal.discovery.module';

import FiltersGroup from './filters.group';
import { FiltersContext, SearchAggregationsContext } from '../../image.picker.tab.module';
import { FILTERS_ACTION_FILTER_SET, FILTERS_DATA_TAGS } from '../../hooks/useFilters';
import { getTranslator } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';

const FiltersGroupTags = () => {
    const Translator = getTranslator();
    const { filters, dispatchFiltersAction } = useContext(FiltersContext);
    const { searchAggregations } = useContext(SearchAggregationsContext);
    const dropdownListRef = useContext(DropdownPortalRefContext);
    const [tags, setTags] = useState(null);
    const isLoading = tags === null;
    const tagsTitle = Translator.trans(/*@Desc("Tags")*/ 'filters.tags.title', {}, 'ibexa_image_picker');
    const tagsLabel = Translator.trans(/*@Desc("Tags")*/ 'filters.tags.label', {}, 'ibexa_image_picker');
    const tagsPlaceholder = Translator.trans(/*@Desc("Choose tags")*/ 'filters.tags.placeholder', {}, 'ibexa_image_picker');
    const noTagsToSelectInfo = Translator.trans(
        /*@Desc("If no tags available, you can’t use this filter.")*/ 'filters.tags.no_tags.info',
        {},
        'ibexa_image_picker',
    );
    const handleTagSelected = (tag) => {
        const isChecked = (filters[FILTERS_DATA_TAGS] ?? []).includes(tag);

        if (!isChecked) {
            dispatchFiltersAction({
                type: FILTERS_ACTION_FILTER_SET,
                filterName: FILTERS_DATA_TAGS,
                updateFunction: (oldTags) => [...(oldTags ?? []), tag],
            });
        } else {
            dispatchFiltersAction({
                type: FILTERS_ACTION_FILTER_SET,
                filterName: FILTERS_DATA_TAGS,
                updateFunction: (oldTags) => [...(oldTags ?? [])].filter((value) => value !== tag),
            });
        }
    };
    const renderDropdown = () => {
        const tagsOptions = tags.map((tag) => ({
            value: tag,
            label: tag,
        }));
        const isDisabled = tagsOptions.length === 0;

        return (
            <>
                <Dropdown
                    dropdownListRef={dropdownListRef}
                    single={false}
                    placeholder={tagsPlaceholder}
                    onChange={handleTagSelected}
                    value={filters[FILTERS_DATA_TAGS] ?? []}
                    options={tagsOptions}
                    disabled={isDisabled}
                />
                <small className="ibexa-form-help">
                    <Icon name="system-information" extraClasses="ibexa-icon ibexa-icon--small ibexa-form-help__icon" />
                    <div className="ibexa-form-help__content">{noTagsToSelectInfo}</div>
                </small>
            </>
        );
    };

    useEffect(() => {
        if (!searchAggregations) {
            return;
        }

        const tagsAggregation = searchAggregations?.find((searchAggregation) => searchAggregation.name === 'keywords') ?? null;
        const newTags = tagsAggregation?.entries.map((tagAggregation) => tagAggregation.key) ?? [];

        setTags((oldTags) => [...new Set([...(oldTags ?? []), ...newTags])]);
    }, [searchAggregations]);

    return (
        <FiltersGroup id="tags" title={tagsTitle}>
            {isLoading && (
                <div className="c-ip-items-view__loading-spinner">
                    <Icon name="spinner" extraClasses="ibexa-icon--medium ibexa-spin" />
                </div>
            )}
            {!isLoading && (
                <>
                    <label className="ibexa-label">{tagsLabel}</label>
                    <div className="form-group">{renderDropdown()}</div>
                </>
            )}
        </FiltersGroup>
    );
};

FiltersGroupTags.propTypes = {};

export default FiltersGroupTags;
