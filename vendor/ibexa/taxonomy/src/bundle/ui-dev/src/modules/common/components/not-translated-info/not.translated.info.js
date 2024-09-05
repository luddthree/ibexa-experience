import React from 'react';
import PropTypes from 'prop-types';

import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';

const { Translator, ibexa } = window;

const NotTranslatedInfo = ({ languageCode }) => {
    const language = ibexa.adminUiConfig.languages.mappings[languageCode];
    const languageName = language.name;
    const warningMessage = Translator.trans(
        /*@Desc("Not translated in %languageName% language")*/ 'taxonomy.not_translated_info',
        { languageName },
        'ibexa_taxonomy_ui',
    );

    return (
        <div className="c-tt-not-translated-info">
            <Icon name="warning-triangle" extraClasses="ibexa-icon--small c-tt-not-translated-info__icon-warning" />
            {warningMessage}
        </div>
    );
};

NotTranslatedInfo.propTypes = {
    languageCode: PropTypes.string.isRequired,
};

export default NotTranslatedInfo;
