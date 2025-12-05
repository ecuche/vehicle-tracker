<?php
/**
 * Validation Helper Functions
 * Additional validation functions beyond the core Validator class
 */

use App\Core\Validator;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleModel;

/**
 * Validate Nigerian phone number with carrier detection
 */
function validate_nigerian_phone($phone) {
    if (!Validator::validatePhone($phone)) {
        return false;
    }
    
    // Clean the phone number
    $clean = preg_replace('/\D/', '', $phone);
    
    // Convert to standard format (234...)
    if (strlen($clean) === 11 && $clean[0] === '0') {
        $clean = '234' . substr($clean, 1);
    } elseif (strlen($clean) === 10) {
        $clean = '234' . $clean;
    }
    
    // Check carrier prefixes
    $mtn_prefixes = ['234803', '234806', '234810', '234813', '234814', '234816', '234903'];
    $airtel_prefixes = ['234802', '234808', '234812', '234902'];
    $glo_prefixes = ['234805', '234807', '234811', '234815', '234905'];
    $etisalat_prefixes = ['234809', '234817', '234818', '234909'];
    
    $prefix = substr($clean, 0, 6);
    
    return in_array($prefix, array_merge($mtn_prefixes, $airtel_prefixes, $glo_prefixes, $etisalat_prefixes));
}

/**
 * Validate VIN with checksum (basic implementation)
 */
function validate_vin_checksum($vin) {
    if (!Validator::validateVIN($vin)) {
        return false;
    }
    
    // Basic VIN validation - in a real application, you might want to implement
    // the full VIN checksum algorithm or use a third-party service
    
    return true;
}

/**
 * Validate NIN with basic checksum validation
 */
function validate_nin_checksum($nin) {
    if (!Validator::validateNIN($nin)) {
        return false;
    }
    
    // Basic NIN validation - Nigerian NIN has some validation rules
    // This is a simplified version
    
    return true;
}

/**
 * Validate plate number format for Nigerian plates
 */
function validate_plate_number($plate) {
    // Remove spaces and convert to uppercase
    $clean = strtoupper(str_replace(' ', '', $plate));
    
    // Basic Nigerian plate number patterns
    $patterns = [
        '/^[A-Z]{2}\d{4}[A-Z]{0,2}$/',          // AB1234 or AB1234CD
        '/^[A-Z]{3}\d{3,4}[A-Z]{0,2}$/',        // ABC123 or ABC1234DE
        '/^[A-Z]{1,2}\d{1,5}[A-Z]{0,2}$/',      // A12345 or AB12345CD
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $clean)) {
            return true;
        }
    }
    
    return false;
}

/**
 * Validate email uniqueness
 */
function validate_email_unique($email, $excludeUserId = null) {
    $user = new User();
    $existing = $user->findByEmail($email);
    
    if (!$existing) {
        return true;
    }
    
    if ($excludeUserId && $existing->id == $excludeUserId) {
        return true;
    }
    
    return false;
}

/**
 * Validate NIN uniqueness
 */
function validate_nin_unique($nin, $excludeUserId = null) {
    $user = new User();
    $existing = $user->findByNIN($nin);
    
    if (!$existing) {
        return true;
    }
    
    if ($excludeUserId && $existing->id == $excludeUserId) {
        return true;
    }
    
    return false;
}

/**
 * Validate phone uniqueness
 */
function validate_phone_unique($phone, $excludeUserId = null) {
    $user = new User();
    $existing = $user->findByPhone($phone);
    
    if (!$existing) {
        return true;
    }
    
    if ($excludeUserId && $existing->id == $excludeUserId) {
        return true;
    }
    
    return false;
}

/**
 * Validate VIN uniqueness
 */
function validate_vin_unique($vin, $excludeVehicleId = null) {
    $vehicle = new Vehicle();
    $existing = $vehicle->findByVIN($vin);
    
    if (!$existing) {
        return true;
    }
    
    if ($excludeVehicleId && $existing->id == $excludeVehicleId) {
        return true;
    }
    
    return false;
}

/**
 * Validate plate number uniqueness
 */
function validate_plate_unique($plate, $excludeVehicleId = null) {
    $plateModel = new \App\Models\PlateNumber();
    return $plateModel->isPlateNumberUnique($plate, $excludeVehicleId);
}

/**
 * Validate vehicle model exists
 */
function validate_vehicle_model($modelId) {
    $vehicleModel = new VehicleModel();
    return $vehicleModel->findById($modelId) !== false;
}

/**
 * Validate year is reasonable for a vehicle
 */
function validate_vehicle_year($year) {
    $currentYear = date('Y');
    return is_numeric($year) && $year >= 1900 && $year <= ($currentYear + 1);
}

/**
 * Validate file upload with specific requirements
 */
function validate_file_upload($file, $options = []) {
    $defaultOptions = [
        'max_size' => 5 * 1024 * 1024, // 5MB
        'allowed_types' => ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'],
        'required' => false,
        'image_only' => false
    ];
    
    $options = array_merge($defaultOptions, $options);
    
    // Check if file is required but not provided
    if ($options['required'] && (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE)) {
        return [false, 'File is required'];
    }
    
    // Check if no file was uploaded (and it's not required)
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return [true, '']; // No file is acceptable if not required
    }
    
    // Validate file upload
    $errors = Validator::validateFileUpload($file, $options['allowed_types'], $options['max_size']);
    
    if (!empty($errors)) {
        return [false, implode(', ', $errors)];
    }
    
    // Additional image validation if required
    if ($options['image_only']) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $imageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($mimeType, $imageTypes)) {
            return [false, 'File must be an image'];
        }
    }
    
    return [true, ''];
}

/**
 * Validate multiple file uploads
 */
function validate_multiple_files($files, $options = []) {
    $results = [];
    $hasErrors = false;
    
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
                
                list($valid, $error) = validate_file_upload($file, $options);
                $results[] = ['valid' => $valid, 'error' => $error];
                
                if (!$valid) {
                    $hasErrors = true;
                }
            }
        }
    } else {
        if ($files['error'] === UPLOAD_ERR_OK) {
            list($valid, $error) = validate_file_upload($files, $options);
            $results[] = ['valid' => $valid, 'error' => $error];
            
            if (!$valid) {
                $hasErrors = true;
            }
        }
    }
    
    return [!$hasErrors, $results];
}

/**
 * Real-time validation for forms (for AJAX)
 */
function realtime_validate($field, $value, $rules) {
    $validator = new Validator();
    $data = [$field => $value];
    $validationRules = [$field => $rules];
    
    $isValid = $validator->validate($data, $validationRules);
    $error = $validator->getError($field);
    
    return [
        'valid' => $isValid,
        'error' => $error,
        'message' => $error ?: 'Valid'
    ];
}

/**
 * Validate password confirmation
 */
function validate_password_confirmation($password, $confirmation) {
    return $password === $confirmation;
}

/**
 * Validate strong password
 */
function validate_strong_password($password) {
    return Validator::validatePasswordStrength($password);
}

/**
 * Validate URL format
 */
function validate_url($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Validate IP address
 */
function validate_ip($ip) {
    return filter_var($ip, FILTER_VALIDATE_IP) !== false;
}

/**
 * Validate domain name
 */
function validate_domain($domain) {
    return preg_match('/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/i', $domain) === 1;
}

/**
 * Validate Nigerian state
 */
function validate_nigerian_state($state) {
    $nigerianStates = [
        'Abia', 'Adamawa', 'Akwa Ibom', 'Anambra', 'Bauchi', 'Bayelsa', 'Benue', 'Borno',
        'Cross River', 'Delta', 'Ebonyi', 'Edo', 'Ekiti', 'Enugu', 'Gombe', 'Imo',
        'Jigawa', 'Kaduna', 'Kano', 'Katsina', 'Kebbi', 'Kogi', 'Kwara', 'Lagos',
        'Nasarawa', 'Niger', 'Ogun', 'Ondo', 'Osun', 'Oyo', 'Plateau', 'Rivers',
        'Sokoto', 'Taraba', 'Yobe', 'Zamfara', 'FCT'
    ];
    
    return in_array(ucwords(strtolower($state)), $nigerianStates);
}

/**
 * Validate transfer ownership (user cannot transfer to themselves)
 */
function validate_transfer_ownership($fromUserId, $toUserId) {
    return $fromUserId != $toUserId;
}

/**
 * Validate user can transfer vehicle (owns the vehicle)
 */
function validate_vehicle_ownership($userId, $vehicleId) {
    $vehicle = new Vehicle();
    $vehicleData = $vehicle->findVehicleById($vehicleId);
    
    return $vehicleData && $vehicleData->user_id == $userId;
}

/**
 * Get validation rules for common forms
 */
function get_validation_rules($formType) {
    $rules = [
        'registration' => [
            'email' => 'required|email|unique:users',
            'phone' => 'required|phone|unique:users',
            'nin' => 'required|nin|unique:users',
            'password' => 'required|password_strength|min:8',
            'password_confirm' => 'required|matches:password',
            'role' => 'required|in:driver,searcher'
        ],
        
        'login' => [
            'email' => 'required|email',
            'password' => 'required'
        ],
        
        'profile_update' => [
            'profile_picture' => 'file|image|size:2'
        ],
        
        'password_change' => [
            'current_password' => 'required',
            'new_password' => 'required|password_strength|min:8',
            'confirm_password' => 'required|matches:new_password'
        ],
        
        'vehicle_registration' => [
            'vin' => 'required|vin|unique:vehicles',
            'plate_number' => 'required|plate_number|unique:plate_numbers',
            'vehicle_model_id' => 'required|integer|vehicle_model',
            'year' => 'required|integer|vehicle_year',
            'vehicle_images' => 'file|image|size:5',
            'vehicle_documents' => 'file|size:10'
        ],
        
        'vehicle_transfer' => [
            'vehicle_id' => 'required|integer',
            'user_identifier' => 'required'
        ],
        
        'plate_assignment' => [
            'vehicle_id' => 'required|integer',
            'plate_number' => 'required|plate_number|unique:plate_numbers'
        ],
        
        'admin_user_update' => [
            'role' => 'required|in:driver,searcher,admin'
        ],
        
        'admin_vehicle_status' => [
            'vehicle_id' => 'required|integer',
            'status' => 'required|in:none,stolen,no_customs_duty,changed_engine,changed_color'
        ]
    ];
    
    return $rules[$formType] ?? [];
}
?>