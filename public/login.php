<?php
require_once '../vendor/autoload.php';

use App\Controllers\AuthController;

$auth = new AuthController();
$message = $_GET['message'] ?? '';

if ($_POST) {
    $result = $auth->login($_POST['email'], $_POST['password']);
    $message = $result['message'];
    
    if ($result['success']) {
        header('Location: dashboard.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <form method="POST" class="form">
            <h2>Login</h2>
            
            <?php if ($message): ?>
                <div class="message <?= strpos($message, 'successful') !== false ? 'success' : 'error' ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
            
            <p><a href="register.php">Don't have an account? Register</a></p>
        </form>
    </div>
</body>
</html>