import request from './request';

/**
 * Get an array of all locations
 * @returns {Promise<Array<{id: number, email: string, forename: string, surname: string}>>}
 */
export async function getAll() {
    const resp = await request('/api/locations');
    return resp.json();
}
