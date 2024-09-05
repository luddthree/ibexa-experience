import React, { useContext } from 'react';
import PropTypes from 'prop-types';

import ActionItem from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/components/action-list-item/action.list.item';
import { isItemEmpty } from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/helpers/item';

import { LanguageCodeContext } from '../../taxonomy.tree.module';

const { document, Translator, ibexa } = window;

const AddTag = ({ item }) => {
    const languageCode = useContext(LanguageCodeContext);
    const itemLabel = Translator.trans(/*@Desc("Add")*/ 'actions.add_tag', {}, 'ibexa_taxonomy_ui');
    const isDisabled = isItemEmpty(item);
    const addTag = () => {
        const form = document.querySelector('form[name="taxonomy_entry_create"]');
        const entryIdField = form.querySelector('#taxonomy_entry_create_parent_entry');
        const languageField = form.querySelector('#taxonomy_entry_create_language');
        const languageDropdownField = languageField.closest('.ibexa-dropdown');
        const languageDropdownInstance = ibexa.helpers.objectInstances.getInstance(languageDropdownField);
        const extraActionsSidebar = document.querySelector('.ibexa-extra-actions--create');
        const openExtraActionsBtn = document.querySelector('.ibexa-btn--create.ibexa-btn--extra-actions');
        const extraActionsSubtitle = extraActionsSidebar.querySelector('.ibexa-extra-actions__header-subtitle');
        const originalId = entryIdField.value;
        const originalSubtitle = extraActionsSubtitle.innerHTML;
        const originalLanguage = languageField.value;
        const newExtraActionsSubtitleContent = extraActionsSubtitle.dataset.template.replace('{{ name }}', item.internalItem.name);
        const revertOriginalFormState = () => {
            entryIdField.value = originalId;
            extraActionsSubtitle.innerHTML = originalSubtitle;
            languageDropdownInstance.selectOption(originalLanguage);

            document.body.removeEventListener('ibexa-extra-actions:after-close', revertOriginalFormState, false);
        };

        entryIdField.value = item.id;
        extraActionsSubtitle.innerHTML = newExtraActionsSubtitleContent;
        languageDropdownInstance.selectOption(languageCode);

        setTimeout(() => {
            openExtraActionsBtn.click();

            document.body.addEventListener('ibexa-extra-actions:after-close', revertOriginalFormState, false);
        }, 0);
    };

    return <ActionItem label={itemLabel} isDisabled={isDisabled} onClick={addTag} />;
};

AddTag.propTypes = {
    item: PropTypes.object,
};

AddTag.defaultProps = {
    item: {},
};

export default AddTag;
