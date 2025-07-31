<?php
namespace App\Controllers;

use App\Models\User;

class AuthController 
{
    private User $userModel;
    
    public function __construct() 
    {
        $this->userModel = new User();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function register(array $data): array 
    {
        try {
            // Validation
            if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
                return ['success' => false, 'message' => 'All fields are required'];
            }
            
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'message' => 'Invalid email format'];
            }
            
            if (strlen($data['password']) < 6) {
                return ['success' => false, 'message' => 'Password must be at least 6 characters'];
            }
            
            if ($this->userModel->emailExists($data['email'])) {
                return ['success' => false, 'message' => 'Email already exists'];
            }
            
            if ($this->userModel->create($data)) {
                return ['success' => true, 'message' => 'Registration successful'];
            }
            
            return ['success' => false, 'message' => 'Registration failed'];
            
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    public function login(string $email, string $password): array 
    {
        try {
            if (empty($email) || empty($password)) {
                return ['success' => false, 'message' => 'Email and password are required'];
            }
            
            $user = $this->userModel->login($email, $password);
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                
                return ['success' => true, 'message' => 'Login successful'];
            }
            
            return ['success' => false, 'message' => 'Invalid email or password'];
            
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    public function logout(): void 
    {
        session_unset();
        session_destroy();
        header('Location: login.php');
        exit;
    }
    
    public function isLoggedIn(): bool 
    {
        return isset($_SESSION['user_id']);
    }
    
    public function requireAuth(): void 
    {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }
    
    public function getCurrentUserId(): ?int 
    {
        return $_SESSION['user_id'] ?? null;
    }
    
    public function getCurrentUser(): ?array 
    {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'email' => $_SESSION['email']
            ];
        }
        return null;
    }
}