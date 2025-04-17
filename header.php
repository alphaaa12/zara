<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boutique de Vêtements</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <nav>
        <div class="logo">Nalouti Store</div>
        <div class="search-bar">
            <form action="search.php" method="GET" class="search-form">
                <input type="text" 
                       name="q" 
                       class="search-input" 
                       placeholder="Rechercher un produit..."
                       autocomplete="off">
                <button type="submit" class="search-button">Rechercher</button>
            </form>
        </div>
        <ul>
            <li><a href="index.php">Accueil</a></li>
            <li><a href="products.php">Boutique</a></li>
            <li><a href="panier.php">Panier (<span id="cart-count">0</span>)</a></li>
            <?php if(isset($_SESSION['user'])): ?>
                <li><a href="compte.php">Mon compte</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            <?php else: ?>
                <li><a href="login.php">Connexion</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
    <main></main>