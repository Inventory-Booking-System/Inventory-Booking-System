import request from './request';

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
