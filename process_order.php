<?php
session_start();
require 'db.php';

// Vérifier l'authentification
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Récupérer les données
$paymentMethod = $_POST['payment_method'] ?? null;

try {
    // Démarrer la transaction
    $pdo->beginTransaction();

    // Récupérer le panier
    $stmt = $pdo->prepare("
        SELECT cart.*, products.price 
        FROM cart 
        JOIN products ON cart.product_id = products.id 
        WHERE user_id = ?
    ");
    $stmt->execute([$_SESSION['user']['id']]);
    $cartItems = $stmt->fetchAll();

    // Calculer le total
    $total = 0;
    foreach ($cartItems as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    // Créer la commande
    $stmt = $pdo->prepare("
        INSERT INTO orders (user_id, total, payment_method)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$_SESSION['user']['id'], $total, $paymentMethod]);
    $orderId = $pdo->lastInsertId();

    // Ajouter les articles
    foreach ($cartItems as $item) {
        $stmt = $pdo->prepare("
            INSERT INTO order_items 
            (order_id, product_id, size, quantity, price)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $orderId,
            $item['product_id'],
            $item['size'],
            $item['quantity'],
            $item['price']
        ]);
    }

    // Vider le panier
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user']['id']]);

    $pdo->commit();
    header("Location: order_confirmation.php?id=$orderId");
    exit();

} catch(PDOException $e) {
    $pdo->rollBack();
    die("Erreur lors du traitement de la commande : " . $e->getMessage());
}