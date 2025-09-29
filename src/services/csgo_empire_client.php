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

    public function GetPriceList() {
        return $this->makeRequest("https://csgoempire.com/api/v2/trading/items?per_page=2500&page=1&auction=no");
    }

    private function makeRequest($endpoint, $auth = true) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if ($auth) {
            $headers = [
                "Authorization: Bearer " . $this->apiKey
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception("cURL error: " . curl_error($ch));
        }

        curl_close($ch);

        return json_decode($response, true);
    }
}
