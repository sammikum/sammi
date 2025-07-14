<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Background Remover</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'templates/parts/header.php'; ?>

    <main>
        <div class="form-container">
            <h1>Register</h1>
            <form action="auth.php?action=register" method="POST">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <button type="submit">Register</button>
            </form>
        </div>
    </main>

    <?php include 'templates/parts/footer.php'; ?>
</body>
</html>
