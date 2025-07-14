<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Background Remover</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'templates/parts/header.php'; ?>

    <main>
        <div class="form-container">
            <h1>Login</h1>
            <form action="auth.php?action=login" method="POST">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <button type="submit">Login</button>
            </form>
        </div>
    </main>

    <?php include 'templates/parts/footer.php'; ?>
</body>
</html>
