<?php
require_once __DIR__ . "/../services/cs_float_client.php";
require_once __DIR__ . "/../services/csgo_empire_client.php";
require_once __DIR__ . "/../db/connection.php";

file_put_contents("/home/270445/cron_test.log", date("Y-m-d H:i:s") . " ran\n", FILE_APPEND);

function normalizeName($name) {

    $normalized = mb_strtolower($name, 'UTF-8');
    $normalized = trim($normalized);
    $normalized = preg_replace('/[^a-z0-9 ]/u', '', $normalized);
    $normalized = preg_replace('/\s+/', ' ', $normalized);
    return $normalized;
}

function normalize_empire($item) {
    if (is_object($item)) {
        $item = (array)$item;
    }
    if (!is_array($item)) {
        return null;
    }

    $coins = $item['purchase_price'] / 100;

    return [
        'market_hash_name' => $item['market_name'] ?? 'UNKNOWN',
        'source' => 'empire',
        'min_price_cents' => empireCoinsToUsdCents($coins),
        'wear_name' => $item['wear_name'] ?? null,
        'qty' => 1,
        'timestamp' => time()
    ];
}


function empireCoinsToUsdCents($coins) {
    $usd = $coins * 0.6143;   // 1 coin = 0.6143 USD
    return round($usd * 100); // til cents
}


// konfig
$MIN_PROFIT_PERCENTAGE = 2;
$MIN_QTY = 1;
$MIN_VALUE = 100;
$MAX_PROFT_PERCENTAGE = 100;

$csFloat = new CSFloatClient();
$empire = new EmpireClient();

$csFloatData = $csFloat->GetPriceList();

$norm_empire = [];
$empire->GetPriceListStream(function($item) use (&$norm_empire) {
    $normalized = normalize_empire($item);
    if ($normalized !== null) {
        $norm_empire[] = $normalized;
    }
});




function normalize_csfloat($item) {
    return [
        'market_hash_name' => $item['market_hash_name'] ?? 'UNKNOWN',
        'source' => 'csfloat',
        'min_price_cents' => (int)($item['min_price'] ?? 0),
        'wear_name' => $item['wear_name'] ?? null,
        'qty' => (int)($item['qty'] ?? 0),
        'timestamp' => time()
    ];
}




$norm_float = array_map("normalize_csfloat", $csFloatData);



$floatIndex = [];
foreach ($norm_float as $item) {
    $normalizedName = normalizeName($item['market_hash_name']);
    $floatIndex[$normalizedName] = $item;
}

$empireIndex = [];
foreach ($norm_empire as $item) {
    $normalizedName = normalizeName($item['market_hash_name']);

    if (!isset($empireIndex[$normalizedName]) || $item['min_price_cents'] < $empireIndex[$normalizedName]['min_price_cents']) {
        $empireIndex[$normalizedName] = $item;
    }
}




$arbitrageOps = [];



foreach ($floatIndex as $name => $floatItem) {
    if (isset($empireIndex[$name])) {
        $empireItem = $empireIndex[$name];

        if ($floatItem['qty'] < $MIN_QTY) {
            continue;
        }

        $floatPrice = $floatItem['min_price_cents'];
        $empirePrice = $empireItem['min_price_cents'];


        if ($empirePrice < $floatPrice) {
            $profit = $floatPrice - $empirePrice;
            $profitPercent = ($profit / $empirePrice) * 100;
            //checking for profit, and minimum price
            if ($profitPercent >= $MIN_PROFIT_PERCENTAGE && $empirePrice >= $MIN_VALUE && $profitPercent <= $MAX_PROFT_PERCENTAGE) {
                $arbitrageOps[] = [
                    'direction' => 'Buy on Empire → Sell on Float',
                    'market_hash_name' => $floatItem['market_hash_name'],
                    'empire_price' => $empirePrice,
                    'float_price' => $floatPrice,
                    'wear_name' => $floatItem['wear_name'] ?? $empireItem['wear_name'],
                    'profit' => $profit,
                    'profit_percent' => round($profitPercent, 2)
                ];
            }
        }


        if ($floatPrice < $empirePrice) {
            $profit = $empirePrice - $floatPrice;
            $profitPercent = ($profit / $floatPrice) * 100;
            //checking for profit, and minimum price
            if ($profitPercent >= $MIN_PROFIT_PERCENTAGE && $floatPrice >= $MIN_VALUE && $profitPercent <= $MAX_PROFT_PERCENTAGE) {
                $arbitrageOps[] = [
                    'direction' => 'Buy on Float → Sell on Empire',
                    'market_hash_name' => $floatItem['market_hash_name'],
                    'empire_price' => $empirePrice,
                    'float_price' => $floatPrice,
                    'wear_name' => $floatItem['wear_name'] ?? $empireItem['wear_name'],
                    'profit' => $profit,
                    'profit_percent' => round($profitPercent, 2)
                ];
            }
        }
    }
}

// --- SAVE RESULTS TO DATABASE ---

// Clear old table entries
$pdo->exec("TRUNCATE arbitrage_ops");

$stmtInsert = $pdo->prepare("
    INSERT INTO arbitrage_ops 
    (market_hash_name, wear_name, direction, empire_price, float_price, profit, profit_percent) 
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

foreach ($arbitrageOps as $op) {
    $stmtInsert->execute([
        $op['market_hash_name'],
        $op['wear_name'],
        $op['direction'],
        $op['empire_price'],
        $op['float_price'],
        $op['profit'],
        $op['profit_percent']
    ]);
    
}



header('Content-Type: application/json');
echo json_encode($arbitrageOps, JSON_PRETTY_PRINT);
