import request from './request';

/**
 * Get an array of all distribution groups
 * @returns {Promise<Array<{id: number, name: string}>>}
 */
export async function getAll() {
    const resp = await request('/api/distributionGroups');
    return resp.json();
}
