import request from './request';

/**
 * @param {{
 *  title: string
 *  startDateTime: number,
 *  endDateTime: number,
 *  user: number,
 *  location: number,
 *  assets: Array<{id: number, returned: boolean}>,
 *  groups: Array<{id: number, quantity: number}>,
 *  details: string
 * }} data
 */
export function create(data) {
    const { title, startDateTime, endDateTime, user, location, assets, groups, details } = data;

    return request('/api/setups', {
        method: 'POST',
        body: JSON.stringify({
            title,
            startDateTime,
            endDateTime,
            user,
            location,
            assets,
            groups,
            details
        })
    });
}

/**
 * @param {number} id Loan ID
 * @param {{
 *  title: string
 *  startDateTime: number,
 *  endDateTime: number,
 *  user: number,
 *  location: number,
 *  assets: Array<{id: number, returned: boolean}>,
 *  groups: Array<{id: number, quantity: number}>,
 *  details: string
 * }} data
 */
export function update(id, data) {
    const { title, startDateTime, endDateTime, user, location, assets, groups, details } = data;

    return request('/api/setups/'+id, {
        method: 'PUT',
        body: JSON.stringify({
            title,
            startDateTime,
            endDateTime,
            user,
            location,
            assets,
            groups,
            details
        })
    });
}

/**
 * @param {number} id Loan ID
 * @param {{
*  title: string
*  startDateTime: number,
*  endDateTime: number,
*  user: number,
*  location: number,
*  assets: Array<{id: number, returned: boolean}>,
*  groups: Array<{id: number, quantity: number}>,
*  details: string
* }} data
*/
export function patch(id, data) {
    const { title, startDateTime, endDateTime, user, location, assets, groups, details } = data;

    return request('/api/setups/'+id, {
        method: 'PATCH',
        body: JSON.stringify({
            title,
            startDateTime,
            endDateTime,
            user,
            location,
            assets,
            groups,
            details
        })
    });
}