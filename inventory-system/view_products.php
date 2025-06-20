<?php
include 'db.php';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT * FROM products";

if (!empty($search)) {
    $sql .= " WHERE name LIKE '%" . $conn->real_escape_string($search) . "%'";
}

$sql .= " ORDER BY name ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Product List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Inventory Products</h2>

    <form method="GET" class="row mb-3">
        <div class="col-md-8">
            <input type="text" name="search" class="form-control" placeholder="Search product name..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="view_products.php" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <a href="add_product.php" class="btn btn-success mb-3">+ Add New Product</a>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Quantity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row["id"] ?></td>
                    <td><?= $row["name"] ?></td>
                    <td><?= $row["quantity"] ?></td>
                    <td>
                        <a href="edit_product.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete_product.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure to delete?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="4" class="text-center">No products found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
