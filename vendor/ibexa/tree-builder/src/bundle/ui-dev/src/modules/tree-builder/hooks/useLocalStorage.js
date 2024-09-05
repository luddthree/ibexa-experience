import { useContext } from 'react';

import { getData, saveData } from '../helpers/localStorage';
import { ModuleIdContext, UserIdContext, SubIdContext } from '../tree.builder.module';

export default (dataId, subIdOverride) => {
    const moduleId = useContext(ModuleIdContext);
    const userId = useContext(UserIdContext);
    const subIdFromContext = useContext(SubIdContext);
    const subId = subIdOverride ?? subIdFromContext;
    const getLocalStorageData = () => getData({ moduleId, userId, subId, dataId });
    const saveLocalStorageData = (data, shouldDispatchSaveEvent = false) =>
        saveData({ moduleId, userId, subId, dataId, data }, shouldDispatchSaveEvent);

    return {
        getLocalStorageData,
        saveLocalStorageData,
    };
};
