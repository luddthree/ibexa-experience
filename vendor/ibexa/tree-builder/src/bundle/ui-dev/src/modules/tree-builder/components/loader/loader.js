import React, { useContext } from 'react';

import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';

import { WidthContainerContext, checkIsTreeCollapsed } from '../width-container/width.container';

const Loader = () => {
    const [{ containerWidth }] = useContext(WidthContainerContext);
    const isCollapsed = checkIsTreeCollapsed(containerWidth);

    if (isCollapsed) {
        return null;
    }

    return (
        <div className="c-tb-loader">
            <Icon name="spinner" extraClasses="ibexa-spin ibexa-icon--medium" />
        </div>
    );
};

export default Loader;
