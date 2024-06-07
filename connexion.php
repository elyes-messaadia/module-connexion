<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = trim($_POST['login']);
    $password = $_POST['password'];

    // Validate inputs
    $errors = [];
    if (empty($login) || strlen($login) > 255) {
        $errors[] = "Invalid login.";
    }
    if (strlen($password) < 1) {
        $errors[] = "Invalid password.";
    }

    if (empty($errors)) {
        // Database connection
        $conn = new mysqli('localhost', 'root', '', 'moduleconnexion');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Validate user
        $hashed_password = hash('sha256', $password);
        $stmt = $conn->prepare("SELECT id, prenom, nom, login FROM utilisateurs WHERE login = ? AND password = ?");
        $stmt->bind_param("ss", $login, $hashed_password);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $prenom, $nom, $login);
            $stmt->fetch();
            $_SESSION['id'] = $id;
            $_SESSION['login'] = $login;
            $_SESSION['prenom'] = $prenom;
            $_SESSION['nom'] = $nom;

            if ($login == 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: profil.php");
            }
        } else {
            $errors[] = "Invalid login or password.";
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
    <title>Connexion</title>
</head>
<body>
    <h1>Connexion</h1>
    <?php if (!empty($errors)) {
        echo '<ul>';
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo '</ul>';
    } ?>
    <!-- What should i put in the form action attribute? -->
    <form method="post" action=""> 
        <label for="login">Login:</label>
        <input type="text" name="login" required><br>
        <label for="password">Password:</label>
        <input type="password" name="password" required><br>
        <button type="submit">Connexion</button>
    </form>
</body>
</html>
