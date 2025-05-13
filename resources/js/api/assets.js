import request from './request';

/**
 * Gets the asset with the specified id
 * @param {*} id
 */
export async function get(id) {
    const resp = await request(`/api/assets/${id}`);
    return await resp.json();
}

/**
 * Gets all assets, with availability data between startDateTime and endDateTime
 * @param {{
 *  startDateTime: number,
 *  endDateTime: number
 * }} data
 */
export async function getAll(data) {
    const { startDateTime, endDateTime } = data;

    const params = new URLSearchParams({ startDateTime, endDateTime });
    const resp = await request('/api/assets?'+params);
    return await resp.json();
}

/**
 * Book in the asset from any existing loans or setups. Whitespace is trimmed
 * and leading 0s are removed.
 * @param {{
 *  tag: number
 * }} data
 */
export async function scanIn({ tag }) {
    const resp = await request(`/api/assets/${tag}/scan/in`);
    const body = await resp.json();
    if (!resp.ok) {
        throw body;
    }
    return body;
}