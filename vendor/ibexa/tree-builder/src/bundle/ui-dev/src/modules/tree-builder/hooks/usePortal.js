import { useContext } from 'react';

import { PortalContext } from '../components/portal-provider/portal.provider';

/**
 * @deprecated This hook is deprecated and will be removed in 5.0 use Portal instead
 */
export default (portalId) => {
    console.warn('Hook usePortal is deprecated and will be removed in 5.0 use Portal instead');

    const portals = useContext(PortalContext);

    return portals[portalId];
};
