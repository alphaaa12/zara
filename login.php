<?php
session_start();
require 'db.php';

$error = '';
$success = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        // Inscription
        if ($_POST['action'] === 'register') {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $name = $_POST['name'];
            
            try {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                
                if ($stmt->rowCount() > 0) {
                    $error = 'Cet email est déjà utilisé';
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (email, password, name) VALUES (?, ?, ?)");
                    $stmt->execute([$email, $hashedPassword, $name]);
                    $success = 'Compte créé avec succès !';
                }
            } catch(PDOException $e) {
                $error = 'Erreur lors de l\'inscription';
            }
        }
        // Connexion
        elseif ($_POST['action'] === 'login') {
            $email = $_POST['email'];
            $password = $_POST['password'];
            
            try {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                
                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user'] = $user;
                    header('Location: index.php');
                    exit();
                } else {
                    $error = 'Identifiants incorrects';
                }
            } catch(PDOException $e) {
                $error = 'Erreur lors de la connexion';
            }
        }
    }
}
?>

<?php include('header.php'); ?>

<div class="auth-container">
    <div class="auth-tabs">
        <button class="auth-tab active" data-tab="login">Connexion</button>
        <button class="auth-tab" data-tab="register">Créer un compte</button>
    </div>

    <div class="auth-content">
        <!-- Formulaire de Connexion -->
        <form class="auth-form active" id="login-form" method="POST">
            <?php if ($error): ?>
                <div class="alert error"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert success"><?= $success ?></div>
            <?php endif; ?>

            <input type="hidden" name="action" value="login">
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" required>
            </div>
            
            <button type="submit" class="auth-btn">Se connecter</button>
        </form>

        <!-- Formulaire d'Inscription -->
        <form class="auth-form" id="register-form" method="POST">
            <input type="hidden" name="action" value="register">
            
            <div class="form-group">
                <label>Nom complet</label>
                <input type="text" name="name" required>
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" required>
            </div>
            
            <button type="submit" class="auth-btn">Créer un compte</button>
        </form>
    </div>
</div>

<?php include('footer.php'); ?>