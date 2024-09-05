export const getImageFormatFromMimeType = (imageMimeType) => imageMimeType.match(/image\/([a-zA-Z-]+)(\+.*)*$/)[1];
