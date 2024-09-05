import serialize from '../../helpers/serialize';

/**
 * Gets block preview
 *
 * @function getBlockPreview
 * @param {Object} body request data
 * @returns {Promise} Fetch promise
 */
export const getBlockPreview = (body, targetSiteaccess) => {
    const url = window.Routing.generate('ibexa.page_builder.block.siteaccess_preview', { siteaccessName: targetSiteaccess });
    const token = document.querySelector('meta[name="CSRF-Token"]').content;
    const request = new Request(url, {
        method: 'POST',
        headers: {
            'X-CSRF-Token': token,
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
        },
        body: serialize(body),
        mode: 'same-origin',
        credentials: 'same-origin',
    });

    return fetch(request);
};
