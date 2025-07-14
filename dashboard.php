<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Background Remover</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'templates/parts/header.php'; ?>

    <main>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <p>This is your dashboard. Here you can see your previous image removals and manage your API key.</p>

        <?php
        require_once 'database.php';
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT api_key FROM users WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        ?>
        <h2>Your API Key</h2>
        <p>Your API key is: <code><?php echo htmlspecialchars($user['api_key']); ?></code></p>
        <form action="auth.php?action=regenerate_api_key" method="POST">
            <button type="submit">Regenerate API Key</button>
        </form>

        <h2>Previous Removals</h2>
        <?php
        $stmt = $conn->prepare("SELECT image_name, date_time FROM api_usage WHERE user_id = ? ORDER BY date_time DESC");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        ?>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><a href="processed/<?php echo htmlspecialchars($row['image_name']); ?>" target="_blank"><?php echo htmlspecialchars($row['image_name']); ?></a></td>
                            <td><?php echo htmlspecialchars($row['date_time']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have no previous removals.</p>
        <?php endif; ?>
        <?php
        $stmt->close();
        $conn->close();
        ?>
    </main>

    <?php include 'templates/parts/footer.php'; ?>
</body>
</html>
