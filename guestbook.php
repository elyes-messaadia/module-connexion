<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livre d'Or</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Livre d'Or</h1>
    <form id="guestbook-form">
        <label for="name">Nom:</label>
        <input type="text" id="name" name="name" required>
        
        <label for="message">Message:</label>
        <textarea id="message" name="message" required></textarea>
        
        <button type="submit">Envoyer</button>
    </form>

    <h2>Messages</h2>
    <div id="messages"></div>

    <script src="script.js"></script>
</body>
</html>
