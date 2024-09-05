import { useEffect, useRef, forwardRef, useImperativeHandle } from 'react';
import { createPortal } from 'react-dom';
import PropTypes from 'prop-types';

import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';
import { getRootDOMElement } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';

const Portal = forwardRef(({ children, getPosition, extraClasses }, ref) => {
    const rootDOMElement = getRootDOMElement();
    const className = createCssClassNames({
        'c-tb-portal c-tb-element': true,
        [extraClasses]: extraClasses !== '',
    });
    const portalRef = useRef(null);
    const prevPosition = useRef({ x: null, y: null });
    const setPortalPosition = (portalPosition) => {
        const { x, y } = portalPosition ?? getPosition();

        if (x !== prevPosition.current.x || y !== prevPosition.current.y) {
            prevPosition.current = { x, y };

            portalRef.current.style.left = `${x}px`;
            portalRef.current.style.top = `${y}px`;
        }
    };

    if (!portalRef.current) {
        portalRef.current = document.createElement('div');
        rootDOMElement.insertAdjacentElement('beforeend', portalRef.current);
    }

    useImperativeHandle(ref, () => ({ setPortalPosition }), []);

    useEffect(() => {
        return () => {
            portalRef.current.remove();
            portalRef.current = null;
        };
    }, []);

    useEffect(() => {
        portalRef.current.className = '';
        portalRef.current.classList.add(...className.split(' '));
    }, [extraClasses]);

    return createPortal(children, portalRef.current);
});

Portal.propTypes = {
    children: PropTypes.node.isRequired,
    getPosition: PropTypes.func,
    extraClasses: PropTypes.string,
};

Portal.defaultProps = {
    extraClasses: '',
    getPosition: () => ({}),
};

Portal.displayName = 'Portal';

export default Portal;
