<?php
require_once '../vendor/autoload.php';

use App\Controllers\AuthController;

$auth = new AuthController();

if ($auth->isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>PHP CRUD App</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Welcome to PHP CRUD App</h1>
        <div class="links">
            <a href="login.php" class="btn">Login</a>
            <a href="register.php" class="btn">Register</a>
        </div>
    </div>
</body>
</html>