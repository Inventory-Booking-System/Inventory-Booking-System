import { getCookie } from '../utils/cookie';

/**
 * @param {RequestInfo | URL} input
 * @param {RequestInit} init
 * @returns {Promise<Response>}
 */
export default async function request(input, init) {
    const resp = await fetch(input, {
        ...init,
        headers: {
            'Accept': 'application/json',
            'Content-Type': init && init.body ? 'application/json' : undefined,
            'X-XSRF-Token': decodeURIComponent(getCookie('XSRF-TOKEN'))
        }
    });
    if (resp.status === 401) {
        window.location.reload();
        return;
    }
    return resp;
}