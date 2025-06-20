<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $quantity = (int)$_POST['quantity'];
    $price = (float)$_POST['price'];
    $category = $conn->real_escape_string($_POST['category']);

    $sql = "INSERT INTO products (name, quantity, price, category) VALUES ('$name', $quantity, $price, '$category')";

    if ($conn->query($sql) === TRUE) {
        header("Location: view_products.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Add Product</h2>
    <form method="post" action="">
        <div class="mb-3">
            <label class="form-label">Product Name:</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Quantity:</label>
            <input type="number" name="quantity" class="form-control" required min="0">
        </div>
        <div class="mb-3">
            <label class="form-label">Price (₹):</label>
            <input type="number" step="0.01" name="price" class="form-control" required min="0">
        </div>
        <div class="mb-3">
            <label class="form-label">Category:</label>
            <input type="text" name="category" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Add Product</button>
        <a href="view_products.php" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>
