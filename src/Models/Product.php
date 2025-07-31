<?php
namespace App\Models;

use App\Config\Database;
use App\Interfaces\CrudInterface;
use App\Traits\FileUpload;
use mysqli;

class Product implements CrudInterface 
{
    use FileUpload; // Include file upload functionality
    
    private mysqli $db;
    protected int $id;
    protected string $name;
    protected string $description;
    protected float $price;
    protected string $image;
    protected int $user_id;
    
    public function __construct() 
    {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Getters and Setters
    public function getId(): int 
    {
        return $this->id ?? 0;
    }
    
    public function setName(string $name): void 
    {
        if (strlen($name) < 2) {
            throw new \Exception("Product name must be at least 2 characters");
        }
        $this->name = $name;
    }
    
    public function getName(): string 
    {
        return $this->name ?? '';
    }
    
    public function setPrice(float $price): void 
    {
        if ($price <= 0) {
            throw new \Exception("Price must be greater than 0");
        }
        $this->price = $price;
    }
    
    public function getPrice(): float 
    {
        return $this->price ?? 0.0;
    }
    
    // CRUD Operations
    public function create(array $data): bool 
    {
        $imageName = null;
        
        // Handle file upload if image is provided
        if (isset($data['image']) && $data['image']['error'] === UPLOAD_ERR_OK) {
            $imageName = $this->uploadFile($data['image'], 'uploads/');
        }
        
        $stmt = $this->db->prepare("INSERT INTO products (name, description, price, image, user_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsi", $data['name'], $data['description'], $data['price'], $imageName, $data['user_id']);
        return $stmt->execute();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        return $product ?: null;
    }

    
    public function read(int $id = null): array 
    {
        if ($id) {
            $stmt = $this->db->prepare("
                SELECT p.*, u.username 
                FROM products p 
                JOIN users u ON p.user_id = u.id 
                WHERE p.id = ?
            ");
            $stmt->bind_param("i", $id);
        } else {
            $stmt = $this->db->prepare("
                SELECT p.*, u.username 
                FROM products p 
                JOIN users u ON p.user_id = u.id 
                ORDER BY p.created_at DESC
            ");
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function update(int $id, array $data): bool 
    {
        // Get current product for image handling
        $currentProduct = $this->read($id);
        $imageName = $currentProduct[0]['image'] ?? null;
        
        // Handle new image upload
        if (isset($data['image']) && $data['image']['error'] === UPLOAD_ERR_OK) {
            // Delete old image
            if ($imageName) {
                $this->deleteFile($imageName, 'uploads/');
            }
            $imageName = $this->uploadFile($data['image'], 'uploads/');
        }
        
        $stmt = $this->db->prepare("UPDATE products SET name = ?, description = ?, price = ?, image = ? WHERE id = ?");
        $stmt->bind_param("ssdsi", $data['name'], $data['description'], $data['price'], $imageName, $id);
        return $stmt->execute();
    }
    
    public function delete(int $id): bool 
    {
        // Get product to delete associated image
        $product = $this->read($id);
        if (!empty($product) && $product[0]['image']) {
            $this->deleteFile($product[0]['image'], 'uploads/');
        }
        
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function getByUser(int $userId): array 
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}