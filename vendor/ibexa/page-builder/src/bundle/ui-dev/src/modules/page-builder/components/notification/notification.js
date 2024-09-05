import React from 'react';
import PropTypes from 'prop-types';

import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';

export const NOTIFICATION_TYPE = {
    ERROR: 'error',
    WARNING: 'warning',
};
const NOTIFICATION_ICON_NAMES = {
    ERROR: 'warning',
    WARNING: 'notice',
};

const Notification = ({ message, type, onClose, noCloseBtn }) => {
    const notificationTypeProperty = Object.keys(NOTIFICATION_TYPE).find((key) => NOTIFICATION_TYPE[key] === type);
    const iconName = NOTIFICATION_ICON_NAMES[notificationTypeProperty];

    return (
        <div className={`c-pb-notification c-pb-notification--${type}`}>
            <Icon name={iconName} extraClasses={`c-pb-notification__icon ibexa-icon--small`} />
            <div className={`c-pb-notification__message`}>{message}</div>
            {!noCloseBtn && (
                <div className="c-pb-notification__close-btn-container">
                    <button type="button" className={`c-pb-notification__close-btn btn ibexa-btn ibexa-btn--no-text`} onClick={onClose}>
                        <Icon name="discard" extraClasses={`c-pb-notification__close-btn-icon ibexa-icon--tiny-small`} />
                    </button>
                </div>
            )}
        </div>
    );
};

Notification.propTypes = {
    message: PropTypes.string.isRequired,
    type: PropTypes.oneOf(Object.values(NOTIFICATION_TYPE)).isRequired,
    onClose: PropTypes.func,
    noCloseBtn: PropTypes.bool,
};

Notification.defaultProps = {
    onClose: () => {},
    noCloseBtn: false,
};

export default Notification;
