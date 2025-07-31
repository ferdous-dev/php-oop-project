<?php
require_once '../vendor/autoload.php';

use App\Controllers\AuthController;
use App\Controllers\ProductController;

$auth = new AuthController();
$auth->requireAuth();

$productController = new ProductController();

if (!isset($_GET['id'])) {
    header('Location: products.php');
    exit;
}

$id = (int)$_GET['id'];
$product = $productController->getById($id);

// Prevent unauthorized deletion
if (!$product || $product['user_id'] !== $_SESSION['user_id']) {
    header('Location: products.php');
    exit;
}

$result = $productController->delete($id);
$message = $result['message'];

// Redirect with message
header("Location: products.php?message=" . urlencode($message));
exit;
