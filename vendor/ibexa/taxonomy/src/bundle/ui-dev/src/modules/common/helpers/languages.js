export const hasTranslation = (entry, languageCode) => !!entry.names?.[languageCode];

export const getTranslatedName = (entry, languageCode) => {
    if (hasTranslation(entry, languageCode)) {
        return entry.names?.[languageCode];
    }

    return entry.name;
};
