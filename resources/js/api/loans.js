import request from './request';

export async function getAll() {
    const resp = await request('/api/loans');
    return await resp.json();
}

/**
 * @param {{
 *  startDateTime: number,
 *  endDateTime: number,
 *  user: number,
 *  assets: Array<{id: number, returned: boolean}>,
 *  details: string,
 *  reservation: boolean
 * }} data
 */
export async function create(data) {
    const { startDateTime, endDateTime, user, assets, details, reservation } = data;

    return await request('/api/loans', {
        method: 'POST',
        body: JSON.stringify({
            startDateTime,
            endDateTime,
            user,
            assets,
            details,
            reservation
        })
    });
}

/**
 * @param {number} id Loan ID
 * @param {{
 *  startDateTime: number,
 *  endDateTime: number,
 *  user: number,
 *  assets: Array<{id: number, returned: boolean}>,
 *  details: string,
 *  reservation: boolean
 * }} data
 */
export async function update(id, data) {
    const { startDateTime, endDateTime, user, assets, details, reservation } = data;

    return await request('/api/loans/'+id, {
        method: 'PUT',
        body: JSON.stringify({
            startDateTime,
            endDateTime,
            user,
            assets,
            details,
            reservation
        })
    });
}