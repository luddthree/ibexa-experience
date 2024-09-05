export const SAVE_ITEM_EVENT = 'ibexa-tree-builder:local-storage-save';

const LOCAL_STORAGE_ID = 'ibexa-tree-builder';

export const getData = ({ moduleId, userId, subId = 'default', dataId }) => {
    const dataStringified = localStorage.getItem(LOCAL_STORAGE_ID);
    const data = dataStringified ? JSON.parse(dataStringified) : {};

    if (!data[moduleId]) {
        data[moduleId] = {};
    }

    if (!data[moduleId][userId]) {
        data[moduleId][userId] = {};
    }

    if (!data[moduleId][userId][subId]) {
        data[moduleId][userId][subId] = {};
    }

    return data[moduleId][userId][subId][dataId];
};

export const saveData = ({ moduleId, userId, subId = 'default', dataId, data: dataToSave }, shouldDispatchSaveEvent = false) => {
    const dataStringified = localStorage.getItem(LOCAL_STORAGE_ID);
    const data = dataStringified ? JSON.parse(dataStringified) : {};

    if (!data[moduleId]) {
        data[moduleId] = {};
    }

    if (!data[moduleId][userId]) {
        data[moduleId][userId] = {};
    }

    if (!data[moduleId][userId][subId]) {
        data[moduleId][userId][subId] = {};
    }

    data[moduleId][userId][subId][dataId] = dataToSave;

    localStorage.setItem(LOCAL_STORAGE_ID, JSON.stringify(data));

    if (shouldDispatchSaveEvent) {
        window.document.dispatchEvent(new CustomEvent(SAVE_ITEM_EVENT));
    }
};
