<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: connexion.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prenom = trim($_POST['prenom']);
    $nom = trim($_POST['nom']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate inputs
    $errors = [];
    if (empty($prenom) || strlen($prenom) > 255) {
        $errors[] = "Invalid prenom.";
    }
    if (empty($nom) || strlen($nom) > 255) {
        $errors[] = "Invalid nom.";
    }
    if (!empty($password) && strlen($password) < 8) {
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

        // Update user information
        if (!empty($password)) {
            $hashed_password = hash('sha256', $password);
            $stmt = $conn->prepare("UPDATE utilisateurs SET prenom = ?, nom = ?, password = ? WHERE id = ?");
            $stmt->bind_param("sssi", $prenom, $nom, $hashed_password, $_SESSION['id']);
        } else {
            $stmt = $conn->prepare("UPDATE utilisateurs SET prenom = ?, nom = ? WHERE id = ?");
            $stmt->bind_param("ssi", $prenom, $nom, $_SESSION['id']);
        }
        $stmt->execute();

        $_SESSION['prenom'] = $prenom;
        $_SESSION['nom'] = $nom;

        echo "Profile updated successfully.";
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
    <title>Profil</title>
</head>
<body>
    <h1>Modifier Profil</h1>
    <?php if (!empty($errors)) {
        echo '<ul>';
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo '</ul>';
    } ?>
    <form method="post" action="">
        <label for="prenom">Prenom:</label>
        <input type="text" name="prenom" value="<?php echo $_SESSION['prenom']; ?>" required><br>
        <label for="nom">Nom:</label>
        <input type="text" name="nom" value="<?php echo $_SESSION['nom']; ?>" required><br>
        <label for="password">Password:</label>
        <input type="password" name="password"><br>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password"><br>
        <button type="submit">Update</button>
    </form>
</body>
</html>

