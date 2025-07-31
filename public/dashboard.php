<?php
require_once '../vendor/autoload.php';

use App\Controllers\AuthController;
use App\Controllers\ProductController;

$auth = new AuthController();
$auth->requireAuth();

$productController = new ProductController();
$products = $productController->getAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Dashboard</h1>
            <div>
                Welcome, <?= htmlspecialchars($_SESSION['username']) ?>
                <a href="products.php" class="btn">Manage Products</a>
                <a href="?logout=1" class="btn btn-danger">Logout</a>
            </div>
        </header>
        
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <?php if ($product['image']): ?>
                        <img src="uploads/<?= htmlspecialchars($product['image']) ?>" alt="Product Image">
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <p><?= htmlspecialchars($product['description']) ?></p>
                    <p class="price">$<?= number_format($product['price'], 2) ?></p>
                    <p class="author">By: <?= htmlspecialchars($product['username']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>

<?php
if (isset($_GET['logout'])) {
    $auth->logout();
}
?>