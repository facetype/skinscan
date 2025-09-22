<?php

    class EmpireClient{

        private $baseUrl = "";

        public function GetPriceList(){
            return $this->makeRequest("ENDPOINT_HERE");

        }

        private function makeRequest($endpoint, $auth = true){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

            if ($auth){
                
                $headers = [
                    "Authorization: ". getenv("CSGO_EMPIRE_API_KEY")
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