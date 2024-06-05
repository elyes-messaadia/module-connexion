<?php
session_start();

// Check if the user is logged in and is admin
if (!isset($_SESSION['login']) || $_SESSION['login'] !== 'admin') {
    header("Location: connexion.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'moduleconnexion');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM utilisateurs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin.php");
    exit();
}

// Fetch all users
$result = $conn->query("SELECT id, login, prenom, nom, password FROM utilisateurs");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration</title>
</head>
<body>
    <h1>Administration</h1>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Login</th>
            <th>Prenom</th>
            <th>Nom</th>
            <th>Password (hashed)</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['login']; ?></td>
            <td><?php echo $row['prenom']; ?></td>
            <td><?php echo $row['nom']; ?></td>
            <td><?php echo $row['password']; ?></td>
            <td>
                <a href="edit_user.php?id=<?php echo $row['id']; ?>">Edit</a>
                <a href="admin.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>


