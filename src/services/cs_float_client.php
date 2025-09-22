<?php

    class CSFloatClient{
        private $baseUrl = "https://csfloat.com/api/v1/listings/price-list";

        public function GetPriceList(){
            return $this->makeRequest("listings/price-list");
        }

        public function getListings($marketHashName){
            $params = http_build_query([
                "market_hash_name" => $marketHashName,
                "limit"=> 10,
                "type" => "buy_now",
                "sort_by"=> "lowest_price"
            ]);
        }

        private function makeRequest($endpoint, $auth = false){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

            if ($auth){
                
                $headers = [
                    "Authorization: ". getenv("CS_FLOAT_API_KEY")
                ];
                
                

                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            }


            $response = curl_exec($ch);
            if(curl_errno($ch)){
                throw new Exception("cURL error: ", curl_errno($ch));
            }
            curl_close($ch);

            return json_decode($response, true);
        }


    }



?>