<?php
namespace App\Traits;

trait FileUpload 
{
    protected function uploadFile(array $file, string $uploadDir = 'uploads/'): ?string 
    {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            throw new \Exception('Invalid file type. Only JPG, PNG, GIF allowed.');
        }
        
        if ($file['size'] > $maxSize) {
            throw new \Exception('File too large. Maximum 5MB allowed.');
        }
        
        // Generate unique filename
        $fileName = uniqid() . '_' . basename($file['name']);
        $uploadPath = $uploadDir . $fileName;
        
        // Create directory if doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return $fileName;
        }
        
        return null;
    }
    
    protected function deleteFile(string $fileName, string $uploadDir = 'uploads/'): bool 
    {
        $filePath = $uploadDir . $fileName;
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return false;
    }
}