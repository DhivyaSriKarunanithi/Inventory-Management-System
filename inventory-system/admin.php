<?php
session_start();
include 'db.php';

$message = "";

// Handle Delete
if (isset($_POST['delete'])) {
    $deleteId = $_POST['delete_id'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $message = "🗑️ Product deleted!";
}

// Handle Update Product
if (isset($_POST['update_product'])) {
    $updateId = $_POST['update_id'];
    $updateName = $_POST['update_name'];
    $updateQty = $_POST['update_qty'];
    $updatePrice = $_POST['update_price'];

    $stmt = $conn->prepare("UPDATE products SET name = ?, quantity = ?, price = ? WHERE id = ?");
    $stmt->bind_param("sidi", $updateName, $updateQty, $updatePrice, $updateId);
    $stmt->execute();
    $message = "✅ Product updated!";
}

// Handle Add Product
if (isset($_POST['add_product'])) {
    $pname = $_POST['pname'];
    $qty = $_POST['qty'];
    $price = $_POST['price'];
    $stmt = $conn->prepare("INSERT INTO products (name, quantity, price) VALUES (?, ?, ?)");
    $stmt->bind_param("sid", $pname, $qty, $price);
    $stmt->execute();
    $message = "✅ Product added!";
}

// ... registration, login, logout code remains unchanged ...

?>
<!DOCTYPE html>
<html>
<head>
    <title>Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">

    <h2 class="text-center mb-4">🛒 Inventory Management System</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <?php if (!isset($_SESSION['username'])): ?>
        <!-- Registration & Login Forms here (unchanged) -->
    <?php else: ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> (<?= ucfirst(htmlspecialchars($_SESSION['role'])) ?>)</h4>
            <a href="?logout=1" class="btn btn-outline-secondary">Logout</a>
        </div>

        <?php if ($_SESSION['role'] == 'admin'): ?>

            <!-- Add Product -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5>Add Product</h5>
                    <form method="post">
                        <input type="text" name="pname" class="form-control mb-2" placeholder="Product Name" required>
                        <input type="number" name="qty" class="form-control mb-2" placeholder="Quantity" min="0" required>
                        <input type="number" step="0.01" name="price" class="form-control mb-2" placeholder="Price" min="0" required>
                        <button type="submit" name="add_product" class="btn btn-dark">Add Product</button>
                    </form>
                </div>
            </div>

            <!-- Products Table -->
            <h5>Available Products</h5>
            <table class="table table-bordered align-middle">
                <tr>
                    <th>ID</th><th>Name</th><th>Qty</th><th>Availability</th><th>Price</th><th>Actions</th>
                </tr>
                <?php
                $res = $conn->query("SELECT * FROM products");
                while ($row = $res->fetch_assoc()):
                ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= $row['quantity'] ?></td>
                        <td>
                            <?php if ($row['quantity'] > 0): ?>
                                <span class="badge bg-success">In Stock</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Out of Stock</span>
                            <?php endif; ?>
                        </td>
                        <td>₹<?= number_format($row['price'], 2) ?></td>
                        <td>
                            <!-- Update button triggers a form below -->
                            <button class="btn btn-sm btn-primary" onclick="document.getElementById('updateForm-<?= $row['id'] ?>').style.display='block'">Edit</button>
                            
                            <!-- Delete Form -->
                            <form method="post" style="display:inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="delete" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>

                    <!-- Hidden Update Form for each product -->
                    <tr id="updateForm-<?= $row['id'] ?>" style="display:none; background:#f8f9fa;">
                        <td colspan="6">
                            <form method="post" class="d-flex gap-2 align-items-center">
                                <input type="hidden" name="update_id" value="<?= $row['id'] ?>">
                                <input type="text" name="update_name" value="<?= htmlspecialchars($row['name']) ?>" class="form-control" required>
                                <input type="number" name="update_qty" value="<?= $row['quantity'] ?>" min="0" class="form-control" required>
                                <input type="number" step="0.01" name="update_price" value="<?= $row['price'] ?>" min="0" class="form-control" required>
                                <button type="submit" name="update_product" class="btn btn-success btn-sm">Save</button>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('updateForm-<?= $row['id'] ?>').style.display='none'">Cancel</button>
                            </form>
                        </td>
                    </tr>

                <?php endwhile; ?>
            </table>

        <?php else: ?>
            <!-- User Dashboard (unchanged) -->
        <?php endif; ?>
    <?php endif; ?>

</div>

<script>
    // Optional: You can add JS to close the update form on outside click if you want
</script>

</body>
</html>
