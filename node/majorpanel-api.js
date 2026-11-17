/**
 * MajorPanel API client for Node.js
 *
 * Official docs: https://majorpanel.com/api
 * Get your free API key: https://majorpanel.com/
 *
 * No dependencies - uses the built-in fetch (Node.js 18+).
 */

const API_URL = 'https://majorpanel.com/api/v2';

class MajorPanelApi {
  /**
   * @param {string} apiKey  Your MajorPanel API key
   * @param {number} timeoutMs Request timeout in milliseconds
   */
  constructor(apiKey, timeoutMs = 30000) {
    this.apiKey = apiKey;
    this.timeoutMs = timeoutMs;
  }

  /** List every service with its ID, name, category, rate (price per 1000), min and max. */
  services() {
    return this.#request({ action: 'services' });
  }

  /**
   * Place a new order. Resolves to e.g. { order: 23501 }.
   * runs / interval are optional and only used for drip-feed orders.
   */
  order(service, link, quantity, { runs, interval } = {}) {
    const params = { action: 'add', service, link, quantity };
    if (runs !== undefined) params.runs = runs;
    if (interval !== undefined) params.interval = interval;
    return this.#request(params);
  }

  /** Get the charge, start count, status and remains of an order. */
  status(orderId) {
    return this.#request({ action: 'status', order: orderId });
  }

  /** Get your current balance and currency. */
  balance() {
    return this.#request({ action: 'balance' });
  }

  async #request(params) {
    const body = new URLSearchParams({ ...params, key: this.apiKey });
    const res = await fetch(API_URL, {
      method: 'POST',
      body,
      headers: { 'User-Agent': 'MajorPanel-Node-Client/1.0' },
      signal: AbortSignal.timeout(this.timeoutMs),
    });
    const text = await res.text();
    try {
      return JSON.parse(text);
    } catch {
      throw new Error('MajorPanel API returned invalid JSON: ' + text.slice(0, 200));
    }
  }
}

module.exports = { MajorPanelApi };

// Quick test: MAJORPANEL_API_KEY=your_key node majorpanel-api.js
if (require.main === module) {
  const api = new MajorPanelApi(process.env.MAJORPANEL_API_KEY || 'YOUR_API_KEY');
  api.balance().then(console.log).catch(console.error);
}
