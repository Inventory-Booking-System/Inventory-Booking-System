import request from './request';

/**
 * Get an array of all equipment issues
 * @returns {Promise<Array<{id: number, name: string}>>}
 */
export async function getAll() {
    const resp = await request('/api/equipmentIssues');
    return resp.json();
}
