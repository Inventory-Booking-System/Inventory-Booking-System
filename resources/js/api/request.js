import { getCookie } from '../utils/cookie';

/**
 * @param {RequestInfo | URL} input
 * @param {RequestInit} init
 * @returns {Promise<Response>}
 */
export default async function request(input, init) {
    return await fetch(input, {
        ...init,
        headers: {
            'Accept': 'application/json',
            'Content-Type': init && init.body ? 'application/json' : undefined,
            'X-XSRF-Token': decodeURIComponent(getCookie('XSRF-TOKEN'))
        }
    });
}