/**
 * Checks whether a given item is an object.
 *
 * @function isObject
 * @param {Any} item
 * @returns {Boolean}
 */
const isObject = (item) => item instanceof Object && item.constructor === Object;

/**
 * Serializes object into a string format
 *
 * @function serializeObject
 * @param {String} key param key
 * @param {Object} param reguest param
 * @returns {String}
 */
const serializeObject = (key, param) => {
    return Object.entries(param)
        .reduce((requestParameters, [paramKey, paramValue]) => {
            return [...requestParameters, serializeByType(`${key}[${paramKey}]`, paramValue)];
        }, [])
        .join('&');
};

/**
 * Serializes array into a string format
 *
 * @function serializeArray
 * @param {String} key param key
 * @param {Array} param reguest param
 * @returns {String}
 */
const serializeArray = (key, param) => {
    return param.map((paramValue, index) => serializeByType(`${key}[${index}]`, paramValue)).join('&');
};

/**
 * Serializes data by type
 *
 * @function serializeByType
 * @param {String} key param key
 * @param {Mixed} param reguest param
 * @returns {String}
 */
const serializeByType = (key, value) => {
    if (isObject(value)) {
        return serializeObject(key, value);
    } else if (Array.isArray(value)) {
        return serializeArray(key, value);
    }
    return `${key}=${value}`;
};

/**
 * Serializes request params into a string format
 *
 * @function serialize
 * @param {Object} params request params
 * @returns {String}
 */
const serialize = (params) => {
    return Object.keys(params)
        .map((key) => {
            const value = params[key];

            if (isObject(value)) {
                return serializeObject(key, value);
            }

            if (Array.isArray(value)) {
                return serializeArray(key, value);
            }

            return `${encodeURIComponent(key)}=${encodeURIComponent(value)}`;
        })
        .join('&');
};

export default serialize;
