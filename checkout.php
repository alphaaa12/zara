<?php
session_start();
require 'db.php';

// Rediriger si non connecté
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Récupérer le panier avec les produits
try {
    $stmt = $pdo->prepare("
        SELECT cart.*, products.name, products.price, products.image, products.sizes 
        FROM cart 
        JOIN products ON cart.product_id = products.id 
        WHERE user_id = ?
    ");
    $stmt->execute([$_SESSION['user']['id']]);
    $cartItems = $stmt->fetchAll();
} catch(PDOException $e) {
    die("Erreur lors de la récupération du panier");
}

include('header.php');
?>

<section class="checkout-container">
    <h1>Finaliser votre commande</h1>
    
    <div class="cart-items">
        <?php foreach ($cartItems as $item): ?>
        <div class="cart-item" data-id="<?= $item['id'] ?>">
            <img src="images/<?= $item['image'] ?>" alt="<?= $item['name'] ?>">
            <div class="item-details">
                <h3><?= $item['name'] ?></h3>
                <div class="item-controls">
                    <div class="form-group">
                        <label>Taille :</label>
                        <select class="size-select">
                            <?php
                            $sizes = explode(',', $item['sizes']);
                            foreach ($sizes as $size) {
                                $selected = $size === $item['size'] ? 'selected' : '';
                                echo "<option value='$size' $selected>$size</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Quantité :</label>
                        <input type="number" class="quantity-input" value="<?= $item['quantity'] ?>" min="1">
                    </div>
                </div>
                <p class="item-price"><?= number_format($item['price'] * $item['quantity'], 2) ?> DT</p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <form id="payment-form" method="POST" action="process_order.php">
        <div class="payment-methods">
            <h2>Méthode de paiement</h2>
            <label>
                <input type="radio" name="payment_method" value="cash" required>
                Paiement à la livraison
            </label>
            <label>
                <input type="radio" name="payment_method" value="online">
                Paiement en ligne (Carte bancaire)
            </div>
        </div>

        <button type="submit" class="confirm-order">Confirmer la commande</button>
    </form>
</section>

<?php include('footer.php'); ?>