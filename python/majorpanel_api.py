"""
MajorPanel API client for Python

Official docs: https://majorpanel.com/api
Get your free API key: https://majorpanel.com/

No dependencies - uses only the Python standard library (Python 3.7+).
"""

import json
import urllib.parse
import urllib.request

API_URL = "https://majorpanel.com/api/v2"


class MajorPanelError(Exception):
    """Raised when the API cannot be reached or returns invalid JSON."""


class MajorPanelApi:
    def __init__(self, api_key, timeout=30):
        self.api_key = api_key
        self.timeout = timeout

    def services(self):
        """List every service with its ID, name, category, rate (price per 1000), min and max."""
        return self._request({"action": "services"})

    def order(self, service, link, quantity, runs=None, interval=None):
        """Place a new order. Returns e.g. {"order": 23501}.

        runs / interval are optional and only used for drip-feed orders.
        """
        params = {"action": "add", "service": service, "link": link, "quantity": quantity}
        if runs is not None:
            params["runs"] = runs
        if interval is not None:
            params["interval"] = interval
        return self._request(params)

    def status(self, order_id):
        """Get the charge, start count, status and remains of an order."""
        return self._request({"action": "status", "order": order_id})

    def balance(self):
        """Get your current balance and currency."""
        return self._request({"action": "balance"})

    def _request(self, params):
        params = dict(params, key=self.api_key)
        data = urllib.parse.urlencode(params).encode("utf-8")
        req = urllib.request.Request(
            API_URL,
            data=data,
            headers={"User-Agent": "MajorPanel-Python-Client/1.0"},
        )
        try:
            with urllib.request.urlopen(req, timeout=self.timeout) as resp:
                body = resp.read().decode("utf-8")
        except Exception as exc:  # network / HTTP errors
            raise MajorPanelError("MajorPanel API request failed: %s" % exc) from exc

        try:
            return json.loads(body)
        except ValueError as exc:
            raise MajorPanelError("MajorPanel API returned invalid JSON: %s" % body[:200]) from exc


if __name__ == "__main__":
    import os

    api = MajorPanelApi(os.environ.get("MAJORPANEL_API_KEY", "YOUR_API_KEY"))
    print(api.balance())
