/**
 * Generates a GUID string.
 *
 * @function generateGuid
 * @param prefix {String} the id prefix
 * @return {String} The generated GUID.
 *
 * @example b-af8a8416-6e18-a307-bd9c-f2c947bbb3aa
 * @author Slavik Meltser (http://slavik.meltser.info/?p=142)
 */
const generateGuid = (prefix = 'guid-') => {
    return [prefix, makeGuidPiece(), makeGuidPiece(true), makeGuidPiece(true), makeGuidPiece()].join('');
};

/**
 * Generates GUID piece
 * @param addDashes {Boolean} add dashes flag
 * @return {String} generated piece of guid
 */
const makeGuidPiece = (addDashes) => {
    const piece = `${Math.random().toString(16)}000000000`.substr(2, 8);

    return addDashes ? `-${piece.substr(0, 4)}-${piece.substr(4, 4)}` : piece;
};

export default generateGuid;
