<?php
require_once '../vendor/autoload.php';

use App\Controllers\AuthController;
use App\Controllers\ProductController;

$auth = new AuthController();
$auth->requireAuth();

$productController = new ProductController();
// $message = '';
$message = $_GET['message'] ?? '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => $_POST['name'],
        'description' => $_POST['description'],
        'price' => $_POST['price'],
        'user_id' => $_SESSION['user_id'],
        'image' => $_FILES['image']
    ];
    $result = $productController->create($data);
    $message = $result['message'];
}

// Get all products for display
$products = $productController->getByUser($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Products</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <a href="dashboard.php" class="btn">Back to Dashboard</a>
    <h2>Create Product</h2>

    <?php if ($message): ?>
        <div class="message <?= strpos($message, 'success') ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="form">
        <input type="text" name="name" placeholder="Product Name" required>
        <textarea name="description" placeholder="Description"></textarea>
        <input type="number" name="price" step="0.01" placeholder="Price" required>
        <input type="file" name="image">
        <button type="submit">Add Product</button>
    </form>

    <h2>My Products</h2>
    <div class="products-grid">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <?php if ($product['image']): ?>
                    <img src="uploads/<?= htmlspecialchars($product['image']) ?>" alt="Product Image">
                <?php endif; ?>
                <h3><?= htmlspecialchars($product['name']) ?></h3>
                <p><?= htmlspecialchars($product['description']) ?></p>
                <p class="price">$<?= number_format($product['price'], 2) ?></p>
                <div>
                    <a href="edit_product.php?id=<?= $product['id'] ?>" class="btn">Edit</a>
                    <a href="delete_product.php?id=<?= $product['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    <a href="show_product.php?id=<?= $product['id'] ?>" class="btn btn-info btn-sm">View</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>