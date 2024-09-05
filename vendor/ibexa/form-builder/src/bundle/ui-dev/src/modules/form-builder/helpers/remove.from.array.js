const removeFromArray = (fields, callback) => {
    const fieldIndex = fields.findIndex(callback);

    if (fieldIndex !== -1) {
        fields.splice(fieldIndex, 1);
    }

    return fields;
};

export default removeFromArray;
