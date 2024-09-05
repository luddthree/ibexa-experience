import { useContext } from 'react';

import { BuildItemContext } from '../tree.builder.module';

export default (...args) => {
    const buildItem = useContext(BuildItemContext);

    return buildItem(...args);
};
