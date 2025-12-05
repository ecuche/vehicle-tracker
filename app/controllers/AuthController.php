<?php
namespace App\Controllers;
use App\Core\Controller;



class AuthController extends Controller{
    public function __construct() {
       parent::__construct();
    }

    public function register() {
        // Check if user is already logged in
        if ($this->auth->isLoggedIn()) {
            header("Location: {$_ENV['APP_URL']}/dashboard");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'phone' => preg_replace('/\D/', '', trim($_POST['phone'])),
                'nin' => trim($_POST['nin'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'password_confirm' => $_POST['password_confirm'] ?? '',
                'role' => in_array($_POST['role'] ?? '', ['driver', 'searcher']) ? $_POST['role'] : 'searcher',
                'verification_token' => bin2hex(random_bytes(32))
            ];
            $this->session->clearErrors();
            $this->session->clearFormData();

            // Validation
            $this->validateRegistration($data);
            $errors = $this->session->get('errors') ?? [];
            
            if (empty($errors)) {
                if ($this->user->create($data)) {
                    // Send verification email
                    sendVerificationEmail($data['email'], $data['verification_token']);
                    $this->session->clearErrors();
                    $this->session->clearFormData();
                    $this->session->setFlash('success', 'Registration successful. Please check your email for verification.');
                    header("Location: {$_ENV['APP_URL']}/login");
                    exit;
                } else {
                    $errors[] = 'Registration failed. Please try again.';
                }
            }
            foreach($data as $key => $value){
                $this->session->setFormData($key, $value);
            }
        }
        require_once 'app/Views/auth/register.php';
    }

    private function validateRegistration($data) {

        if(empty($data['name']) || strlen($data['name']) < 3) {
            $this->session->setError('name', 'Name must be at least 3 characters long');
        }

        if (!$this->validator::validateEmail($data['email'])) {
            $this->session->setError('email', 'Invalid email format');
        }

        if (!$this->validator::validatePhone($data['phone'])) {
            $this->session->setError('phone', 'Invalid Nigerian phone number format');
        }

        if (!$this->validator::validateNIN($data['nin'])) {
            $this->session->setError('nin', 'NIN must be 11 digits');
        }

        if (!$this->validator::validatePasswordStrength($data['password'])) {
            $this->session->setError('password', 'Password must be at least 8 characters with uppercase, lowercase, number');
        }

        if ($data['password'] !== $data['password_confirm']) {
            $this->session->setError('password_confirm', 'Passwords do not match');
        }

        if ($this->user->findByEmail($data['email'])) {
            $this->session->setError('email', 'Email already registered');
        }

        if ($this->user->findByNIN($data['nin'])) {
            $this->session->setError('nin', 'NIN already registered');
        }

        if ($this->user->findByPhone($data['phone'])) {
            $this->session->setError('phone', 'Phone number already registered');
        }
    }

    public function login() {
        if ($this->auth->isLoggedIn()) {
            header("Location: {$_ENV['APP_URL']}/dashboard");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

           
            // Rate limiting check
            if ($this->auth->isRateLimited($email)) {
                $this->session->setFlash('error', 'Too many login attempts. Please try again in 15 minutes.');
                header("Location: {$_ENV['APP_URL']}/login");
                exit;
            }
            
            $user = $this->user->findByEmail($email);
            $user = (object)$user;
            
            if ($user && password_verify($password, $user->password)) {
                if ($user->email_verified) {
                    if ($user->is_banned) {
                        $this->session->setFlash('error', 'Your account has been banned. Please contact administrator.');
                        header("Location: {$_ENV['APP_URL']}/login");
                        exit;
                    }

                    $this->auth->login($user);
                    
                    // Reset rate limiting
                    $this->auth->resetRateLimit($email);
                    
                    header("Location: {$_ENV['APP_URL']}/dashboard");
                    exit;
                } else {
            
                    $this->auth->recordFailedAttempt($email);
                    $this->session->setFlash('error', 'Please verify your email before logging in.');
                }
            } else {                
                $this->auth->recordFailedAttempt($email);
                $this->session->setFlash('error', 'Invalid credentials');
            }
        }        
        require_once 'app/Views/auth/login.php';
    }

    public function logout() {
        $this->auth->logout();
        header("Location: {$_ENV['APP_URL']}/login");
        exit;
    }

    public function verifyEmail($token) {
        if ($this->user->verifyEmail($token)) {
            $this->session->setFlash('success', 'Email verified successfully. You can now login.');
        } else {
            $this->session->setFlash('error', 'Invalid or expired verification token.');
        }
        
        header("Location: {$_ENV['APP_URL']}/login");
        exit;
    }

    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $user = $this->user->findByEmail($email);
            
            if ($user) {
                $reset_token = bin2hex(random_bytes(32));
                $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                if ($this->user->createPasswordReset($user->id, $reset_token, $expires_at)) {
                    sendPasswordResetEmail($email, $reset_token);
                }
            }
            
            // Always show success message for security
            $this->session->setFlash('success', 'If the email exists, a password reset link has been sent.');
            header("Location: {$_ENV['APP_URL']}/login");
            exit;
        }
        
        require_once 'app/Views/auth/forgot_password.php';
    }

    public function resetPassword($token) {
        $reset_request = $this->user->findPasswordReset($token);
        
        if (!$reset_request || strtotime($reset_request->expires_at) < time()) {
            $this->session->setFlash('error', 'Invalid or expired reset token.');
            header("Location: {$_ENV['APP_URL']}/login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';
            
            $errors = [];
            
            if (!$this->validator::validatePasswordStrength($password)) {
                $errors[] = 'Password must be at least 8 characters with uppercase, lowercase, number and special character';
            }
            
            if ($password !== $password_confirm) {
                $errors[] = 'Passwords do not match';
            }
            
            if (empty($errors)) {
                if ($this->user->updatePassword($reset_request->user_id, $password)) {
                    $this->user->deletePasswordReset($token);
                    $this->session->setFlash('success', 'Password reset successfully. You can now login.');
                    header("Location: {$_ENV['APP_URL']}/login");
                    exit;
                } else {
                    $errors[] = 'Password reset failed. Please try again.';
                }
            }
            
            $this->session->setFlash('errors', $errors);
        }
        require_once 'app/Views/auth/reset_password.php';
    }
}
?>