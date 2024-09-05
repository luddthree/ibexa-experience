import React from 'react';
import PropTypes from 'prop-types';

import ActionItem from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/components/action-list-item/action.list.item';

const { Translator, Routing } = window;

const PreviewContent = ({ item, isLoading, fetchedData }) => {
    const { locationId, contentId, versionNo, translations: translationsString } = item.internalItem;
    const translations = translationsString.split(',');
    const defaultTranslation = translations[0];
    const previewableTranslations = fetchedData[0]?.previewableTranslations.values ?? [];
    const isDisabled = !previewableTranslations.includes(defaultTranslation) || isLoading;
    const itemLabel = Translator.trans(/*@Desc("Preview")*/ 'actions.preview_content', {}, 'ibexa_content_tree_ui');
    const getPreviewLink = (languageCode) => {
        const siteAccessContextSelect = document.querySelector('.ibexa-change-siteaccess-context');
        const selectedSiteAccessContext = siteAccessContextSelect?.value ?? null;
        const previewRouteParams = {
            locationId,
            contentId,
            versionNo,
            languageCode,
            referrer: 'content_view',
        };

        if (selectedSiteAccessContext) {
            previewRouteParams.preselectedSiteAccess = selectedSiteAccessContext;
        }

        return Routing.generate('ibexa.content.preview', previewRouteParams);
    };
    const openPreview = () => {
        window.location = getPreviewLink(defaultTranslation);
    };

    return <ActionItem label={itemLabel} isLoading={isLoading} isDisabled={isDisabled} onClick={openPreview} />;
};

PreviewContent.propTypes = {
    item: PropTypes.object,
    isLoading: PropTypes.bool,
    fetchedData: PropTypes.arrayOf(PropTypes.object),
};

PreviewContent.defaultProps = {
    item: {},
    isLoading: false,
    fetchedData: [],
};

export default PreviewContent;
