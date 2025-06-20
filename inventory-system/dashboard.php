<?php
include 'db.php';
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied.";
    exit();
}

$total = $conn->query("SELECT COUNT(*) as total FROM products")->fetch_assoc()['total'];
$low_stock = $conn->query("SELECT COUNT(*) as total FROM products WHERE quantity <= 5")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Welcome, <?= $_SESSION['username'] ?> (Admin)</h2>
    <div class="row text-center">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>Total Products</h5>
                    <h2><?= $total ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5>Low Stock Items</h5>
                    <h2><?= $low_stock ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Existing links -->
    <a href="view_products.php" class="btn btn-dark mt-4">→ Manage Products</a>
    <a href="manage_users.php" class="btn btn-info mt-4">👥 Manage Users</a>
    <a href="logout.php" class="btn btn-outline-secondary mt-4">Logout</a>
</div>
</body>
</html>
