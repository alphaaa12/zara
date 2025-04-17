<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Récupérer les informations utilisateur
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user']['id']]);
    $user = $stmt->fetch();
} catch(PDOException $e) {
    die("Erreur de récupération des données utilisateur");
}

// Récupérer l'historique des commandes
try {
    $stmt = $pdo->prepare("
        SELECT orders.*, 
               SUM(order_items.quantity * order_items.price) AS total,
               COUNT(order_items.id) AS items_count
        FROM orders
        LEFT JOIN order_items ON orders.id = order_items.order_id
        WHERE user_id = ?
        GROUP BY orders.id
        ORDER BY orders.created_at DESC
    ");
    $stmt->execute([$_SESSION['user']['id']]);
    $orders = $stmt->fetchAll();
} catch(PDOException $e) {
    die("Erreur de récupération des commandes");
}

include('header.php');
?>

<section class="account-container">
    <h1>Mon Compte</h1>
    
    <div class="account-info">
        <h2>Mes informations</h2>
        <div class="info-grid">
            <div class="info-item">
                <label>Nom :</label>
                <p><?= htmlspecialchars($user['name']) ?></p>
            </div>
            <div class="info-item">
                <label>Email :</label>
                <p><?= htmlspecialchars($user['email']) ?></p>
            </div>
            <div class="info-item">
                <label>Date d'inscription :</label>
                <p><?= date('d/m/Y', strtotime($user['created_at'])) ?></p>
            </div>
        </div>
    </div>

    <div class="order-history">
        <h2>Historique des commandes</h2>
        
        <?php if(count($orders) > 0): ?>
            <div class="orders-list">
                <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div class="order-id">Commande #<?= $order['id'] ?></div>
                        <div class="order-date"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></div>
                        <div class="order-status <?= $order['status'] ?>"><?= ucfirst($order['status']) ?></div>
                    </div>
                    
                    <div class="order-details">
                        <div class="order-items">
                            <?php
                            // Récupérer les articles de la commande
                            $stmt = $pdo->prepare("
                                SELECT order_items.*, products.name 
                                FROM order_items 
                                JOIN products ON order_items.product_id = products.id
                                WHERE order_id = ?
                            ");
                            $stmt->execute([$order['id']]);
                            $items = $stmt->fetchAll();
                            ?>
                            
                            <?php foreach ($items as $item): ?>
                            <div class="order-item">
                                <span class="item-name"><?= $item['name'] ?></span>
                                <span class="item-quantity">x<?= $item['quantity'] ?></span>
                                <span class="item-price"><?= number_format($item['price'], 2) ?> DT</span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="order-total">
                            Total : <?= number_format($order['total'], 2) ?> DT
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="no-orders">Aucune commande passée pour le moment.</p>
        <?php endif; ?>
    </div>
</section>

<?php include('footer.php'); ?>