<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['count' => 0]);
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT SUM(quantity) AS count FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user']['id']]);
    $result = $stmt->fetch();
    
    echo json_encode(['count' => $result['count'] ?? 0]);
    
} catch(PDOException $e) {
    echo json_encode(['count' => 0]);
}
?>