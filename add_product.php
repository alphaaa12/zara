<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $target_dir = "images/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Vérifier si c'est une vraie image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if($check === false) {
        die("Le fichier n'est pas une image");
    }

    // Vérifier la taille du fichier (max 2MB)
    if ($_FILES["image"]["size"] > 2000000) {
        die("L'image est trop volumineuse");
    }

    // Autoriser certains formats
    if(!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        die("Seuls JPG, JPEG, PNG & GIF sont autorisés");
    }

    // Générer un nom unique
    $new_filename = uniqid() . '.' . $imageFileType;
    $target_path = $target_dir . $new_filename;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_path)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO products 
                (name, price, image, category, description)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $_POST['name'],
                $_POST['price'],
                $new_filename,
                $_POST['category'],
                $_POST['description']
            ]);
            header("Location: admin.php?success=1");
        } catch(PDOException $e) {
            die("Erreur d'insertion : " . $e->getMessage());
        }
    } else {
        die("Erreur lors de l'upload de l'image");
    }
}