import React, { useContext } from 'react';

import Icon from '@ibexa-admin-ui-modules/common/icon/icon';
import { ConfirmContext, SelectedLocationsContext } from '@ibexa-admin-ui-modules/universal-discovery/universal.discovery.module';
import { getTranslator } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';

const Snackbar = () => {
    const Translator = getTranslator();
    const onConfirm = useContext(ConfirmContext);
    const [selectedLocations] = useContext(SelectedLocationsContext);
    const isAnyLocationSelected = !!selectedLocations.length;

    if (!isAnyLocationSelected) {
        return null;
    }

    const selectedLabel = Translator.trans(/*@Desc("Selected")*/ 'snackbar.selected.label', {}, 'ibexa_image_picker');
    const insertLabel = Translator.trans(/*@Desc("Insert")*/ 'snackbar.insert.label', {}, 'ibexa_image_picker');

    return (
        <div className="c-ip-snackbar">
            <div className="c-ip-snackbar__selection-info">
                <div className="c-ip-snackbar__selection-info-label">{selectedLabel}</div>
                {selectedLocations.map((selectedLocation) => (
                    <div key={selectedLocation.location.id} className="c-ip-snackbar__selection-info-item">
                        <div className="c-ip-snackbar__selection-info-item-content">
                            {selectedLocation.location.ContentInfo.Content.TranslatedName}
                        </div>
                    </div>
                ))}
            </div>
            <button
                className="c-ip-snackbar__insert-btn btn ibexa-btn ibexa-btn--small ibexa-btn--dark"
                type="button"
                onClick={() => onConfirm()}
            >
                <Icon name="upload-image" extraClasses="ibexa-icon--small" />
                {insertLabel}
            </button>
        </div>
    );
};

export default Snackbar;
