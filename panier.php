<?php
session_start();
require 'db.php';

// Redirection si non connecté
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Récupérer les articles du panier
try {
    $stmt = $pdo->prepare("
        SELECT cart.*, products.name, products.price, products.image 
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
} catch(PDOException $e) {
    die("Erreur lors de la récupération du panier");
}

include('header.php');
?>

<section class="cart-container">
    <h1>Votre Panier</h1>
    
    <?php if(count($cartItems) === 0): ?>
        <div class="empty-cart">
            <p>Votre panier est vide</p>
            <a href="index.php" class="continue-shopping">Continuer vos achats</a>
        </div>
    <?php else: ?>
        <div class="cart-items">
            <?php foreach ($cartItems as $item): ?>
            <div class="cart-item" data-id="<?= $item['id'] ?>">
                <img src="images/<?= $item['image'] ?>" alt="<?= $item['name'] ?>">
                <div class="item-info">
                    <h3><?= $item['name'] ?></h3>
                    <div class="item-controls">
                        <div class="quantity-controls">
                            <button class="quantity-btn minus">-</button>
                            <input type="number" class="quantity-input" value="<?= $item['quantity'] ?>" min="1">
                            <button class="quantity-btn plus">+</button>
                        </div>
                        <button class="remove-item">Supprimer</button>
                    </div>
                </div>
                <div class="item-pricing">
                    <p class="price"><?= number_format($item['price'], 2) ?> DT</p>
                    <p class="subtotal"><?= number_format($item['price'] * $item['quantity'], 2) ?> DT</p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="cart-summary">
            <div class="total">
                <h3>Total :</h3>
                <p class="total-price"><?= number_format($total, 2) ?> DT</p>
            </div>
            <a href="checkout.php" class="checkout-btn">Passer la commande</a>
        </div>
    <?php endif; ?>
</section>

<?php include('footer.php'); ?>