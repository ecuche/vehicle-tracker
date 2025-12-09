<?php
namespace App\Core;

use Dotenv\Dotenv;

class Upload {
    private $uploadPath;
    private $maxFileSize;
    private $allowedTypes;
    private $errors = [];

    public function __construct() {
        // Load environment variables
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->load();

        $this->uploadPath = $_ENV['UPLOAD_PATH'] ?? dirname(__DIR__, 2) . '/public/assets/uploads/';
        $this->maxFileSize = $_ENV['UPLOAD_MAX_SIZE'] ?? 5 * 1024 * 1024; // 5MB default
        $this->allowedTypes = explode(',', $_ENV['UPLOAD_ALLOWED_TYPES'] ?? 'image/jpeg,image/png,image/gif,application/pdf');

        // Create upload directories if they don't exist
        $this->createUploadDirectories();
    }

    private function createUploadDirectories() {
        $directories = [
            'profiles',
            'vehicles/images',
            'vehicles/documents'
        ];

        foreach ($directories as $directory) {
            $fullPath = $this->uploadPath . $directory;
            if (!is_dir($fullPath)) {
                mkdir($fullPath, 0755, true);
            }
        }
    }

    public function handleProfilePicture($file) {
        $validation = Validator::validateFileUpload($file, [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp'
        ], $this->maxFileSize);

        if (!empty($validation)) {
            return ['success' => false, 'error' => implode(', ', $validation)];
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'profile_' . uniqid() . '_' . time() . '.' . $extension;
        $filePath = "{$this->uploadPath}profiles/{$filename}";

        // Compress and optimize image
        if (!$this->processImage($file['tmp_name'], $filePath)) {
            return ['success' => false, 'error' => 'Failed to process image'];
        }

        return [
            'success' => true,
            'file_path' => 'profiles/' . $filename,
            'full_path' => $filePath
        ];
    }

    public function handleVehicleImages($files) {
        $results = [];
        
        // Handle multiple files
        if (is_array($files['name'])) {
            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $file = [
                        'name' => $files['name'][$i],
                        'type' => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error' => $files['error'][$i],
                        'size' => $files['size'][$i]
                    ];

                    $result = $this->handleSingleVehicleImage($file);
                    if ($result['success']) {
                        $results[] = $result['file_path'];
                    } else {
                        $this->errors[] = $result['error'];
                    }
                }
            }
        } else {
            // Single file
            if ($files['error'] === UPLOAD_ERR_OK) {
                $result = $this->handleSingleVehicleImage($files);
                if ($result['success']) {
                    $results[] = $result['file_path'];
                } else {
                    $this->errors[] = $result['error'];
                }
            }
        }

        return [
            'success' => empty($this->errors),
            'file_paths' => $results,
            'errors' => $this->errors
        ];
    }

    private function handleSingleVehicleImage($file) {
        $validation = Validator::validateFileUpload($file, [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp'
        ], $this->maxFileSize);

        if (!empty($validation)) {
            return ['success' => false, 'error' => implode(', ', $validation)];
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'vehicle_' . uniqid() . '_' . time() . '.' . $extension;
        $filePath = $this->uploadPath . '/vehicles/images/' . $filename;

        // Compress and optimize image
        if (!$this->processImage($file['tmp_name'], $filePath)) {
            return ['success' => false, 'error' => 'Failed to process image'];
        }

        return [
            'success' => true,
            'file_path' => 'vehicles/images/' . $filename,
            'full_path' => $filePath
        ];
    }

    public function handleVehicleDocuments($files) {
        $results = [];
        
        // Handle multiple files
        if (is_array($files['name'])) {
            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $file = [
                        'name' => $files['name'][$i],
                        'type' => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error' => $files['error'][$i],
                        'size' => $files['size'][$i]
                    ];

                    $result = $this->handleSingleVehicleDocument($file);
                    if ($result['success']) {
                        $results[] = $result['file_path'];
                    } else {
                        $this->errors[] = $result['error'];
                    }
                }
            }
        } else {
            // Single file
            if ($files['error'] === UPLOAD_ERR_OK) {
                $result = $this->handleSingleVehicleDocument($files);
                if ($result['success']) {
                    $results[] = $result['file_path'];
                } else {
                    $this->errors[] = $result['error'];
                }
            }
        }

        return [
            'success' => empty($this->errors),
            'file_paths' => $results,
            'errors' => $this->errors
        ];
    }

    private function handleSingleVehicleDocument($file) {
        $validation = Validator::validateFileUpload($file, $this->allowedTypes, $this->maxFileSize);

        if (!empty($validation)) {
            return ['success' => false, 'error' => implode(', ', $validation)];
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'document_' . uniqid() . '_' . time() . '.' . $extension;
        $filePath = $this->uploadPath . '/vehicles/documents/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            return ['success' => false, 'error' => 'Failed to upload document'];
        }

        return [
            'success' => true,
            'file_path' => 'vehicles/documents/' . $filename,
            'full_path' => $filePath
        ];
    }

    private function processImage($sourcePath, $destinationPath) {
        try {
            $imageInfo = getimagesize($sourcePath);
            if (!$imageInfo) {
                return false;
            }

            $mimeType = $imageInfo['mime'];
            $quality = 85; // Quality for JPEG images

            switch ($mimeType) {
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($sourcePath);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($sourcePath);
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($sourcePath);
                    break;
                case 'image/webp':
                    $image = imagecreatefromwebp($sourcePath);
                    break;
                default:
                    return false;
            }

            if (!$image) {
                return false;
            }

            // Get original dimensions
            $width = imagesx($image);
            $height = imagesy($image);

            // Calculate new dimensions (max 1200px on the longest side)
            $maxDimension = 1200;
            if ($width > $height && $width > $maxDimension) {
                $newWidth = $maxDimension;
                $newHeight = intval($height * $maxDimension / $width);
            } elseif ($height > $maxDimension) {
                $newHeight = $maxDimension;
                $newWidth = intval($width * $maxDimension / $height);
            } else {
                $newWidth = $width;
                $newHeight = $height;
            }

            // Create new image
            $newImage = imagecreatetruecolor($newWidth, $newHeight);

            // Preserve transparency for PNG and GIF
            if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
                imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 0, 0, 0, 127));
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
            }

            // Resize image
            imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            // Save image
            switch ($mimeType) {
                case 'image/jpeg':
                    imagejpeg($newImage, $destinationPath, $quality);
                    break;
                case 'image/png':
                    imagepng($newImage, $destinationPath, 9); // Compression level for PNG
                    break;
                case 'image/gif':
                    imagegif($newImage, $destinationPath);
                    break;
                case 'image/webp':
                    imagewebp($newImage, $destinationPath, $quality);
                    break;
            }

            // Free memory
            imagedestroy($image);
            imagedestroy($newImage);

            return true;
        } catch (\Exception $e) {
            error_log("Image processing error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteFile($filePath) {
        $fullPath = $this->uploadPath . $filePath;
        
        if (file_exists($fullPath) && is_file($fullPath)) {
            return unlink($fullPath);
        }
        
        return false;
    }

    public function getFileUrl($filePath) {
        if (empty($filePath)) {
            return null;
        }
        
        return "/public/assets/uploads/{$filePath}";
    }

    public function getFilePath($filePath) {
        if (empty($filePath)) {
            return null;
        }
        
        return $this->uploadPath . $filePath;
    }

    public function fileExists($filePath) {
        if (empty($filePath)) {
            return false;
        }
        
        return file_exists($this->uploadPath . $filePath);
    }

    public function getFileSize($filePath) {
        if (!$this->fileExists($filePath)) {
            return 0;
        }
        
        return filesize($this->uploadPath . $filePath);
    }

    public function getFileMimeType($filePath) {
        if (!$this->fileExists($filePath)) {
            return null;
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $this->uploadPath . $filePath);
        finfo_close($finfo);
        
        return $mimeType;
    }

    public function cleanupOrphanedFiles($olderThan = '30 days') {
        $cutoffTime = strtotime('-' . $olderThan);
        $deletedCount = 0;
        
        $directories = [
            'profiles',
            'vehicles/images',
            'vehicles/documents'
        ];
        
        foreach ($directories as $directory) {
            $dirPath = $this->uploadPath . $directory;
            if (!is_dir($dirPath)) {
                continue;
            }
            
            $files = scandir($dirPath);
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                
                $filePath = $dirPath . '/' . $file;
                if (filemtime($filePath) < $cutoffTime) {
                    if (unlink($filePath)) {
                        $deletedCount++;
                    }
                }
            }
        }
        
        return $deletedCount;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function clearErrors() {
        $this->errors = [];
    }
}
?>