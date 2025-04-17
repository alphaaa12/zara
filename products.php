<?php
require 'db.php';
include('header.php');

$categoryFilter = isset($_GET['category']) ? $_GET['category'] : null;

try {
    $sql = "SELECT * FROM products";
    $params = [];
    
    if($categoryFilter && in_array($categoryFilter, ['homme', 'femme'])) {
        $sql .= " WHERE category = ?";
        $params[] = $categoryFilter;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
} catch(PDOException $e) {
    die("Erreur lors de la récupération des produits");
}
?>

<section class="products-listing">
    <h1>Notre collection</h1>
    <div class="products-grid">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <img src="images/<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
                <h3><?= $product['name'] ?></h3>
                <p class="price"><?= number_format($product['price'], 2) ?> DT</p>
                <a href="product.php?id=<?= $product['id'] ?>" class="view-product">Voir le produit</a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php include('footer.php'); ?>