import React, { useMemo } from 'react';
import PropTypes from 'prop-types';

import Popup from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/popup/popup.component.js';

const { Translator } = window;

const SettingsPopup = ({ isVisible, onContinue, onChangeSettings }) => {
    const title = Translator.trans(/*@Desc("How builder works")*/ 'settings.popup.title', {}, 'ibexa_page_builder');
    const addBlockInfoLabel = Translator.trans(
        /*@Desc("When you add a block by dragging it from Page blocks menu into the drop zone, the block settings panel opens immediately where you can configure all block properties.")*/ 'settings.popup.add_block_info',
        {},
        'ibexa_page_builder',
    );
    const defaultBehaviourLabel = Translator.trans(
        /*@Desc("This is a default behavior which applies to both Page and Dashboard builder. You can globally turn off automatic opening of the block settings panel in the user settings.")*/ 'settings.popup.default_behaviour',
        {},
        'ibexa_page_builder',
    );
    const actionBtnsConfig = useMemo(() => {
        const continueBtn = {
            label: Translator.trans(/*@Desc("Continue")*/ 'settings.popup.continue', {}, 'ibexa_page_builder'),
            className: 'ibexa-btn--filled-info',
        };
        const changeSettingsBtn = {
            label: Translator.trans(/*@Desc("Change settings")*/ 'settings.popup.change_settings', {}, 'ibexa_page_builder'),
            'data-bs-dismiss': 'modal',
            onClick: onChangeSettings,
            className: 'ibexa-btn--info',
        };

        return [continueBtn, changeSettingsBtn];
    }, [onChangeSettings]);

    return (
        <Popup
            isVisible={isVisible}
            size="medium"
            onClose={onContinue}
            title={title}
            extraClasses="c-pb-settings-modal__popup"
            actionBtnsConfig={actionBtnsConfig}
        >
            <div className="c-pb-settings-modal__text-wrapper">
                <p className="c-pb-settings-modal__text">{addBlockInfoLabel}</p>
                <p className="c-pb-settings-modal__text">{defaultBehaviourLabel}</p>
            </div>
        </Popup>
    );
};

SettingsPopup.propTypes = {
    isVisible: PropTypes.bool.isRequired,
    onContinue: PropTypes.func.isRequired,
    onChangeSettings: PropTypes.func.isRequired,
};

export default SettingsPopup;
