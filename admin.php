<form action="add_product.php" method="POST" enctype="multipart/form-data">
    <input type="text" name="name" placeholder="Nom du produit" required>
    <input type="number" name="price" step="0.01" placeholder="Prix" required>
    <input type="file" name="image" accept="image/*" required>
    <select name="category">
        <option value="homme">Homme</option>
        <option value="femme">Femme</option>
    </select>
    <textarea name="description"></textarea>
    <button type="submit">Ajouter le produit</button>
</form>