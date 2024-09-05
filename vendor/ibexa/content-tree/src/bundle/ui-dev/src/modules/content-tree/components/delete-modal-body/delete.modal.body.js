import React from 'react';
import PropTypes from 'prop-types';

import { getTranslator, isExternalInstance } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';

const DeleteBodyModal = ({
    reverseRelationsCount,
    totalSubitemsCount,
    name,
    confirmationChecked,
    setConfirmationChecked,
    isDeletingUser,
}) => {
    const Translator = getTranslator();
    const reverseRelationHeader = Translator.trans(
        /*@Desc("Conflict with reverse Relations")*/ 'modal.delete.reverse_relation.header',
        {},
        'ibexa_content_tree_ui',
    );
    const reverseRelationMessage = Translator.trans(
        /*@Desc("'%name%' is in use by %reverseRelationsCount% Content item(s). You should remove all reverse Relations before deleting the Content item.")*/ 'modal.delete.reverse_relation.message',
        {
            name,
            reverseRelationsCount,
        },
        'ibexa_content_tree_ui',
    );
    const subitemsHeader = Translator.trans(/*@Desc("Sub-items")*/ 'modal.delete.subitems.header', {}, 'ibexa_content_tree_ui');
    const subitemsMessage = isExternalInstance()
        ? Translator.trans(
              /*@Desc("Deleting '%name%' and its %totalSubitemsCount% Content item(s) will also delete the sub-items of this Location.")*/ 'modal.delete.subitems.message',
              {
                  name,
                  totalSubitemsCount,
              },
              'ibexa_content_tree_ui',
          )
        : Translator.trans(
              /*@Desc("Sending '%name%' and its %totalSubitemsCount% Content item(s) to Trash will also send the sub-items of this Location to Trash.")*/ 'modal.delete.subitems.trash_message',
              {
                  name,
                  totalSubitemsCount,
              },
              'ibexa_content_tree_ui',
          );
    const confirmMessage = Translator.trans(
        /*@Desc("I understand the consequences of this action.")*/ 'modal.delete.confirm.message',
        {},
        'ibexa_content_tree_ui',
    );
    const changeConfirmBtnState = () => {
        setConfirmationChecked((prevState) => !prevState);
    };
    const renderHeader = () => {
        if (isExternalInstance() && !isDeletingUser) {
            return Translator.trans(
                /*@Desc("Are you sure you want to delete this Content?")*/ 'modal.delete.header.content',
                {},
                'ibexa_content_tree_ui',
            );
        }

        if (isDeletingUser) {
            return Translator.trans(
                /*@Desc("Are you sure you want to delete this User?")*/ 'modal.delete.header.user',
                {},
                'ibexa_content_tree_ui',
            );
        }

        return Translator.trans(
            /*@Desc("Are you sure you want to send this Content item to Trash?")*/ 'modal.delete.header.trash_content',
            {},
            'ibexa_content_tree_ui',
        );
    };
    const renderReverseRelationWarning = () => {
        if (reverseRelationsCount === 0) {
            return null;
        }

        return (
            <div className="ibexa-modal__trash-option">
                <p className="ibexa-modal__option-label">
                    <label className="ibexa-label form-label required">{reverseRelationHeader}</label>
                </p>
                <p className="ibexa-modal__option-description">{reverseRelationMessage}</p>
            </div>
        );
    };
    const renderSubitemsWarning = () => {
        if (totalSubitemsCount === 0) {
            return null;
        }

        return (
            <div className="ibexa-modal__trash-option">
                <p className="ibexa-modal__option-label">
                    <label className="ibexa-label form-label required">{subitemsHeader}</label>
                </p>
                <p className="ibexa-modal__option-description">{subitemsMessage}</p>
            </div>
        );
    };
    const renderConfirmation = () => {
        if (reverseRelationsCount === 0 && totalSubitemsCount === 0) {
            return null;
        }

        return (
            <div className="form-check">
                <input
                    onChange={changeConfirmBtnState}
                    checked={confirmationChecked}
                    type="checkbox"
                    id="location_trash_confirm"
                    className="ibexa-input ibexa-input--checkbox  form-check-input"
                />
                <label className="ibexa-label ibexa-label--checkbox-radio form-check-label" htmlFor="location_trash_confirm">
                    {confirmMessage}
                </label>
            </div>
        );
    };

    return (
        <>
            {renderHeader()}
            {renderReverseRelationWarning()}
            {renderSubitemsWarning()}
            {renderConfirmation()}
        </>
    );
};

DeleteBodyModal.propTypes = {
    name: PropTypes.string.isRequired,
    confirmationChecked: PropTypes.bool.isRequired,
    setConfirmationChecked: PropTypes.func.isRequired,
    isDeletingUser: PropTypes.bool,
    reverseRelationsCount: PropTypes.number,
    totalSubitemsCount: PropTypes.number,
};

DeleteBodyModal.defaultProps = {
    isDeletingUser: false,
    reverseRelationsCount: 0,
    totalSubitemsCount: 0,
};

export default DeleteBodyModal;
