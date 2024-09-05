import React from 'react';
import PropTypes from 'prop-types';
import ConfigPanel, { CONFIG_PANEL_TYPE_RIGHT } from '../../../common/components/config-panel/config.panel';
import ConfigPanelBody from '../../../common/components/config-panel/config.panel.body';

const BlockConfigPanel = ({ title, subtitle, isClosed, onCancel, isDistractionFreeModeActive, children }) => {
    return (
        <ConfigPanel
            extraClasses="c-pb-block-config-panel"
            type={CONFIG_PANEL_TYPE_RIGHT}
            onCancel={onCancel}
            title={title}
            subtitle={subtitle}
            isClosed={isClosed}
            isDistractionFreeModeActive={isDistractionFreeModeActive}
        >
            <ConfigPanelBody extraClasses="c-pb-block-config-panel__body">{children}</ConfigPanelBody>
        </ConfigPanel>
    );
};

BlockConfigPanel.propTypes = {
    children: PropTypes.element,
    title: PropTypes.string.isRequired,
    subtitle: PropTypes.string,
    isClosed: PropTypes.bool.isRequired,
    onCancel: PropTypes.func.isRequired,
    isDistractionFreeModeActive: PropTypes.bool,
};

BlockConfigPanel.defaultProps = {
    children: null,
    subtitle: null,
    isDistractionFreeModeActive: false,
};

export default BlockConfigPanel;
