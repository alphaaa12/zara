<?php
require 'db.php';
include('header.php');

$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';

try {
    $stmt = $pdo->prepare("
        SELECT * 
        FROM products 
        WHERE name LIKE :query 
        OR description LIKE :query
        ORDER BY name ASC
    ");
    
    $stmt->execute(['query' => "%$searchQuery%"]);
    $results = $stmt->fetchAll();
    
} catch(PDOException $e) {
    die("Erreur de recherche");
}
?>

<section class="search-results">
    <h2>Résultats pour "<?= htmlspecialchars($searchQuery) ?>"</h2>
    
    <?php if(count($results) > 0): ?>
        <div class="products-grid">
            <?php foreach ($results as $product): ?>
                <div class="product-card">
                    <img src="images/<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
                    <h3><?= $product['name'] ?></h3>
                    <p class="price"><?= number_format($product['price'], 2) ?> DT</p>
                    <a href="product.php?id=<?= $product['id'] ?>" class="view-product">Voir le produit</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-results">
            <p>Aucun résultat trouvé pour "<?= htmlspecialchars($searchQuery) ?>"</p>
            <a href="products.php" class="cta-btn">Voir tous les produits</a>
        </div>
    <?php endif; ?>
</section>

<?php include('footer.php'); ?>