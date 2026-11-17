<?php
// Example: check balance, find a service, place an order and check its status.
// Run: MAJORPANEL_API_KEY=your_key php place-order.php

require __DIR__ . '/../php/MajorPanelApi.php';

$api = new MajorPanelApi(getenv('MAJORPANEL_API_KEY') ?: 'YOUR_API_KEY');

// 1. Balance
$balance = $api->balance();
echo "Balance: {$balance['balance']} {$balance['currency']}\n";

// 2. Find the first Instagram service
$services = $api->services();
$instagram = null;
foreach ($services as $s) {
    if (stripos($s['category'], 'instagram') !== false) {
        $instagram = $s;
        break;
    }
}
if (!$instagram) {
    exit("No Instagram service found\n");
}
echo "Using service #{$instagram['service']}: {$instagram['name']} ({$instagram['rate']} per 1000)\n";

// 3. Place an order (minimum quantity for that service)
$order = $api->order((int) $instagram['service'], 'https://www.instagram.com/p/EXAMPLE/', (int) $instagram['min']);
if (isset($order['error'])) {
    exit("Order failed: {$order['error']}\n");
}
echo "Order created: #{$order['order']}\n";

// 4. Check its status
$status = $api->status((int) $order['order']);
echo "Status: {$status['status']}, remains: {$status['remains']}\n";
