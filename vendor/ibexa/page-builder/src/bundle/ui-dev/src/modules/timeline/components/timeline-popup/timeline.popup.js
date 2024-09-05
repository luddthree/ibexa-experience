import React, { useCallback, useEffect, useRef, useState } from 'react';
import PropTypes from 'prop-types';
import ClickOutsidePopup from '../click-outside-popup/click.outside.popup';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

const { bootstrap } = window;

export const TimelinePopupContext = React.createContext();

const TimelinePopup = ({ extraClasses, children }) => {
    const [isPopupExpanded, setIsPopupExpanded] = useState(false);
    const containerRef = useRef(null);
    const closePopup = useCallback(() => setIsPopupExpanded(false), [setIsPopupExpanded]);
    const togglePopup = useCallback(() => setIsPopupExpanded(!isPopupExpanded), [isPopupExpanded]);
    const className = createCssClassNames({
        'c-pb-timeline-popup': true,
        'c-pb-timeline-popup--popup-expanded': isPopupExpanded,
        [extraClasses]: true,
    });

    useEffect(() => {
        const elementsWithTooltip = containerRef.current?.querySelectorAll('[data-original-title]');

        elementsWithTooltip.forEach((element) => {
            const tooltip = bootstrap.Tooltip.getInstance(element);

            tooltip?.hide();
        });
    }, [isPopupExpanded]);

    return (
        <ClickOutsidePopup ref={containerRef} className={className} onClickOutside={closePopup} isPopupExpanded={isPopupExpanded}>
            <TimelinePopupContext.Provider value={{ closePopup, togglePopup }}>{children}</TimelinePopupContext.Provider>
        </ClickOutsidePopup>
    );
};

TimelinePopup.propTypes = {
    children: PropTypes.node.isRequired,
    extraClasses: PropTypes.string,
};

TimelinePopup.defaultProps = {
    extraClasses: '',
};
export default TimelinePopup;
