<?php
session_start();
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];

    if (empty($login) || empty($password)) {
        $error = 'Tous les champs sont requis.';
    } else {
        $pdo = getDBConnection();

        try {
            $stmt = $pdo->prepare("SELECT password FROM utilisateurs WHERE login = ?");
            $stmt->execute([$login]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['login'] = $login;
                header('Location: profil.php');
                exit();
            } else {
                $error = 'Login ou mot de passe incorrect.';
            }
        } catch (PDOException $e) {
            $error = 'Une erreur s\'est produite lors de la connexion. Veuillez réessayer.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="zaza.css">
</head>
<body>
    <h1>Connexion</h1>
    <?php if (isset($_GET['logout']) && $_GET['logout'] == 'success'): ?>
        <p style="color:green;">Vous avez été déconnecté avec succès.</p>
    <?php endif; ?>
    <?php if (isset($_GET['register']) && $_GET['register'] == 'success'): ?>
        <p style="color:green;">Inscription réussie. Veuillez vous connecter.</p>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post">
        <label>Login: <input type="text" name="login" required></label><br>
        <label>Mot de passe: <input type="password" name="password" required></label><br>
        <input type="submit" value="Se connecter">
    </form>
</body>
</html>

