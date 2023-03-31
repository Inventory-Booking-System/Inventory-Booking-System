import request from './request';

/**
 * @param {{
 *  title: string
 *  startDateTime: number,
 *  endDateTime: number,
 *  user: number,
 *  location: number,
 *  assets: Array<{id: number, returned: boolean}>,
 *  details: string
 * }} data
 */
export function create(data) {
    const { title, startDateTime, endDateTime, user, location, assets, details } = data;

    return request('/api/setups', {
        method: 'POST',
        body: JSON.stringify({
            title,
            startDateTime,
            endDateTime,
            user,
            location,
            assets,
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
 *  details: string
 * }} data
 */
export function update(id, data) {
    const { title, startDateTime, endDateTime, user, location, assets, details } = data;

    return request('/api/setups/'+id, {
        method: 'PUT',
        body: JSON.stringify({
            title,
            startDateTime,
            endDateTime,
            user,
            location,
            assets,
            details
        })
    });
}