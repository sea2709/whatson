/**
 * input leading zero to a string
 * @param n
 * @returns {string}
 */
export function getZeroPad (n) {
    return (parseInt(n, 10) >= 10 ? '' : '0') + n
}