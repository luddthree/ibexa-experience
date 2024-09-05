import React from 'react';
import PropTypes from 'prop-types';

import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';

const { Translator } = window;

const ConfirmModal = (props) => {
    return (
        <div className="modal ibexa-modal ibexa-image-editor-confirm-modal">
            <div className="modal-dialog">
                <div className="modal-content">
                    <div className="modal-header">
                        <h5 className="modal-title">
                            {Translator.trans(/*@Desc("Save as...")*/ 'image_editor.save_as', {}, 'ibexa_image_editor')}
                        </h5>
                        <button type="button" className="close" onClick={() => props.cancel()}>
                            <Icon name="discard" extraClasses="ibexa-icon--small-medium" />
                        </button>
                    </div>
                    <div className="modal-body">
                        <p>
                            {Translator.trans(
                                /*@Desc("Clicking 'Save as new' will create a copy of the modified image.")*/ 'image_editor.save_as.warning',
                                {},
                                'ibexa_image_editor',
                            )}
                        </p>
                        <p>
                            {Translator.trans(
                                /*@Desc("If you click 'Apply changes to all' you will modify this image which is used in other Content items.")*/ 'image_editor.save_as.sub_warning',
                                {},
                                'ibexa_image_editor',
                            )}
                        </p>
                    </div>
                    <div className="modal-footer">
                        <div className="ibexa-image-editor-confirm-modal__confirm-wrapper">
                            <button type="button" className="btn ibexa-btn ibexa-btn--primary" onClick={() => props.confirm(true)}>
                                {Translator.trans(/*@Desc("Save as new")*/ 'image_editor.save_as_new', {}, 'ibexa_image_editor')}
                            </button>
                            <button type="button" className="btn ibexa-btn ibexa-btn--secondary" onClick={() => props.confirm()}>
                                {Translator.trans(/*@Desc("Apply changes to all")*/ 'image_editor.apply_to_all', {}, 'ibexa_image_editor')}
                            </button>
                        </div>

                        <button type="button" className="btn ibexa-btn ibexa-btn--secondary" onClick={() => props.cancel()}>
                            {Translator.trans(/*@Desc("Cancel")*/ 'image_editor.cancel', {}, 'ibexa_image_editor')}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};

ConfirmModal.propTypes = {
    confirm: PropTypes.func.isRequired,
    cancel: PropTypes.func.isRequired,
};

ConfirmModal.defaultProps = {};

export default ConfirmModal;
