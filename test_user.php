<?php
require_once 'vendor/autoload.php';

use App\Models\User;

// Test creating a user
$user = new User();

// Test data
$userData = [
    'username' => 'testuser',
    'email' => 'test@example.com',
    'password' => 'password123'
];

// Test create
echo "Testing User Creation...\n";
if ($user->create($userData)) {
    echo "✅ User created successfully!\n";
} else {
    echo "❌ Failed to create user\n";
}

// Test read all users
echo "\nTesting Read All Users...\n";
$users = $user->read();
foreach ($users as $u) {
    echo "User: {$u['username']} - {$u['email']}\n";
}

// Test login
echo "\nTesting Login...\n";
$loggedInUser = $user->login('test@example.com', 'password123');
if ($loggedInUser) {
    echo "✅ Login successful! Welcome {$loggedInUser['username']}\n";
} else {
    echo "❌ Login failed\n";
}