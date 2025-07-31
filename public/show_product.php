<?php
require_once '../vendor/autoload.php';

use App\Controllers\ProductController;

$product = null;
$error = null;

// Get ID from URL
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $controller = new ProductController();
    $product = $controller->show($id);
} else {
    $error = "No product ID provided.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Product</title>
    <link rel="stylesheet" href="assets/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2>Product Details</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif (!$product): ?>
        <div class="alert alert-warning">Product not found.</div>
    <?php else: ?>
        <div class="card" style="width: 30rem;">
            <?php if ($product['image']): ?>
                <img src="uploads/<?= htmlspecialchars($product['image']) ?>" class="card-img-top" alt="Product Image">
            <?php endif; ?>
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                <p class="card-text"><?= htmlspecialchars($product['description']) ?></p>
                <p><strong>Price:</strong> $<?= htmlspecialchars($product['price']) ?></p>
                <a href="products.php" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    <?php endif; ?>
</body>
</html>
