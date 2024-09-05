const LOCAL_STORAGE_ID = 'ibexa-image-picker';

export default (key) => {
    const getData = () => {
        const dataStringified = localStorage.getItem(LOCAL_STORAGE_ID);
        const data = dataStringified ? JSON.parse(dataStringified) : {};

        return data[key];
    };

    const saveData = (dataToSave) => {
        const dataStringified = localStorage.getItem(LOCAL_STORAGE_ID);
        const data = dataStringified ? JSON.parse(dataStringified) : {};

        data[key] = dataToSave;

        localStorage.setItem(LOCAL_STORAGE_ID, JSON.stringify(data));
    };

    return {
        getLocalStorageData: getData,
        saveLocalStorageData: saveData,
    };
};
