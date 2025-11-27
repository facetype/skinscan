<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$itemName = $data['item'] ?? '';

if (!$itemName) {
    echo json_encode(["success" => false, "error" => "Invalid item"]);
    exit;
}

function normalizeName($name) {
    $normalized = mb_strtolower($name, 'UTF-8');
    $normalized = trim($normalized);
    $normalized = preg_replace('/[^a-z0-9 ]/u', '', $normalized);
    $normalized = preg_replace('/\s+/', ' ', $normalized);
    return $normalized;
}

$normalized = normalizeName($itemName);

require_once __DIR__ . '/../db/connection.php';

try {
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO favorites (user_id, item_name)
        VALUES (:uid, :name)
    ");
    
    $stmt->execute([
        ':uid' => $_SESSION['user_id'],
        ':name' => $normalized
    ]);

    echo json_encode(["success" => true]);
    exit;
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
    exit;
}

?>