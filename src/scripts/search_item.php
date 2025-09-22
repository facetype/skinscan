<?php

    // for søking etter enkelte items via f.eks tekstboks på nettsiden


    require_once __DIR__ . "/../src/services/cs_float_client.php";
    require_once __DIR__ . "/../db/connection.php";


    header("Content-Type: applicaition/json");


    if (!isset($_GET['query'])) {
        echo "Missing query parameter";
        exit;
    }
    
    $itemName = $_GET['query'];
    $client = new CSFloatClient();
    
    
    $data = $client->getListings($item);
    echo json_encode($data, JSON_PRETTY_PRINT);


?>