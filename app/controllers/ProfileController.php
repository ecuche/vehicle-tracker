<?php
namespace App\Controllers;
use App\Core\Controller;

class ProfileController extends Controller {
   
    public function __construct() {
        parent::__construct();
        if (!$this->auth->isLoggedIn()) {
            header("Location: {$_ENV['APP_URL']}/login");
            exit;
        }
    }

    public function index() {
        $user_id = $this->auth->getUserId();
        $user = $this->user->findById($user_id);
        
        if (!$user) {
            $this->session->setFlash('error', 'User not found');
            header("Location: {$_ENV['APP_URL']}/dashboard");
            exit;
        }
        
        $data = [
            'user' => $user
        ];
        extract($data);
        require_once 'app/Views/profile/index.php';
    }

    public function update() {
        $user_id = $this->auth->getUserId();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];
            
            // Handle profile picture upload
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                $upload_result = $this->upload->handleProfilePicture($_FILES['profile_picture']);
                
                if ($upload_result['success']) {
                    // Update user profile picture
                    if ($this->user->updateProfilePicture($user_id, $upload_result['file_path'])) {
                        $this->session->setFlash('success', 'Profile picture updated successfully');
                    } else {
                        $errors[] = 'Failed to update profile picture';
                    }
                } else {
                    $errors[] = $upload_result['error'];
                }
            }
            
            if (!empty($errors)) {
                $this->session->setFlash('errors', $errors);
            }
        }
        
        header("Location: {$_ENV['APP_URL']}/profile");
        exit;
    }

    public function removeProfilePicture() {
        $user_id = $this->auth->getUserId();
        $user = $this->user->findById($user_id);
        
        if ($user && $user['profile_picture']) {
            // Delete physical file
            if (file_exists($_ENV['UPLOAD_PATH'].$user['profile_picture'])) {
                unlink($_ENV['UPLOAD_PATH'].$user['profile_picture']);
            }
            
            // Update database
            if ($this->user->updateProfilePicture($user_id, null)) {
                $this->session->setFlash('success', 'Profile picture removed successfully');
            } else {
                $this->session->setFlash('error', 'Failed to remove profile picture');
            }
        }
        $this->redirect('profile');
    }

    public function changePassword() {
        $user_id = $this->auth->getUserId();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            $this->validatePasswordChange($user_id, $current_password, $new_password, $confirm_password);
            
            if (!$this->session->checkError()) {
                if ($this->user->updatePassword($user_id, $new_password)) {
                    $this->session->setFlash('success', 'Password changed successfully');
                } else {
                    $errors[] = 'Failed to change password';
                }
            }
        }
        $this->redirect('profile');
        exit;
    }

    private function validatePasswordChange($user_id, $current_password, $new_password, $confirm_password) {
        
        $user = $this->user->findById($user_id);
        $this->session->clearErrors();
        
        if (!password_verify($current_password, $user['password'])) {
            $this->session->setError('current_password', 'Current password is incorrect'); 
        }
        
        if (!$this->validator::validatePasswordStrength($new_password)) {
            $this->session->setError('new_password', 'New password must be at least 8 characters with uppercase, lowercase and number');
        }
        
        if ($new_password !== $confirm_password) {
            $this->session->setError('confirm_password', 'New passwords do not match');
        }
    }

    public function getUserProfile($identifier) {
        if (!$this->auth->isLoggedIn()) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $user_role = $this->auth->getUserRole();
        
        // Only searcher and admin can view user profiles
        if (!in_array($user_role, ['searcher', 'admin'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Access denied']);
            exit;
        }

        $user = $this->user->findByIdentifier($identifier);
        
        if ($user) {
            // Get user's vehicles and contact details
            $vehicles = $this->user->getUserVehiclesWithHistory($user['id']);
            $plate_numbers = $this->user->getUserPlateNumbers($user['id']);
            
            $profile_data = [
                'user' => [
                    'email' => $user['email'],
                    'phone' => $user['phone'],
                    'nin' => $user['nin'],
                    'role' => $user['role'],
                    'profile_picture' => $user['profile_picture']
                ],
                'vehicles' => $vehicles,
                'plate_numbers' => $plate_numbers
            ];
            
            header('Content-Type: application/json');
            echo json_encode($profile_data);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'User not found']);
        }
        exit;
    }
}
?>