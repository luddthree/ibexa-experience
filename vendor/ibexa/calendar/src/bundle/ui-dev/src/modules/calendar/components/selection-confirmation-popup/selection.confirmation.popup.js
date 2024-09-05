import React, { useContext, useMemo } from 'react';
import { EventsSelectionContext } from '../../../calendar.module';
import Popup from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/popup/popup.component';

const { Translator } = window;

const SelectionConfirmationPopup = () => {
    const [eventsSelection, dispatchSelectEventAction] = useContext(EventsSelectionContext);
    const isPopupVisible = eventsSelection.selectionToBeConfirmed !== null;
    const handleSelectionReject = () => dispatchSelectEventAction({ type: 'REJECT_SELECTION' });
    const handleSelectionConfirm = () => dispatchSelectEventAction({ type: 'CONFIRM_SELECTION' });
    const cancelBtnLabel = Translator.trans(
        /*@Desc("Cancel")*/ 'calendar.selection_confirmation_popup.cancel.btn',
        {},
        'ibexa_calendar_widget',
    );
    const confirmBtnLabel = Translator.trans(
        /*@Desc("Refresh list")*/ 'calendar.selection_confirmation_popup.confirm.btn',
        {},
        'ibexa_calendar_widget',
    );
    const actionBtnsConfig = useMemo(() => {
        const confirmBtnAttrs = {
            label: confirmBtnLabel,
            onClick: handleSelectionConfirm,
            className: 'ibexa-btn--primary ibexa-btn--trigger font-weight-bold',
        };
        const cancelBtnAttrs = {
            label: cancelBtnLabel,
            onClick: handleSelectionReject,
            className: 'ibexa-btn--secondary ibexa-btn--no',
            'data-dismiss': 'modal',
        };

        return [confirmBtnAttrs, cancelBtnAttrs];
    }, [handleSelectionReject, handleSelectionConfirm]);
    const popupTitle = Translator.trans(
        /*@Desc("Conflict when selecting items")*/ 'calendar.selection_confirmation_popup.no_events',
        {},
        'ibexa_calendar_widget',
    );
    const popupMessage = Translator.trans(
        /*@Desc("This event has different actions from the items currently selected in the list.")*/ 'calendar.selection_confirmation_popup.message',
        {},
        'ibexa_calendar_widget',
    );
    const popupActionMessage = Translator.trans(
        /*@Desc("To continue, click 'Refresh list'. The selected list will update and the new item will be added to the list")*/ 'calendar.list_view.action_message',
        {},
        'ibexa_calendar_widget',
    );

    return (
        <Popup
            onClose={handleSelectionReject}
            isVisible={isPopupVisible}
            size="medium"
            title={popupTitle}
            additionalClasses="c-selection-confirmation-popup"
            actionBtnsConfig={actionBtnsConfig}
        >
            <div className="c-selection-confirmation-popup__modal-body">
                <p>{popupMessage}</p>
                <p>{popupActionMessage}</p>
            </div>
        </Popup>
    );
};

SelectionConfirmationPopup.propTypes = {};

export default SelectionConfirmationPopup;
