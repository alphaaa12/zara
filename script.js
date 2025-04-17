// Filtrage des produits
document.querySelectorAll('.category-btn').forEach(button => {
    button.addEventListener('click', () => {
        const category = button.dataset.category;
        
        // Gestion des classes actives
        document.querySelectorAll('.category-btn').forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');

        // Filtrage
        document.querySelectorAll('.product-card').forEach(product => {
            if(category === 'all' || product.dataset.category === category) {
                product.style.display = 'block';
            } else {
                product.style.display = 'none';
            }
        });
    });
});

// Gestion de la quantité
document.querySelectorAll('.quantity-btn').forEach(button => {
    button.addEventListener('click', async (e) => {
        const cartItem = button.closest('.cart-item');
        const input = cartItem.querySelector('.quantity-input');
        let quantity = parseInt(input.value);
        
        if (button.classList.contains('minus') && quantity > 1) {
            quantity--;
        } else if (button.classList.contains('plus')) {
            quantity++;
        }
        
        input.value = quantity;
        await updateCartItem(cartItem.dataset.id, quantity);
    });
});

// Mise à jour asynchrone
async function updateCartItem(cartId, quantity) {
    try {
        const response = await fetch('update_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                cartId: cartId,
                quantity: quantity
            })
        });
        
        const result = await response.json();
        if (result.success) {
            location.reload();
        }
    } catch (error) {
        console.error('Erreur:', error);
    }
}

// Suppression d'article
document.querySelectorAll('.remove-item').forEach(button => {
    button.addEventListener('click', async () => {
        const cartItem = button.closest('.cart-item');
        
        try {
            const response = await fetch('remove_from_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    cartId: cartItem.dataset.id
                })
            });
            
            const result = await response.json();
            if (result.success) {
                cartItem.remove();
                updateCartCount();
                location.reload();
            }
        } catch (error) {
            console.error('Erreur:', error);
        }
    });
});

// Gestion des onglets d'authentification
document.querySelectorAll('.auth-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        const tabId = tab.dataset.tab;
        
        // Active l'onglet
        document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        
        // Affiche le bon formulaire
        document.querySelectorAll('.auth-form').forEach(form => {
            form.classList.remove('active');
            if (form.id === `${tabId}-form`) {
                form.classList.add('active');
            }
        });
    });
});

// Mise à jour de la taille et quantité
document.querySelectorAll('.cart-item').forEach(item => {
    const sizeSelect = item.querySelector('.size-select');
    const quantityInput = item.querySelector('.quantity-input');
    const cartId = item.dataset.id;

    function updateCartItem() {
        const formData = new FormData();
        formData.append('size', sizeSelect.value);
        formData.append('quantity', quantityInput.value);
        formData.append('cartId', cartId);

        fetch('update_cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload(); // Rafraîchir pour mettre à jour les prix
            }
        });
    }

    sizeSelect.addEventListener('change', updateCartItem);
    quantityInput.addEventListener('change', updateCartItem);
});


if (window.innerWidth < 768) {
    const video = document.querySelector('.hero-video');
    video.pause(); // Désactive l'autoplay sur mobile si nécessaire
}