import React from 'react';
import PropTypes from 'prop-types';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

const TimelinePopupContainer = ({ children, extraClasses, scrollWrapperExtraClasses }) => {
    const className = createCssClassNames({
        'c-pb-timeline-popup__container': true,
        [extraClasses]: true,
    });
    const scrollWrapperClassName = createCssClassNames({
        'c-pb-timeline-popup__scroll-wrapper': true,
        [scrollWrapperExtraClasses]: true,
    });

    return (
        <div className={className}>
            <div className={scrollWrapperClassName}>{children}</div>
        </div>
    );
};

TimelinePopupContainer.propTypes = {
    children: PropTypes.element.isRequired,
    extraClasses: PropTypes.string,
    scrollWrapperExtraClasses: PropTypes.string,
};

TimelinePopupContainer.defaultProps = {
    extraClasses: '',
    scrollWrapperExtraClasses: '',
};

export default TimelinePopupContainer;
