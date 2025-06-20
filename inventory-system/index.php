<?php
session_start();
include 'db.php';

$message = "";

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (!in_array($role, ['admin', 'user'])) {
        $message = "Invalid role selected.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "Username already taken.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashed, $role);
            if ($stmt->execute()) {
                $message = "Registration successful. Please login.";
            } else {
                $message = "Registration failed. Try again.";
            }
        }
    }
}

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($user = $res->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $user['role'];
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $message = "Incorrect password.";
        }
    } else {
        $message = "User not found.";
    }
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_POST['add_product']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $pname = $_POST['pname'];
    $qty = intval($_POST['qty']);
    $price = floatval($_POST['price']);

    $stmt = $conn->prepare("INSERT INTO products (name, quantity, price) VALUES (?, ?, ?)");
    $stmt->bind_param("sid", $pname, $qty, $price);
    if ($stmt->execute()) {
        $message = "Product added!";
    } else {
        $message = "Failed to add product.";
    }
}

if (isset($_POST['delete']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $deleteId = intval($_POST['delete_id']);
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    if ($stmt->execute()) {
        $message = "Product deleted!";
    } else {
        $message = "Failed to delete product.";
    }
}

if (isset($_POST['update_product']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $updateId = intval($_POST['update_id']);
    $updateName = $_POST['update_name'];
    $updateQty = intval($_POST['update_qty']);
    $updatePrice = floatval($_POST['update_price']);

    $stmt = $conn->prepare("UPDATE products SET name = ?, quantity = ?, price = ? WHERE id = ?");
    $stmt->bind_param("sidi", $updateName, $updateQty, $updatePrice, $updateId);
    if ($stmt->execute()) {
        $message = "Product updated!";
    } else {
        $message = "Failed to update product.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Inventory Management</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
<style>
  body {
    /* Gradient background */
    background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    min-height: 100vh;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
  .header-banner {
    background: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1470&q=80') no-repeat center center/cover;
    height: 220px;
    border-radius: 0.75rem;
    margin-bottom: 30px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.15);
  }
  h1 {
    color: #3b3a66;
    text-shadow: 1px 1px 4px #ddd;
  }
  .card {
    box-shadow: 0 4px 15px rgb(0 0 0 / 0.1);
  }
  .btn-primary {
    background: #5a5ac9;
    border: none;
  }
  .btn-primary:hover {
    background: #4747b8;
  }
  .btn-success {
    background: #3bbd99;
    border: none;
  }
  .btn-success:hover {
    background: #2d997f;
  }
  .btn-danger {
    background: #e55353;
    border: none;
  }
  .btn-danger:hover {
    background: #bf3c3c;
  }
  .badge {
    font-size: 0.9em;
  }
  .alert-info {
    background: #bbdefb;
    color: #1a237e;
    font-weight: 600;
  }
  footer {
    text-align: center;
    margin-top: 60px;
    color: #555;
  }
</style>
</head>
<body>

<div class="container py-5">

  <div class="header-banner rounded"></div>

  <h1 class="mb-4 text-center"><i class="bi bi-box-seam"></i> Inventory Management System</h1>

  <?php if ($message): ?>
    <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <?php if (!isset($_SESSION['username'])): ?>

    <div class="row justify-content-center gap-5">

      <!-- Registration Card -->
      <div class="card col-md-5 p-4 bg-white rounded">
        <h3 class="mb-3 text-primary"><i class="bi bi-person-plus-fill"></i> Register</h3>
        <form method="post">
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select" required>
              <option value="user">User</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <button type="submit" name="register" class="btn btn-success w-100">
            <i class="bi bi-check-circle"></i> Register
          </button>
        </form>
      </div>

      <!-- Login Card -->
      <div class="card col-md-5 p-4 bg-white rounded">
        <h3 class="mb-3 text-primary"><i class="bi bi-box-arrow-in-right"></i> Login</h3>
        <form method="post">
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required />
          </div>
          <button type="submit" name="login" class="btn btn-primary w-100">
            <i class="bi bi-door-open"></i> Login
          </button>
        </form>
      </div>

    </div>

  <?php else: ?>

    <div class="mb-3 text-end">
      <span class="badge bg-secondary fs-6 me-3">
        <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['username']) ?> (<?= htmlspecialchars($_SESSION['role']) ?>)
      </span>
      <form method="post" class="d-inline">
        <button type="submit" name="logout" class="btn btn-outline-danger btn-sm">
          <i class="bi bi-box-arrow-right"></i> Logout
        </button>
      </form>
    </div>

    <?php if ($_SESSION['role'] === 'admin'): ?>
      <div class="card p-4 mb-4 bg-white rounded">
        <h3 class="text-primary mb-3"><i class="bi bi-gear-fill"></i> Admin Panel - Manage Products</h3>

        <!-- Add product form -->
        <form method="post" class="row g-3 align-items-end mb-4">
          <div class="col-md-4">
            <label class="form-label">Product Name</label>
            <input type="text" name="pname" class="form-control" required />
          </div>
          <div class="col-md-3">
            <label class="form-label">Quantity</label>
            <input type="number" name="qty" class="form-control" min="0" required />
          </div>
          <div class="col-md-3">
            <label class="form-label">Price (₹)</label>
            <input type="number" step="0.01" name="price" class="form-control" min="0" required />
          </div>
          <div class="col-md-2">
            <button type="submit" name="add_product" class="btn btn-success w-100">
              <i class="bi bi-plus-circle"></i> Add Product
            </button>
          </div>
        </form>

        <!-- Product list -->
        <table class="table table-striped table-bordered align-middle">
          <thead class="table-primary">
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Quantity</th>
              <th>Price (₹)</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $res = $conn->query("SELECT * FROM products");
            while ($row = $res->fetch_assoc()):
            ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td><?= $row['quantity'] ?></td>
              <td><?= number_format($row['price'], 2) ?></td>
              <td class="d-flex gap-2">
                <form method="post" onsubmit="return confirm('Delete product <?= htmlspecialchars($row['name']) ?>?');">
                  <input type="hidden" name="delete_id" value="<?= $row['id'] ?>" />
                  <button type="submit" name="delete" class="btn btn-danger btn-sm" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>

                <button class="btn btn-primary btn-sm" onclick="toggleUpdateForm(<?= $row['id'] ?>)" title="Edit">
                  <i class="bi bi-pencil-square"></i>
                </button>
              </td>
            </tr>
            <tr id="update-form-<?= $row['id'] ?>" style="display:none;" class="table-light">
              <form method="post">
                <td><?= $row['id'] ?></td>
                <td><input type="text" name="update_name" value="<?= htmlspecialchars($row['name']) ?>" class="form-control" required /></td>
                <td><input type="number" name="update_qty" value="<?= $row['quantity'] ?>" min="0" class="form-control" required /></td>
                <td><input type="number" step="0.01" name="update_price" value="<?= $row['price'] ?>" min="0" class="form-control" required /></td>
                <td>
                  <input type="hidden" name="update_id" value="<?= $row['id'] ?>" />
                  <button type="submit" name="update_product" class="btn btn-success btn-sm">
                    <i class="bi bi-check2-circle"></i> Save
                  </button>
                  <button type="button" class="btn btn-secondary btn-sm" onclick="toggleUpdateForm(<?= $row['id'] ?>)">
                    <i class="bi bi-x-circle"></i> Cancel
                  </button>
                </td>
              </form>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>

    <?php else: ?>
      <div class="card p-4 bg-white rounded shadow-sm">
        <h3 class="text-primary mb-4"><i class="bi bi-bag-fill"></i> Available Products</h3>
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Name</th>
              <th>Price (₹)</th>
              <th>Quantity</th>
              <th>Buy</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $res = $conn->query("SELECT * FROM products");
            while ($row = $res->fetch_assoc()):
            ?>
            <tr>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td>₹ <?= number_format($row['price'], 2) ?></td>
              <td><?= $row['quantity'] ?></td>
              <td>
                <?php if ($row['quantity'] > 0): ?>
                  <form method="post" action="buy.php">
                    <input type="hidden" name="product_id" value="<?= $row['id'] ?>" />
                    <button type="submit" class="btn btn-primary btn-sm">
                      <i class="bi bi-cart-plus"></i> Buy
                    </button>
                  </form>
                <?php else: ?>
                  <span class="badge bg-danger">Out of stock</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

  <?php endif; ?>

</div>

<script>
  function toggleUpdateForm(id) {
    const form = document.getElementById('update-form-' + id);
    if (form.style.display === 'none' || form.style.display === '') {
      form.style.display = 'table-row';
    } else {
      form.style.display = 'none';
    }
  }
</script>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<footer>
  <small>&copy; <?= date('Y') ?> Inventory Management System</small>
</footer>

</body>
</html>
