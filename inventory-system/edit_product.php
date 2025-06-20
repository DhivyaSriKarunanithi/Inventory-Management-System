<?php
include 'db.php';
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied. Only admins allowed.";
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: view_products.php");
    exit();
}

$id = (int)$_GET['id'];

$result = $conn->query("SELECT * FROM products WHERE id = $id");
if (!$result || $result->num_rows === 0) {
    header("Location: view_products.php");
    exit();
}

$product = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $quantity = (int)$_POST['quantity'];
    $price = (float)$_POST['price'];
    $category = $conn->real_escape_string($_POST['category']);

    $update_sql = "UPDATE products SET name='$name', quantity=$quantity, price=$price, category='$category' WHERE id=$id";
    if ($conn->query($update_sql)) {
        header("Location: view_products.php");
        exit();
    } else {
        echo "Error updating product: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h2>Edit Product</h2>
    <form method="post">
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Quantity</label>
            <input type="number" name="quantity" class="form-control" value="<?= $product['quantity'] ?>" required min="0">
        </div>
        <div class="mb-3">
            <label>Price (₹)</label>
            <input type="number" step="0.01" name="price" class="form-control" value="<?= $product['price'] ?>" required min="0">
        </div>
        <div class="mb-3">
            <label>Category</label>
            <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($product['category']) ?>" required>
        </div>
        <button class="btn btn-primary" type="submit">Update</button>
        <a href="view_products.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
