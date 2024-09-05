import React, { useEffect, useRef, useState } from 'react';
import PropTypes from 'prop-types';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

const ConfigPanelFooter = ({ children, extraClasses, isClosed }) => {
    const [isSlimFooter, setIsSlimFooter] = useState(false);
    const footerRef = useRef(null);
    const fitFooter = () => {
        const content = footerRef.current.previousElementSibling;

        if (!content) {
            return;
        }

        const hasVerticalScrollbar = content.scrollHeight > content.clientHeight;

        setIsSlimFooter(hasVerticalScrollbar);
    };
    const className = createCssClassNames({
        'ibexa-pb-config-panel__footer': true,
        'ibexa-pb-config-panel__footer--slim': isSlimFooter,
        [extraClasses]: true,
    });

    useEffect(() => {
        window.addEventListener('resize', fitFooter, false);
        fitFooter();

        return () => {
            window.removeEventListener('resize', fitFooter, false);
        };
    }, [isClosed]);

    return (
        <div ref={footerRef} className={className}>
            {children}
        </div>
    );
};

ConfigPanelFooter.propTypes = {
    children: PropTypes.element,
    extraClasses: PropTypes.string,
    isClosed: PropTypes.bool.isRequired,
};

ConfigPanelFooter.defaultProps = {
    children: null,
    extraClasses: '',
};

export default ConfigPanelFooter;
