<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(403);
    echo json_encode(["error" => "Not logged in"]);
    exit;
}

require_once __DIR__ . '/../db/connection.php';

header('Content-Type: application/json');

$stmt = $pdo->query("SELECT * FROM arbitrage_ops ORDER BY profit_percent DESC");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);
