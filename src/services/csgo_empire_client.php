<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

class EmpireClient {
    private $baseUrl = "https://csgoempire.com/api/v2/trading/user/trades";
    private $apiKey;

    public function __construct() {
        $envPath = __DIR__;
        $parentPath = __DIR__ . '/../..';

        if (file_exists($parentPath . '/.env')) {
            $dotenv = Dotenv::createImmutable($parentPath);
            $dotenv->safeLoad();
        } elseif (file_exists($envPath . '/.env')) {
            $dotenv = Dotenv::createImmutable($envPath);
            $dotenv->safeLoad();
        }

        $this->apiKey = $_ENV["CSGO_EMPIRE_API_KEY"] ?? null;

        if (!$this->apiKey) {
            throw new Exception("API key not found. Please set CSGO_EMPIRE_API_KEY in your .env file.");
        }
    }

    public function GetPriceListStream($callback) {
        $page = 1;
        $perPage = 2500;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer " . $this->apiKey]);

        while (true) {
            $url = "https://csgoempire.com/api/v2/trading/items?per_page=$perPage&page=$page&auction=no";
            curl_setopt($ch, CURLOPT_URL, $url);

            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                throw new Exception("cURL error: " . curl_error($ch));
            }

            $response = json_decode($response, true);
            if (!isset($response['data']) || empty($response['data'])) {
                break;
            }

            foreach ($response['data'] as $item) {
                $callback($item);
            }

            $page++;
            usleep(150000);
        }

        curl_close($ch);
    }
}
