import React, { useRef, useEffect } from 'react';
import PropTypes from 'prop-types';

import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import Dropdown from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/dropdown/dropdown';

const { Translator, ibexa } = window;
const languages = Object.values(ibexa.adminUiConfig.languages.mappings).filter(({ enabled }) => enabled);

const HeaderContent = ({ name, languageCode, setLanguageCode }) => {
    const dropdownListRef = useRef();
    const language = ibexa.adminUiConfig.languages.mappings[languageCode];
    const languageName = language.name;
    const languageInfoMessage = Translator.trans(
        /*@Desc("in %languageName%")*/ 'taxonomy.in_language',
        { languageName },
        'ibexa_taxonomy_ui',
    );
    const options = languages.map((option) => ({
        value: option.languageCode,
        label: option.name,
    }));
    const renderLanguageSwitcher = () => {
        const switcherIcon = <Icon name="languages" extraClasses="ibexa-icon--small" />;

        return (
            <Dropdown
                single={true}
                value={languageCode}
                options={options}
                dropdownListRef={dropdownListRef}
                onChange={setLanguageCode}
                renderSelectedItem={() => switcherIcon}
            />
        );
    };

    useEffect(() => {
        if (!dropdownListRef.current) {
            dropdownListRef.current = document.createElement('div');

            dropdownListRef.current.classList.add('c-tt-header-content-language-list', 'c-tb-element');
            document.body.appendChild(dropdownListRef.current);
        }

        return () => {
            if (dropdownListRef.current) {
                dropdownListRef.current.remove();
            }
        };
    }, []);

    return (
        <div className="c-tt-header-content c-tb-header__name">
            <Icon name="content-tree" extraClasses="ibexa-icon--small c-tb-header__tree-icon" />
            <div className="c-tt-header-content__right-column">
                <div className="c-tt-header-content__right-column-content">
                    <span className="c-tb-header__name-content">{name}</span>
                    <span className="c-tt-header-content__language">{languageInfoMessage}</span>
                </div>
                <div className="c-tt-header-content__right-column-switcher">{renderLanguageSwitcher()}</div>
            </div>
        </div>
    );
};

HeaderContent.propTypes = {
    name: PropTypes.node.isRequired,
    languageCode: PropTypes.string.isRequired,
    setLanguageCode: PropTypes.func.isRequired,
};

export default HeaderContent;
