<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false]);
    exit();
}

try {
    $stmt = $pdo->prepare("
        UPDATE cart SET 
        size = ?, 
        quantity = ? 
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([
        $_POST['size'],
        $_POST['quantity'],
        $_POST['cartId'],
        $_SESSION['user']['id']
    ]);
    
    echo json_encode(['success' => true ]);
} catch(PDOException $e) {
    echo json_encode([ 'success' => false ]);
}