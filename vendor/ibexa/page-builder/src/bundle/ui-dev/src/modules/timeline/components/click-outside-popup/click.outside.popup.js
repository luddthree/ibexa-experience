import React, { forwardRef, useCallback, useEffect } from 'react';
import PropTypes from 'prop-types';

const ClickOutsidePopup = forwardRef(({ onClickOutside, isPopupExpanded, className, children }, containerRef) => {
    const handleDocumentClick = useCallback(
        (event) => {
            const isClickOutside = !containerRef.current.contains(event.target);

            if (isClickOutside) {
                onClickOutside();
            }
        },
        [onClickOutside, containerRef],
    );
    const handleIframeClick = useCallback(() => {
        const isClickOutside = !containerRef.current.contains(document.activeElement);

        if (isClickOutside) {
            onClickOutside();
        }
    }, [onClickOutside, containerRef]);

    useEffect(() => {
        if (isPopupExpanded) {
            document.addEventListener('click', handleDocumentClick, false);
            window.addEventListener('blur', handleIframeClick, false);
        }

        return () => {
            document.removeEventListener('click', handleDocumentClick, false);
            window.removeEventListener('blur', handleIframeClick, false);
        };
    }, [handleDocumentClick, handleIframeClick, isPopupExpanded]);

    return (
        <div className={className} ref={containerRef}>
            {children}
        </div>
    );
});

ClickOutsidePopup.propTypes = {
    onClickOutside: PropTypes.func.isRequired,
    isPopupExpanded: PropTypes.bool.isRequired,
    className: PropTypes.string.isRequired,
    children: PropTypes.element.isRequired,
};

ClickOutsidePopup.displayName = 'ClickOutsidePopup';

export default ClickOutsidePopup;
