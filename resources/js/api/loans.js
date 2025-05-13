import request from './request';

export const Status = {
    BOOKED: 0,
    RESERVATION: 1,
    OVERDUE: 2,
    SETUP: 3,
    CANCELLED: 4,
    COMPLETED: 5,
    MODIFIED: 6
};

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
 *  groups: Array<{id: number, quantity: number}>,
 *  details: string,
 *  reservation: boolean
 * }} data
 */
export async function create(data) {
    const { startDateTime, endDateTime, user, assets = [], groups = [], details, reservation } = data;

    return await request('/api/loans', {
        method: 'POST',
        body: JSON.stringify({
            startDateTime,
            endDateTime,
            user,
            assets,
            groups,
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
 *  groups: Array<{id: number, quantity: number}>,
 *  details: string,
 *  reservation: boolean
 * }} data
 */
export async function update(id, data) {
    const { startDateTime, endDateTime, user, assets, groups, details, reservation } = data;

    return await request('/api/loans/'+id, {
        method: 'PUT',
        body: JSON.stringify({
            startDateTime,
            endDateTime,
            user,
            assets,
            groups,
            details,
            reservation
        })
    });
}

export async function getReservations() {
    const resp = await request('/api/loans/reservations');
    return await resp.json();
}
