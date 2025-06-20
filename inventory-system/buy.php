<?php
session_start();
include 'db.php';

// Check if user is logged in and role is 'user'
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php"); // redirect to login page
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $productId = intval($_POST['product_id']);

    // Check product quantity
    $stmt = $conn->prepare("SELECT quantity FROM products WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($product = $result->fetch_assoc()) {
        if ($product['quantity'] > 0) {
            // Reduce quantity by 1
            $newQty = $product['quantity'] - 1;
            $stmt = $conn->prepare("UPDATE products SET quantity = ? WHERE id = ?");
            $stmt->bind_param("ii", $newQty, $productId);
            $stmt->execute();

            $_SESSION['message'] = "Purchase successful!";
        } else {
            $_SESSION['message'] = "Sorry, product is out of stock.";
        }
    } else {
        $_SESSION['message'] = "Product not found.";
    }
} else {
    $_SESSION['message'] = "Invalid request.";
}

header("Location: index.php");
exit;
