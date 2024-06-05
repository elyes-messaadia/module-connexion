<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = trim($_POST['login']);
    $prenom = trim($_POST['prenom']);
    $nom = trim($_POST['nom']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate inputs
    $errors = [];
    if (empty($login) || strlen($login) > 255) {
        $errors[] = "Invalid login.";
    }
    if (empty($prenom) || strlen($prenom) > 255) {
        $errors[] = "Invalid prenom.";
    }
    if (empty($nom) || strlen($nom) > 255) {
        $errors[] = "Invalid nom.";
    }
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        // Database connection
        $conn = new mysqli('localhost', 'root', '', 'moduleconnexion');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if login exists
        $stmt = $conn->prepare("SELECT login FROM utilisateurs WHERE login = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            // Insert user
            $hashed_password = hash('sha256', $password);
            $stmt = $conn->prepare("INSERT INTO utilisateurs (login, prenom, nom, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $login, $prenom, $nom, $hashed_password);
            $stmt->execute();

            // Redirect to login page
            header("Location: connexion.php");
        } else {
            $errors[] = "Login already exists.";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="zaza.css">
    <title>Inscription</title>
</head>
<body>
    <h1>Inscription</h1>
    <?php if (!empty($errors)) {
        echo '<ul>';
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo '</ul>';
    } ?>
    <form method="post" action="">
        <label for="login">Login:</label>
        <input type="text" name="login" required><br>
        <label for="prenom">Prenom:</label>
        <input type="text" name="prenom" required><br>
        <label for="nom">Nom:</label>
        <input type="text" name="nom" required><br>
        <label for="password">Password:</label>
        <input type="password" name="password" required><br>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" required><br>
        <button type="submit">Inscription</button>
    </form>
</body>
</html>

