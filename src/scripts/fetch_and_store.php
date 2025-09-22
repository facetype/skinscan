<?php

    require_once __DIR__ . "/../src/services/cs_float_client.php";
    require_once __DIR__ . "/../src/services/csgo_empire_client.php";
    require_once __DIR__ . "/../db/connection.php";


    //konfig
    $MIN_PROFIT_PERCENTAGE = 5;
    $MIN_QTY = 1;

    //db
    

    //  henter fra forskjellige markeder 
    $csFloat = new CSFloatClient();
    $empire = new EmpireClient();

    $csFloatData = $csFloat->GetPriceList();
    $empireData = $empire->GetPriceList();


    function normalize_csfloat($item) {
        return [
            'market_hash_name' => $item['market_hash_name'],
            'source' => 'csfloat',
            'min_price_cents' => (int)$item['min_price'],
            'qty' => (int)$item['qty'],
            'timestamp' => time()
        ];
    }


    // sjekk senere om json respons stemmer med det her
    function normalize_empire($item) { 
        return [
            'market_hash_name' => $item['market_hash_name'],
            'source' => 'empire',
            'min_price_cents' => (int)$item['min_price'],
            'qty' => (int)$item['qty'],
            'timestamp' => time()
        ];
    }

    $norm_float = array_map("normalize_csfloat", $csFloatData);
    $norm_empire = array_map("normalize_empire", $empireData);
    



?>