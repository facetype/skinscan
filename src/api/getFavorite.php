<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit;
}

function normalizeName($name) {
    return $name;
}

require_once __DIR__ . '/../db/connection.php';

try {
    $stmt = $pdo->prepare("
        SELECT item_name 
        FROM favorites 
        WHERE user_id = :uid
        ORDER BY item_name ASC
    ");
    
    $stmt->execute([ ':uid' => $_SESSION['user_id'] ]);
    $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode([
        "success" => true,
        "favorites" => $rows
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
    exit;
}
?>
