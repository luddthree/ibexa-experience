import React, { useContext } from 'react';
import PropTypes from 'prop-types';

import { getContentTypeIconUrl } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/content.type.helper';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import SortSwitcher from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/universal-discovery/components/sort-switcher/sort.switcher';

import ViewSwitcher from '../view-switcher/view.switcher';
import { FiltersContext } from '../../image.picker.tab.module';

const ItemsViewTopBar = ({ activeView, onViewChange }) => {
    const { selectedLocationData } = useContext(FiltersContext);
    const contentTypeIconUrl = getContentTypeIconUrl(selectedLocationData.contentTypeIdentifier);

    return (
        <div className="c-ip-items-view-top-bar">
            <h3 className="c-ip-items-view-top-bar__title">
                <Icon
                    customPath={contentTypeIconUrl}
                    name={selectedLocationData.contentTypeIdentifier}
                    extraClasses="ibexa-icon--medium c-ip-items-view-top-bar__content-type-icon"
                />
                <span className="c-ip-items-view-top-bar__title-text">{selectedLocationData?.name}</span>
            </h3>
            <div className="c-ip-items-view-top-bar__actions">
                <SortSwitcher />
                <ViewSwitcher onViewChange={onViewChange} activeView={activeView} />
            </div>
        </div>
    );
};

ItemsViewTopBar.propTypes = {
    activeView: PropTypes.string.isRequired,
    onViewChange: PropTypes.func.isRequired,
};

export default ItemsViewTopBar;
