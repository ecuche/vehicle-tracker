<?php
namespace App\Controllers;
use App\Core\Controller;



class AuthController extends Controller{
    public function __construct() {
       parent::__construct();
    }

    public function showRegister(){
         $this->response->authRedirect();
        $this->view('auth/register');
    }

    public function register() {
        // Check if user is already logged in
        $this->response->authRedirect();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => ucwords(trim($this->request->post('name') ?? '')),
                'email' => trim($this->request->post('email') ?? ''),
                'phone' => preg_replace('/\D/', '', trim($this->request->post('phone'))),
                'nin' => trim($this->request->post('nin') ?? ''),
                'password' => $this->request->post('password') ?? '',
                'password_confirm' => $this->request->post('password_confirm') ?? '',
                'role' => in_array($this->request->post('role') ?? '', ['driver', 'searcher']) ? $this->request->post('role') : 'searcher',
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
                    $this->redirect('login');
                    exit;
                } else {
                    $errors[] = 'Registration failed. Please try again.';
                }
            }
            foreach($data as $key => $value){
                $this->session->setFormData($key, $value);
            }
        }
        $this->view('auth/register');
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

    public function showLogin(){
        $this->response->authRedirect();
        $this->view('auth/login');
    }

    public function login() {
        $this->response->authRedirect();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($this->request->post('email') ?? '');
            $password = $this->request->post('password') ?? '';
           
            // Rate limiting check
            if ($this->auth->isRateLimited($email)) {
                $this->session->setFlash('error', 'Too many login attempts. Please try again in 15 minutes.');
                $this->redirect("login");
                exit;
            }            
            $user = $this->user->findByEmail($email);            
            if ($user && password_verify($password, $user['password'])) {
                if ($user['email_verified']) {
                    if ($user['is_banned']) {
                        $this->session->setFlash('error', 'Your account has been banned. Please contact administrator.');
                        $this->redirect("login");
                        exit;
                    }

                    $this->auth->login($user);
                    
                    // Reset rate limiting
                    $this->auth->resetRateLimit($email);
                    
                    $this->redirect('dashboard');
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
        $this->view('auth/login');
    }

    public function logout() {
        $this->auth->logout();
        $this->redirect('login');
        exit;
    }

    public function verifyEmail($token) {
        if ($this->user->verifyEmail($token)) {
            $this->session->setFlash('success', 'Email verified successfully. You can now login.');
        } else {
            $this->session->setFlash('error', 'Invalid or expired verification token.');
        }
        $this->redirect('login');
    }


    public function showForgotPassword () {
        $this->response->authRedirect();
        $this->view("auth/forgot-password");
    }


    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $this->request->post('email');
            $user = $this->user->findByEmail($email);
            
            if ($user) {
                do {
                    $reset_token = bin2hex(random_bytes(32));
                } while ($this->user->countRow(['token'=> $reset_token], 'password_resets'));
                
                if ($this->user->createPasswordReset($user['id'], $reset_token)) {
                    sendPasswordResetEmail($email, $reset_token);
                }
            }
            // Always show success message for security
            $this->session->setFlash('success', 'If the email exists, a password reset link has been sent.');
            $this->redirect("login");   
        }
        $this->response->redirect("forgot-password");
    }

    public function showResetPassword ($token) {
        $this->response->authRedirect();
        $reset_request = $this->user->findPasswordReset($token);
        if (!$reset_request || strtotime($reset_request['expires_at']) < time()) {
            $this->session->setFlash('error', 'Invalid or expired reset token.');
            $this->redirect("login");
        }
        $data = [
            "token"=> $token
        ];
        $this->view("auth/reset-password", $data);
    }


    public function resetPassword($token) {
        $reset_request = $this->user->findPasswordReset($token);
       
        if (!$reset_request || strtotime($reset_request['expires_at']) < time()) {
            $this->session->setFlash('error', 'Invalid or expired reset token.');
            $this->redirect("login");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $this->request->post('password') ?? '';
            $password_confirm = $this->request->post('password_confirm') ?? '';
            
            $errors = [];
            
            if (!$this->validator::validatePasswordStrength($password)) {
                $errors[] = 'Password must be at least 8 characters with uppercase, lowercase, number and special character';
            }
            
            if ($password !== $password_confirm) {
                $errors[] = 'Passwords do not match';
            }
            
            if (empty($errors)) {
                if ($this->user->updatePassword($reset_request['user_id'], $password)) {
                    $this->user->deletePasswordReset($token);
                    $this->session->setFlash('success', 'Password reset successfully. You can now login.');
                    $this->redirect("login");
                    exit;
                } else {
                    $errors[] = 'Password reset failed. Please try again.';
                }
            }
            
            $this->session->setFlash('errors', $errors);
        }
        $this->response->redirect('auth/reset_password', ['token'=> $token]);
    }
}
?>