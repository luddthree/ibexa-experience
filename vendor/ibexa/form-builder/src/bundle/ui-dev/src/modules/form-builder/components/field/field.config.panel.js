import React from 'react';
import PropTypes from 'prop-types';
import ConfigPanel, {
    CONFIG_PANEL_TYPE_RIGHT,
} from '@ibexa-page-builder/src/bundle/ui-dev/src/modules/common/components/config-panel/config.panel';
import ConfigPanelBody from '@ibexa-page-builder/src/bundle/ui-dev/src/modules/common/components/config-panel/config.panel.body';

const FieldConfigPanel = ({ title, isClosed, onCancel, children }) => {
    return (
        <ConfigPanel
            extraClasses="c-ibexa-fb-config-panel"
            type={CONFIG_PANEL_TYPE_RIGHT}
            showCloseBtn={true}
            onCancel={onCancel}
            title={title}
            isClosed={isClosed}
            hasBackBtn={true}
        >
            <ConfigPanelBody extraClasses="c-ibexa-fb-config-panel__body">{children}</ConfigPanelBody>
        </ConfigPanel>
    );
};

FieldConfigPanel.propTypes = {
    children: PropTypes.element,
    title: PropTypes.string.isRequired,
    isClosed: PropTypes.bool.isRequired,
    onCancel: PropTypes.func.isRequired,
};

FieldConfigPanel.defaultProps = {
    children: null,
};

export default FieldConfigPanel;
