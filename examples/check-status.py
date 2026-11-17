"""
Example: check the status of one or more orders.
Run: MAJORPANEL_API_KEY=your_key python check-status.py 23501 23502
"""

import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "python"))
from majorpanel_api import MajorPanelApi  # noqa: E402

api = MajorPanelApi(os.environ.get("MAJORPANEL_API_KEY", "YOUR_API_KEY"))

if len(sys.argv) < 2:
    sys.exit("Usage: python check-status.py ORDER_ID [ORDER_ID ...]")

for order_id in sys.argv[1:]:
    result = api.status(int(order_id))
    if "error" in result:
        print("#%s  error: %s" % (order_id, result["error"]))
    else:
        print("#%s  %s  start=%s  remains=%s  charge=%s %s" % (
            order_id,
            result.get("status"),
            result.get("start_count"),
            result.get("remains"),
            result.get("charge"),
            result.get("currency", ""),
        ))
