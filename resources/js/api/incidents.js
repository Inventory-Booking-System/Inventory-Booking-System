import request from './request';

/**
 * @param {{
 *  startDateTime: number,
 *  distributionGroup: number,
 *  location: number,
 *  equipmentIssues: Array<{id: number, quantity: number}>,
 *  evidence: string
 *  details: string
 * }} data
 */
export function create(data) {
    const { startDateTime, distributionGroup, location, equipmentIssues, evidence, details } = data;

    return request('/api/incidents', {
        method: 'POST',
        body: JSON.stringify({
            startDateTime,
            distributionGroup,
            location,
            equipmentIssues,
            evidence,
            details
        })
    });
}

/**
 * @param {number} id Incident ID
 * @param {{
 *  startDateTime: number,
 *  distributionGroup: number,
 *  location: number,
 *  equipmentIssues: Array<{id: number, quantity: number}>,
 *  evidence: string
 *  details: string
 * }} data
 */
export function update(id, data) {
    const { startDateTime, distributionGroup, location, equipmentIssues, evidence, details } = data;

    return request('/api/incidents/'+id, {
        method: 'PUT',
        body: JSON.stringify({
            startDateTime,
            distributionGroup,
            location,
            equipmentIssues,
            evidence,
            details
        })
    });
}