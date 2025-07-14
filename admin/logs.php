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
    <title>API Usage Logs - Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <?php include 'parts/header.php'; ?>

    <main>
        <h1>API Usage Logs</h1>
        <?php
        $conn = getDBConnection();
        $result = $conn->query("SELECT u.username, au.image_name, au.date_time, au.status FROM api_usage au JOIN users u ON au.user_id = u.id ORDER BY au.date_time DESC");
        ?>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Image Name</th>
                    <th>Date & Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['image_name']); ?></td>
                        <td><?php echo $row['date_time']; ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
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
