<?php
namespace App\Controllers;

use App\Models\Product;

class ProductController 
{
    private Product $productModel;
    
    public function __construct() 
    {
        $this->productModel = new Product();
    }
    
    public function create(array $data): array 
    {
        try {
            // Validation
            if (empty($data['name'])) {
                return ['success' => false, 'message' => 'Product name is required'];
            }
            
            if (empty($data['price']) || !is_numeric($data['price']) || $data['price'] <= 0) {
                return ['success' => false, 'message' => 'Valid price is required'];
            }
            
            if (empty($data['user_id'])) {
                return ['success' => false, 'message' => 'User ID is required'];
            }
            
            if ($this->productModel->create($data)) {
                return ['success' => true, 'message' => 'Product created successfully'];
            }
            
            return ['success' => false, 'message' => 'Failed to create product'];
            
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function show($id)
    {   
        $result = $this->productModel->read($id);
        return !empty($result) ? $result[0] : null;
    }

    
    public function update(int $id, array $data): array 
    {
        try {
            if (empty($data['name'])) {
                return ['success' => false, 'message' => 'Product name is required'];
            }
            
            if (empty($data['price']) || !is_numeric($data['price']) || $data['price'] <= 0) {
                return ['success' => false, 'message' => 'Valid price is required'];
            }
            
            if ($this->productModel->update($id, $data)) {
                return ['success' => true, 'message' => 'Product updated successfully'];
            }
            
            return ['success' => false, 'message' => 'Failed to update product'];
            
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    public function delete(int $id): array 
    {
        try {
            if ($this->productModel->delete($id)) {
                return ['success' => true, 'message' => 'Product deleted successfully'];
            }
            
            return ['success' => false, 'message' => 'Failed to delete product'];
            
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    public function getAll(): array 
    {
        return $this->productModel->read();
    }
    
    public function getById(int $id): ?array 
    {
        $result = $this->productModel->read($id);
        return !empty($result) ? $result[0] : null;
    }
    
    public function getByUser(int $userId): array 
    {
        return $this->productModel->getByUser($userId);
    }
}