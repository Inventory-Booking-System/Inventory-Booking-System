import request from './request';

/**
 * Get an array of users shown on the POS staff screen
 * @returns {Promise<Array<{ id: number, forename: string, surname: string }>>}
 */
export async function getAll() {
    const resp = await request('/api/users');
    return resp.json();
}

/**
 * Get an array of users shown on the POS student screen
 * @returns {Promise<Array<{ id: number, forename: string, surname: string, booking_authoriser_user_id: number }>>}
 */
export async function getUsersWithPosAccess() {
    const resp = await request('/api/users/pos');
    return resp.json();
}
