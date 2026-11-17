<?php
/**
 * MajorPanel API client for PHP
 *
 * Official docs: https://majorpanel.com/api
 * Get your free API key: https://majorpanel.com/
 *
 * Requires PHP 7.4+ with the cURL extension.
 */

class MajorPanelApi
{
    /** @var string */
    private $apiUrl = 'https://majorpanel.com/api/v2';

    /** @var string */
    private $apiKey;

    /** @var int */
    private $timeout;

    public function __construct(string $apiKey, int $timeout = 30)
    {
        $this->apiKey  = $apiKey;
        $this->timeout = $timeout;
    }

    /**
     * List every service with its ID, name, category, rate (price per 1000), min and max.
     */
    public function services(): array
    {
        return $this->request(['action' => 'services']);
    }

    /**
     * Place a new order.
     *
     * @param int      $service  Service ID (from services())
     * @param string   $link     Link to the page / post
     * @param int      $quantity Quantity to deliver
     * @param int|null $runs     Optional: number of runs (drip-feed)
     * @param int|null $interval Optional: minutes between runs (drip-feed)
     * @return array   e.g. ['order' => 23501]
     */
    public function order(int $service, string $link, int $quantity, ?int $runs = null, ?int $interval = null): array
    {
        $params = [
            'action'   => 'add',
            'service'  => $service,
            'link'     => $link,
            'quantity' => $quantity,
        ];
        if ($runs !== null)     { $params['runs'] = $runs; }
        if ($interval !== null) { $params['interval'] = $interval; }

        return $this->request($params);
    }

    /**
     * Get the charge, start count, status and remains of an order.
     */
    public function status(int $orderId): array
    {
        return $this->request(['action' => 'status', 'order' => $orderId]);
    }

    /**
     * Get your current balance and currency.
     */
    public function balance(): array
    {
        return $this->request(['action' => 'balance']);
    }

    /**
     * Send a POST request to the API and decode the JSON answer.
     *
     * @throws RuntimeException on network errors or invalid JSON
     */
    private function request(array $params): array
    {
        $params['key'] = $this->apiKey;

        $ch = curl_init($this->apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_USERAGENT      => 'MajorPanel-PHP-Client/1.0',
        ]);

        $body = curl_exec($ch);
        if ($body === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new RuntimeException('MajorPanel API request failed: ' . $error);
        }
        curl_close($ch);

        $data = json_decode($body, true);
        if (!is_array($data)) {
            throw new RuntimeException('MajorPanel API returned invalid JSON: ' . substr($body, 0, 200));
        }

        return $data;
    }
}
