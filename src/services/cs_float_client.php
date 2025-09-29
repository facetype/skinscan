<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

class CSFloatClient {
    private $baseUrl = "https://csfloat.com/api/v1/";
    private $apiKey;

    public function __construct() {

        $projectRoot = __DIR__ . '/../../';
        if (file_exists($projectRoot . '/.env')) {
            $dotenv = Dotenv::createImmutable($projectRoot);
            $dotenv->safeLoad();
        }


        $this->apiKey = getenv("CS_FLOAT_API_KEY") 
            ?: ($_ENV["CS_FLOAT_API_KEY"] ?? $_SERVER["CS_FLOAT_API_KEY"] ?? null);
    }


    public function GetPriceList() {
        return $this->makeRequest("listings/price-list");
    }


    public function GetListings($marketHashName) {
        $params = http_build_query([
            "market_hash_name" => $marketHashName,
            "limit" => 10,
            "type" => "buy_now",
            "sort_by" => "lowest_price"
        ]);
        return $this->makeRequest("listings?" . $params);
    }

    private function makeRequest($endpoint, $auth = false) {
        $url = $this->baseUrl . ltrim($endpoint, '/');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if ($auth) {
            if (!$this->apiKey) {
                throw new Exception("API key not found. Please set CS_FLOAT_API_KEY in your .env file.");
            }

            $headers = [
                "Authorization: Bearer " . $this->apiKey,
                "accept: application/json"
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
?>
