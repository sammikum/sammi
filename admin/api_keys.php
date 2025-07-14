<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}
require_once '../database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage API Keys - Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <?php include 'parts/header.php'; ?>

    <main>
        <h1>Manage API Keys</h1>
        <?php
        $conn = getDBConnection();
        $result = $conn->query("SELECT u.username, u.api_key FROM users u");
        ?>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>API Key</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><code><?php echo htmlspecialchars($row['api_key']); ?></code></td>
                        <td>
                            <a href="api_key_revoke.php?username=<?php echo $row['username']; ?>" onclick="return confirm('Are you sure?')">Revoke</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php
        $conn->close();
        ?>
    </main>

    <?php include 'parts/footer.php'; ?>
</body>
</html>
