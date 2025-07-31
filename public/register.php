<?php
require_once '../vendor/autoload.php';

use App\Controllers\AuthController;

$auth = new AuthController();
$message = '';

if ($_POST) {
    $result = $auth->register($_POST);
    $message = $result['message'];
    
    if ($result['success']) {
        header('Location: login.php?message=' . urlencode($message));
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <form method="POST" class="form">
            <h2>Register</h2>
            
            <?php if ($message): ?>
                <div class="message error"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
            
            <p><a href="login.php">Already have an account? Login</a></p>
        </form>
    </div>
</body>
</html>