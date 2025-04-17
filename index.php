<?php include('header.php'); ?>

<section class="hero">
    <!-- Vidéo de fond -->
    <video autoplay muted loop playsinline class="background-video">
        <source src="videos/background.mp4" type="video/mp4">
        <source src="videos/background.webm" type="video/webm">
        Votre navigateur ne supporte pas les vidéos HTML
        <img src="images/fallback.jpg" alt="Background alternative">
    </video>
    
    <div class="hero-content">
        <h1>Bienvenue chez Nalouti Store</h1>
        <p>Découvrez notre nouvelle collection printemps/été</p>
        <a href="products.php" class="cta-btn">Voir la collection</a>
    </div>
</section>
<!-- Catégories en vedette -->
<section class="featured-categories">
    <div class="category-card" onclick="location.href='products.php?category=homme'">
        <img src="images/homme-category.jpg" alt="Homme">
        <div class="category-label">Collection Homme</div>
    </div>
    <div class="category-card" onclick="location.href='products.php?category=femme'">
        <img src="images/femme-category.jpg" alt="Femme">
        <div class="category-label">Collection Femme</div>
    </div>
</section>

<!-- Produits phares -->
<section class="featured-products">
    <h2>Nouveauté</h2>
    <div class="products-grid">
        <?php
        require 'db.php';
        try {
            $stmt = $pdo->query("SELECT * FROM products ORDER BY RAND() LIMIT 4");
            while ($product = $stmt->fetch()) {
                echo '
                <div class="product-card">
                    <img src="images/'.$product['image'].'" alt="'.$product['name'].'">
                    <h3>'.$product['name'].'</h3>
                    <p class="price">'.number_format($product['price'], 2).' DT</p>
                    <a href="product.php?id='.$product['id'].'" class="view-product">Voir le produit</a>
                </div>';
            }
        } catch(PDOException $e) {
            die("Erreur lors de la récupération des produits");
        }
        ?>
    </div>
</section>

<?php include('footer.php'); ?>