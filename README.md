# MajorPanel API – SMM Panel API Client (PHP, Python, Node.js)

Free, ready-to-use client libraries for the **[MajorPanel SMM panel API](https://majorpanel.com/api)**.
Connect your website, Telegram bot or child panel to [MajorPanel](https://majorpanel.com/) and order Instagram, TikTok, YouTube, Facebook and Telegram services automatically at wholesale prices.

- No dependencies (plain cURL / urllib / fetch)
- Same simple methods in every language: `services`, `order`, `status`, `balance`
- MIT license – copy it into your own project

## Get your API key

1. Create a free account at **[majorpanel.com](https://majorpanel.com/)**
2. Open the **API** page and copy your key
3. Never commit your key – use an environment variable such as `MAJORPANEL_API_KEY`

## API basics

| | |
|---|---|
| HTTP method | `POST` |
| API URL | `https://majorpanel.com/api/v2` |
| Response | JSON |

```bash
curl -X POST https://majorpanel.com/api/v2 \
  -d "key=YOUR_API_KEY" \
  -d "action=services"
```

| Action | Parameters | Returns |
|---|---|---|
| `services` | – | list of services: `service`, `name`, `type`, `category`, `rate` (per 1000), `min`, `max` |
| `add` | `service`, `link`, `quantity`, optional `runs`, `interval` (drip-feed) | `{"order": 23501}` |
| `status` | `order` | `charge`, `start_count`, `status`, `remains`, `currency` |
| `balance` | – | `balance`, `currency` |

Full documentation: **https://majorpanel.com/api**

## PHP

```php
require 'php/MajorPanelApi.php';

$api = new MajorPanelApi(getenv('MAJORPANEL_API_KEY'));

print_r($api->balance());                       // ['balance' => '100.84', 'currency' => 'USD']
$services = $api->services();                   // all services with price per 1000
$order    = $api->order(1, 'https://instagram.com/p/xyz', 1000);
print_r($api->status($order['order']));
```

Drip-feed order (10 runs, every 60 minutes):

```php
$api->order(1, 'https://instagram.com/p/xyz', 100, 10, 60);
```

## Python

```python
from majorpanel_api import MajorPanelApi
import os

api = MajorPanelApi(os.environ["MAJORPANEL_API_KEY"])

print(api.balance())
services = api.services()
order = api.order(1, "https://instagram.com/p/xyz", 1000)
print(api.status(order["order"]))
```

## Node.js (18+)

```js
const { MajorPanelApi } = require('./node/majorpanel-api');

const api = new MajorPanelApi(process.env.MAJORPANEL_API_KEY);

(async () => {
  console.log(await api.balance());
  const services = await api.services();
  const { order } = await api.order(1, 'https://instagram.com/p/xyz', 1000);
  console.log(await api.status(order));
})();
```

## Examples

- [`examples/place-order.php`](examples/place-order.php) – check balance, pick a service, place an order, check its status
- [`examples/check-status.py`](examples/check-status.py) – check the status of several orders from the command line

## Errors

If something is wrong (bad key, low balance, wrong service ID, quantity out of range) the API answers with JSON that contains an `error` field. Always check for it before reading the result:

```php
$result = $api->order(1, $link, 10);
if (isset($result['error'])) {
    echo 'Error: ' . $result['error'];
}
```

## Use cases

- **Child panel / reseller website** – resell MajorPanel services under your own brand and prices
- **Telegram or Discord bot** – let users order directly from chat
- **Agency dashboards** – place promotion orders when a campaign starts and pull status updates into client reports

## Links

- Website: [majorpanel.com](https://majorpanel.com/)
- API docs: [majorpanel.com/api](https://majorpanel.com/api)
- Services & prices: [majorpanel.com/services](https://majorpanel.com/services)
- Support: support@majorpanel.com

## License

MIT – see [LICENSE](LICENSE).
