import React, { useState, useEffect } from 'react';
import { createPortal } from 'react-dom';
import PropTypes from 'prop-types';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import ConfigPanel, {
    CONFIG_PANEL_TYPE_LEFT,
    LEFT_PANEL_TYPES,
} from '@ibexa-page-builder/src/bundle/ui-dev/src/modules/common/components/config-panel/config.panel';
import ConfigPanelBody from '@ibexa-page-builder/src/bundle/ui-dev/src/modules/common/components/config-panel/config.panel.body';
import ConfigPanelFooter from '@ibexa-page-builder/src/bundle/ui-dev/src/modules/common/components/config-panel/config.panel.footer';
import Search from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/input/filter.search';
import SegmentGroup from './components/segment.group';

import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

const { Translator, ibexa } = window;
const CLOSE_CONFIG_PANEL_KEY = 'Escape';

const UpdatePreviewRequestParams = ({ updatePreviewRequestParams, blockPreviewPagePreviewRequestParams }) => {
    const segmentationsListNode = document.querySelector('.ibexa-segmentation-list');
    const pageBuilderConfigPanelWrapper = document.querySelector('.ibexa-pb-config-panels-wrapper');
    const [isConfigPanelVisible, setIsConfigPanelVisible] = useState(false);
    const [segmentFilterQuery, setSegmentFilterQuery] = useState('');
    const closeBtnLabel = Translator.trans(/*@Desc("Save and close")*/ 'visibility.action_btn.close', {}, 'ibexa_page_builder');

    useEffect(() => {
        if (isConfigPanelVisible) {
            document.body.addEventListener('click', closeConfigPanelByClickOutside, false);
            document.body.addEventListener('keyup', closeConfigPanelByKeyboard, false);
        }

        return () => {
            document.body.removeEventListener('click', closeConfigPanelByClickOutside);
            document.body.removeEventListener('keyup', closeConfigPanelByKeyboard);
        };
    }, [isConfigPanelVisible]);

    if (!segmentationsListNode) {
        return;
    }

    const segmentationsList = JSON.parse(segmentationsListNode.dataset.segments);
    const handleExternalCloseConfigPanel = () => {
        setIsConfigPanelVisible(false);

        return true;
    };
    const handleInternalCloseConfigPanel = () => {
        setIsConfigPanelVisible(() => !document.dispatchEvent(new CustomEvent('ibexa-pb-config-panel-close-itself')));
    };
    const closeConfigPanelByClickOutside = (event) => event.target.classList.contains('ibexa-backdrop') && handleInternalCloseConfigPanel();
    const closeConfigPanelByKeyboard = (event) => event.key === CLOSE_CONFIG_PANEL_KEY && handleInternalCloseConfigPanel();
    const toggleConfigPanel = () => {
        setIsConfigPanelVisible((isConfigPanelVisiblePrev) => {
            if (isConfigPanelVisiblePrev) {
                document.dispatchEvent(new CustomEvent('ibexa-pb-config-panel-close-itself'));

                return false;
            }

            const wasConfigPanelOpened = document.dispatchEvent(
                new CustomEvent('ibexa-pb-config-panel-open', {
                    cancelable: true,
                    detail: { settings: { onClose: handleExternalCloseConfigPanel } },
                }),
            );

            if (wasConfigPanelOpened) {
                ibexa.helpers.tooltips.hideAll();
            }

            return wasConfigPanelOpened;
        });
    };
    const handleSegmentSelect = (segmentId) => {
        const requestParams = { ...blockPreviewPagePreviewRequestParams };

        if (segmentId) {
            requestParams.segmentId = segmentId;
        } else {
            delete requestParams.segmentId;
        }

        updatePreviewRequestParams(requestParams);
    };

    const renderInfobarActionMenuButton = () => {
        const togglerTitle = Translator.trans(/*@Desc("Visibility")*/ 'visibility.action_btn.title', {}, 'ibexa_page_builder');
        const togglerClassName = createCssClassNames({
            'btn ibexa-btn ibexa-btn--no-text ibexa-btn--selector': true,
            'ibexa-pb-action-bar__action-btn': true,
            [`ibexa-pb-action-bar__action-btn--${LEFT_PANEL_TYPES.VISIBILITY}`]: true,
            'ibexa-btn--selected': isConfigPanelVisible,
        });

        return (
            <button className={togglerClassName} onClick={toggleConfigPanel} title={togglerTitle} type="button">
                <Icon name="profile" extraClasses="ibexa-icon--medium" />
            </button>
        );
    };

    const updateSegmentFilterQuery = ({ target: { value } }) => {
        setSegmentFilterQuery(value);
    };

    const renderSegmentationList = () => {
        const configPanelTitle = Translator.trans(/*@Desc("Segments")*/ 'segments.config_panel.title', {}, 'ibexa_page_builder');
        const noneOptionLabel = Translator.trans(/*@Desc("None")*/ 'segments.config_panel.none_option.label', {}, 'ibexa_page_builder');
        const moreButtonLabel = Translator.trans(/*@Desc("More")*/ 'segments.config_panel.more_button', {}, 'ibexa_page_builder');

        return (
            <ConfigPanel
                type={CONFIG_PANEL_TYPE_LEFT}
                leftPanelType={LEFT_PANEL_TYPES.VISIBILITY}
                isClosed={!isConfigPanelVisible}
                onCancel={handleInternalCloseConfigPanel}
                title={configPanelTitle}
            >
                <ConfigPanelBody extraClasses="c-segments">
                    <div className="c-segments__container">
                        <div className="c-segments__wrapper">
                            <div className="c-segments__search-bar">
                                <Search
                                    onChange={updateSegmentFilterQuery}
                                    value={segmentFilterQuery}
                                    extraClasses="c-segments__sidebar-filter"
                                />
                            </div>
                            <label className="c-segments__label c-segments__label--no-filters">
                                <input
                                    type="radio"
                                    name="segment"
                                    className="ibexa-input ibexa-input--radio"
                                    onChange={() => handleSegmentSelect(null)}
                                    defaultChecked={true}
                                />
                                {noneOptionLabel}
                            </label>
                            <div className="c-segments__filters">
                                {segmentationsList.map((segmentationGroup) => (
                                    <SegmentGroup
                                        key={segmentationGroup.id}
                                        id={segmentationGroup.id}
                                        name={segmentationGroup.name}
                                        segments={segmentationGroup.segments}
                                        onChange={handleSegmentSelect}
                                        segmentFilterQueryLowerCase={segmentFilterQuery.toLowerCase()}
                                        moreButtonLabel={moreButtonLabel}
                                    />
                                ))}
                            </div>
                        </div>
                    </div>
                </ConfigPanelBody>
                <ConfigPanelFooter isClosed={!isConfigPanelVisible}>
                    <button type="button" className="btn ibexa-btn ibexa-btn--filled-info" onClick={handleInternalCloseConfigPanel}>
                        {closeBtnLabel}
                    </button>
                </ConfigPanelFooter>
            </ConfigPanel>
        );
    };

    return (
        <>
            {createPortal(renderInfobarActionMenuButton(), segmentationsListNode)}
            {createPortal(renderSegmentationList(), pageBuilderConfigPanelWrapper)}
        </>
    );
};

UpdatePreviewRequestParams.propTypes = {
    updatePreviewRequestParams: PropTypes.func.isRequired,
    blockPreviewPagePreviewRequestParams: PropTypes.object.isRequired,
};

export default UpdatePreviewRequestParams;

window.ibexa.addConfig('pageBuilder.components.updatePreviewRequestParams', [UpdatePreviewRequestParams], true);
