import request from './request';

/**
 * Get an array of all users
 * @returns {Promise<Array<{id: number, email: string, forename: string, surname: string}>>}
 */
export async function getAll() {
    const resp = await request('/api/users');
    return resp.json();
}
