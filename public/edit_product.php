<?php
require_once '../vendor/autoload.php';

use App\Controllers\AuthController;
use App\Controllers\ProductController;

$auth = new AuthController();
$auth->requireAuth();

$productController = new ProductController();
$message = '';

if (!isset($_GET['id'])) {
    header('Location: products.php');
    exit;
}

$id = (int)$_GET['id'];
$product = $productController->getById($id);

// Redirect if not found or not owned by user
if (!$product || $product['user_id'] !== $_SESSION['user_id']) {
    header('Location: products.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => $_POST['name'],
        'description' => $_POST['description'],
        'price' => $_POST['price'],
        'image' => $_FILES['image']
    ];

    $result = $productController->update($id, $data);
    $message = $result['message'];

    if ($result['success']) {
        header("Location: products.php?message=" . urlencode($message));
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h2>Edit Product</h2>

    <?php if ($message): ?>
        <div class="message <?= strpos($message, 'success') ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="form">
        <input type="text" name="name" placeholder="Product Name" value="<?= htmlspecialchars($product['name']) ?>" required>
        <textarea name="description" placeholder="Description"><?= htmlspecialchars($product['description']) ?></textarea>
        <input type="number" name="price" step="0.01" placeholder="Price" value="<?= htmlspecialchars($product['price']) ?>" required>
        
        <?php if ($product['image']): ?>
            <img src="uploads/<?= htmlspecialchars($product['image']) ?>" alt="Current Image" style="max-width: 200px;"><br>
        <?php endif; ?>
        
        <input type="file" name="image">
        <button type="submit">Update Product</button>
    </form>
    
    <p><a href="products.php" class="btn">Back to Products</a></p>
</div>
</body>
</html>
