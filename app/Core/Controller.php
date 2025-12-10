<?php

namespace App\Core;

// Import necessary model
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Transfer;
use App\Models\Audit;
use App\Models\VehicleModel;
use App\Models\PlateNumber;
use App\Models\VehicleStatusHistory;
use App\Models\searchHistory;

// Import core classes
use App\Core\Session;
use App\Core\Auth;
use App\Core\CSRF;
use App\core\Upload;
use App\Core\Validator;
use App\Core\Request;


abstract class Controller{

    //models
  
    protected $user;
    protected $vehicle;
    protected $transfer;
    protected $audit;
    protected $vehicleModel;
    protected $plateNumber;
    protected $searchHistory;
    protected $vehicleStatusHistory;

    //other core classes
    
    protected $upload;
    protected $session;
    protected $auth;
    protected $validator;
    protected $csrf;
    protected $request;

    public function __construct() {
        //models
        $this->user = new User();
        $this->vehicle = new Vehicle();
        $this->audit = new Audit();
        $this->transfer = new Transfer();
        $this->vehicleModel = new VehicleModel();
        $this->plateNumber = new PlateNumber();
        $this->vehicleStatusHistory = new VehicleStatusHistory();
        $this->searchHistory = new SearchHistory(); 

        //other core classes
        $this->auth = new Auth();
        $this->upload = new Upload();
        $this->validator = new Validator();
        $this->session = new Session();
        $this->csrf = new CSRF();
        $this->request = new Request();
    }

    protected function view(string $template, array $data = []): void
    {
        extract($data);
        require_once __DIR__ . "/../views/{$template}.php";
    }

    protected function redirect(string $url, array $data = []): void  
    {
        $params = empty($data) ? '' : '/?' .http_build_query($data);
        header("Location: {$_ENV['APP_URL']}/{$url}".$params);
        exit;
    }

    protected function setFormError($error): void
    {
        $this->session->setError('formError', $error);
    }

    protected function getFormError()
    {
        // return $this->session->flashError('formError');
    }

    protected function isPostRequest(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }


    protected function isGetRequest(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    protected function validateCsrfToken($token): bool
    {
        return CSRF::validate($token);
    }

    protected function generateCsrfToken(): string
    {
        return CSRF::generate();
    }

    protected function setFormData($data): void
    {
        foreach ($data as $key => $value) {
            $this->session->setFormData($key, $value);
        }
    }

    protected function getFormData()
    {
        // return $this->session->flashFormData();
    }

    protected function clearFormData(): void
    {
        $this->session->clearFormData();
    }

    protected function flashFormData()
    {
        // return $this->session->flashFormData();
    }

    protected function keepFormData(): void
    {
        $this->session->keepFormData();
    }


    
}