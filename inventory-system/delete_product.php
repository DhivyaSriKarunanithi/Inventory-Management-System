<?php
include 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: view_products.php");
    exit();
}

$id = (int)$_GET['id'];

$del_sql = "DELETE FROM products WHERE id = $id";

if ($conn->query($del_sql)) {
    header("Location: view_products.php");
    exit();
} else {
    echo "Error deleting product: " . $conn->error;
}
?>
