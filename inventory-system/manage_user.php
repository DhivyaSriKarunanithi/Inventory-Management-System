<?php
include 'db.php';
session_start();

// Allow only admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied.";
    exit();
}

// Handle user deletion
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);

    // Prevent admin from deleting themselves
    $current_user = $_SESSION['username'];
    $check = $conn->query("SELECT * FROM users WHERE id=$delete_id");
    $row = $check->fetch_assoc();

    if ($row && $row['username'] !== $current_user) {
        $conn->query("DELETE FROM users WHERE id=$delete_id");
        header("Location: manage_users.php");
        exit();
    }
}

// Fetch all users
$result = $conn->query("SELECT id, username, role FROM users");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">👥 User Management</h2>

    <table class="table table-bordered bg-white shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while($user = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= $user['username'] ?></td>
                <td><?= ucfirst($user['role']) ?></td>
                <td>
                    <?php if ($user['username'] !== $_SESSION['username']): ?>
                        <a href="?delete=<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                    <?php else: ?>
                        <span class="text-muted">Logged-in</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <a href="admin.php" class="btn btn-secondary mt-3">← Back to Dashboard</a>
</div>
</body>
</html>
