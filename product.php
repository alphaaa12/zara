<?php
require 'db.php';
include('header.php');

if (!isset($_GET['id'])) {
    header('Location: products.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch();

    if (!$product) {
        header('Location: products.php');
        exit;
    }
} catch(PDOException $e) {
    die("Erreur lors de la récupération du produit");
}

// Définir les tailles disponibles selon la catégorie
$sizes = $product['category'] === 'homme' 
    ? ['S', 'M', 'L', 'XL'] 
    : ['36', '38', '40', '42'];
?>

<div class="product-details">
    <div class="product-image">
        <img src="images/<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
    </div>
    <div class="product-info">
        <h1><?= $product['name'] ?></h1>
        <p class="price"><?= number_format($product['price'], 2) ?> DT</p>
        <p class="description"><?= $product['description'] ?? '' ?></p>
        
        <form action="add_to_cart.php" method="POST" class="add-to-cart-form">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            
            <div class="size-selector">
                <label for="size">Choisir la taille:</label>
                <select name="size" id="size" required>
                    <option value="">Sélectionner une taille</option>
                    <?php foreach ($sizes as $size): ?>
                        <option value="<?= $size ?>"><?= $size ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="quantity-selector">
                <label for="quantity">Quantité:</label>
                <input type="number" name="quantity" id="quantity" value="1" min="1" max="10" required>
            </div>
            
            <button type="submit" class="add-to-cart-btn">Ajouter au panier</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addToCartForm = document.querySelector('.add-to-cart-form');
    
    addToCartForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('add_to_cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mettre à jour le compteur du panier
                const cartCount = document.querySelector('.cart-count');
                if (cartCount) {
                    cartCount.textContent = data.cartCount;
                }
                
                // Afficher un message de confirmation
                const confirmationMsg = document.createElement('div');
                confirmationMsg.className = 'confirmation-message';
                confirmationMsg.textContent = 'Produit ajouté au panier !';
                addToCartForm.appendChild(confirmationMsg);
                
                setTimeout(() => {
                    confirmationMsg.remove();
                }, 3000);
            } else {
                alert(data.message || 'Erreur lors de l\'ajout au panier');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue');
        });
    });
});
</script>

<style>
.confirmation-message {
    background-color: #4CAF50;
    color: white;
    padding: 10px;
    border-radius: 4px;
    margin-top: 10px;
    text-align: center;
}
</style>

<style>
.product-details {
    display: flex;
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
    gap: 2rem;
}

.product-image {
    flex: 1;
}

.product-image img {
    width: 100%;
    height: auto;
    object-fit: cover;
}

.product-info {
    flex: 1;
    padding: 1rem;
}

.product-info h1 {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.price {
    font-size: 1.5rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 1rem;
}

.description {
    margin-bottom: 2rem;
    line-height: 1.6;
}

.size-selector,
.quantity-selector {
    margin-bottom: 1.5rem;
}

label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
}

select,
input[type="number"] {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-top: 0.25rem;
}

.add-to-cart-btn {
    background-color: #000;
    color: white;
    padding: 1rem 2rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    width: 100%;
    font-size: 1.1rem;
    transition: background-color 0.3s;
}

.add-to-cart-btn:hover {
    background-color: #333;
}

@media (max-width: 768px) {
    .product-details {
        flex-direction: column;
    }
}
</style>

<?php include('footer.php'); ?>