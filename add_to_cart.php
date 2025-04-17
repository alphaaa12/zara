<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Non connecté']);
    exit();
}

if (!isset($_POST['product_id']) || !isset($_POST['size']) || !isset($_POST['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit();
}

$productId = $_POST['product_id'];
$size = $_POST['size'];
$quantity = intval($_POST['quantity']);
$userId = $_SESSION['user']['id'];

try {
    // Vérifier si le produit est déjà dans le panier avec la même taille
    $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ? AND size = ?");
    $stmt->execute([$userId, $productId, $size]);
    
    if ($stmt->rowCount() > 0) {
        // Mettre à jour la quantité
        $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ? AND size = ?");
        $stmt->execute([$quantity, $userId, $productId, $size]);
    } else {
        // Ajouter au panier
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, size, quantity) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $productId, $size, $quantity]);
    }
    
    // Récupérer le nombre total d'articles
    $stmt = $pdo->prepare("SELECT SUM(quantity) AS count FROM cart WHERE user_id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch();
    
    echo json_encode(['success' => true, 'cartCount' => $result['count'] ?? 0]);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données']);
}
?>