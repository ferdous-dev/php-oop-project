<?php
namespace App\Models;

use App\Config\Database;
use App\Interfaces\CrudInterface;
use mysqli;

class User implements CrudInterface 
{
    private mysqli $db;
    protected int $id;
    protected string $username;
    protected string $email;
    protected string $password;
    
    public function __construct() 
    {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Getters and Setters (Encapsulation)
    public function getId(): int 
    {
        return $this->id ?? 0;
    }
    
    public function setUsername(string $username): void 
    {
        if (strlen($username) < 3) {
            throw new \Exception("Username must be at least 3 characters");
        }
        $this->username = $username;
    }
    
    public function getUsername(): string 
    {
        return $this->username ?? '';
    }
    
    public function setEmail(string $email): void 
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Invalid email format");
        }
        $this->email = $email;
    }
    
    public function getEmail(): string 
    {
        return $this->email ?? '';
    }
    
    // CRUD Operations
    public function create(array $data): bool 
    {
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt->bind_param("sss", $data['username'], $data['email'], $hashedPassword);
        return $stmt->execute();
    }
    
    public function read(int $id = null): array 
    {
        if ($id) {
            $stmt = $this->db->prepare("SELECT id, username, email, created_at FROM users WHERE id = ?");
            $stmt->bind_param("i", $id);
        } else {
            $stmt = $this->db->prepare("SELECT id, username, email, created_at FROM users");
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function update(int $id, array $data): bool 
    {
        $stmt = $this->db->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $data['username'], $data['email'], $id);
        return $stmt->execute();
    }
    
    public function delete(int $id): bool 
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    // Authentication methods
    public function login(string $email, string $password): ?array 
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user && password_verify($password, $user['password'])) {
            // Don't return password in the result
            unset($user['password']);
            return $user;
        }
        return null;
    }
    
    public function emailExists(string $email): bool 
    {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
}