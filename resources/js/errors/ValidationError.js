export default class ValidationError extends Error {
    constructor(message, options) {
        super(message, options);
    }
}