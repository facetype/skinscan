<?php
require_once __DIR__ . '/../db/connection.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode([
        'success' => false,
        'error' => 'Please enter both username and password.'
    ]);
    exit;
}

//check if user exists
try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    
    if ($stmt->fetch()) {
        echo json_encode([
            'success' => false,
            'error' => 'Username already exists.'
        ]);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $insert = $pdo->prepare("
        INSERT INTO users (username, password)
        VALUES (:username, :password)
    ");
    $insert->execute([
        'username' => $username,
        'password' => $hashedPassword
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'User registered successfully.'
    ]);

} catch (PDOException $e) {
    error_log("DB error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'An error occurred. Please try again later.'
    ]);
}
