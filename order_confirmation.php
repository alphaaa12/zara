<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || !isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

try {
    $stmt = $pdo->prepare("
        SELECT orders.*, users.name, users.email 
        FROM orders 
        JOIN users ON orders.user_id = users.id 
        WHERE orders.id = ?
    ");
    $stmt->execute([$_GET['id']]);
    $order = $stmt->fetch();
} catch(PDOException $e) {
    die("Erreur lors de la récupération de la commande");
}

include('header.php');
?>

<section class="confirmation-container">
    <h1>Commande confirmée !</h1>
    <div class="order-summary">
        <p>Merci <?= $order['name'] ?> ! Votre commande #<?= $order['id'] ?> est confirmée.</p>
        <p>Total payé : <?= number_format($order['total'], 2) ?> DT</p>
        <p>Méthode de paiement : <?= $order['payment_method'] === 'cash' ? 'Paiement à la livraison' : 'Carte bancaire' ?></p>
    </div>
    <a href="index.php" class="back-to-shop">Retour à la boutique</a>
</section>

<?php include('footer.php'); ?>