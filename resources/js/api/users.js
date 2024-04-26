import request from './request';

/**
 * Get an array of all users
 * @returns {Promise<Array<{ id: number, forename: string, surname: string }>>}
 */
export async function getAll() {
    const resp = await request('/api/users');
    return resp.json();
}

/**
 * Get an array of users with POS access
 * @returns {Promise<Array<{ id: number, forename: string, surname: string, booking_authoriser_user_id: number }>>}
 */
export async function getUsersWithPosAccess() {
    const resp = await request('/api/users/pos');
    return resp.json();
}
