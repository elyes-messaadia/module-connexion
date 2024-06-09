<?php
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = trim($_POST['login']);
    $prenom = trim($_POST['prenom']);
    $nom = trim($_POST['nom']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($login) || empty($prenom) || empty($nom) || empty($password) || empty($confirm_password)) {
        $error = 'Tous les champs sont requis.';
    } elseif ($password != $confirm_password) {
        $error = 'Les mots de passe ne correspondent pas.';
    } else {
        $password_hashed = password_hash($password, PASSWORD_BCRYPT);
        $pdo = getDBConnection();

        try {
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (login, prenom, nom, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$login, $prenom, $nom, $password_hashed]);
            header('Location: connexion.php?register=success');
        } catch (PDOException $e) {
            $error = 'Une erreur s\'est produite lors de l\'inscription. Veuillez réessayer.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="zaza.css">
</head>
<body>
    <h1>Inscription</h1>
    <?php if (isset($error)): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post">
        <label>Login: <input type="text" name="login" required></label><br>
        <label>Prénom: <input type="text" name="prenom" required></label><br>
        <label>Nom: <input type="text" name="nom" required></label><br>
        <label>Mot de passe: <input type="password" name="password" required></label><br>
        <label>Confirmer mot de passe: <input type="password" name="confirm_password" required></label><br>
        <input type="submit" value="S'inscrire">
    </form>
</body>
</html>

