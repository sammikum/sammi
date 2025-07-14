<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Background Remover</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <?php include 'parts/header.php'; ?>

    <main>
        <h1>Admin Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</p>

        <?php
        require_once '../database.php';
        $conn = getDBConnection();

        $user_count_result = $conn->query("SELECT COUNT(*) as count FROM users");
        $user_count = $user_count_result->fetch_assoc()['count'];

        $processed_images_count_result = $conn->query("SELECT COUNT(*) as count FROM api_usage");
        $processed_images_count = $processed_images_count_result->fetch_assoc()['count'];

        $conn->close();
        ?>

        <div class="stats-container">
            <div class="stat-card">
                <h2>Total Users</h2>
                <p><?php echo $user_count; ?></p>
            </div>
            <div class="stat-card">
                <h2>Total Images Processed</h2>
                <p><?php echo $processed_images_count; ?></p>
            </div>
        </div>
    </main>

    <?php include 'parts/footer.php'; ?>
</body>
</html>
