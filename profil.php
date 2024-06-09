<?php
session_start();
require 'database.php';

if (!isset($_SESSION['login'])) {
    header('Location: connexion.php');
    exit();
}

$login = $_SESSION['login'];
$pdo = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $prenom = trim($_POST['prenom']);
    $nom = trim($_POST['nom']);
    $password = $_POST['password'];

    if (empty($prenom) || empty($nom) || empty($password)) {
        $error = 'Tous les champs sont requis.';
    } else {
        $password_hashed = password_hash($password, PASSWORD_BCRYPT);

        try {
            $stmt = $pdo->prepare("UPDATE utilisateurs SET prenom = ?, nom = ?, password = ? WHERE login = ?");
            $stmt->execute([$prenom, $nom, $password_hashed, $login]);
            $success = 'Informations mises à jour.';
        } catch (PDOException $e) {
            $error = 'Une erreur s\'est produite lors de la mise à jour. Veuillez réessayer.';
        }
    }
}

try {
    $stmt = $pdo->prepare("SELECT prenom, nom FROM utilisateurs WHERE login = ?");
    $stmt->execute([$login]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    die('Une erreur s\'est produite lors de la récupération des informations. Veuillez réessayer.');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link rel="stylesheet" href="zaza.css">
</head>
<body>
    <h1>Modifier Profil</h1>
    <?php if (isset($error)): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php elseif (isset($success)): ?>
        <p style="color:green;"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>
    <form method="post">
        <label>Prénom: <input type="text" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required></label><br>
        <label>Nom: <input type="text" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required></label><br>
        <label>Mot de passe: <input type="password" name="password" required></label><br>
        <input type="submit" value="Mettre à jour">
    </form>
    <a href="deconnexion.php">Déconnexion</a>
</body>
</html>


