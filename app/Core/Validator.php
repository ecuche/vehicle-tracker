<?php
namespace App\Core;

class Validator {
    private $errors = [];
    private $data;
    private $rules;

    public function validate($data, $rules) {
        $this->errors = [];
        $this->data = $data;
        $this->rules = $rules;

        foreach ($rules as $field => $fieldRules) {
            $fieldRules = is_array($fieldRules) ? $fieldRules : explode('|', $fieldRules);
            $value = $this->getValue($field);

            foreach ($fieldRules as $rule) {
                $ruleParts = explode(':', $rule);
                $ruleName = $ruleParts[0];
                $ruleParam = $ruleParts[1] ?? null;

                if (!$this->validateRule($field, $value, $ruleName, $ruleParam)) {
                    break; // Stop validating this field after first error
                }
            }
        }

        return empty($this->errors);
    }

    public function getErrors() {
        return $this->errors;
    }

    public function getError($field) {
        return $this->errors[$field] ?? null;
    }

    public function hasErrors() {
        return !empty($this->errors);
    }

    private function getValue($field) {
        return $this->data[$field] ?? null;
    }

    private function validateRule($field, $value, $ruleName, $ruleParam) {
        switch ($ruleName) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    $this->addError($field, "The {$field} field is required.");
                    return false;
                }
                break;

            case 'email':
                if (!empty($value) && !self::validateEmail($value)) {
                    $this->addError($field, "The {$field} must be a valid email address.");
                    return false;
                }
                break;

            case 'min':
                if (!empty($value) && strlen($value) < $ruleParam) {
                    $this->addError($field, "The {$field} must be at least {$ruleParam} characters.");
                    return false;
                }
                break;

            case 'max':
                if (!empty($value) && strlen($value) > $ruleParam) {
                    $this->addError($field, "The {$field} may not be greater than {$ruleParam} characters.");
                    return false;
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, "The {$field} must be a number.");
                    return false;
                }
                break;

            case 'integer':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_INT)) {
                    $this->addError($field, "The {$field} must be an integer.");
                    return false;
                }
                break;

            case 'phone':
                if (!empty($value) && !self::validatePhone($value)) {
                    $this->addError($field, "The {$field} must be a valid Nigerian phone number.");
                    return false;
                }
                break;

            case 'nin':
                if (!empty($value) && !self::validateNIN($value)) {
                    $this->addError($field, "The {$field} must be a valid 11-digit NIN.");
                    return false;
                }
                break;

            case 'vin':
                if (!empty($value) && !self::validateVIN($value)) {
                    $this->addError($field, "The {$field} must be a valid 17-character VIN.");
                    return false;
                }
                break;

            case 'password_strength':
                if (!empty($value) && !self::validatePasswordStrength($value)) {
                    $this->addError($field, "The {$field} must contain at least 8 characters with uppercase, lowercase and number.");
                    return false;
                }
                break;

            case 'matches':
                $matchValue = $this->getValue($ruleParam);
                if ($value !== $matchValue) {
                    $this->addError($field, "The {$field} must match {$ruleParam}.");
                    return false;
                }
                break;

            case 'unique':
                if (!empty($value) && !$this->validateUnique($field, $value, $ruleParam)) {
                    $this->addError($field, "The {$field} has already been taken.");
                    return false;
                }
                break;

            case 'file':
                if (!empty($value) && !$this->validateFile($field, $value, $ruleParam)) {
                    $this->addError($field, "The {$field} must be a valid file.");
                    return false;
                }
                break;

            case 'image':
                if (!empty($value) && !$this->validateImage($field, $value)) {
                    $this->addError($field, "The {$field} must be a valid image.");
                    return false;
                }
                break;

            case 'size':
                if (!empty($value) && $value['size'] > ($ruleParam * 1024 * 1024)) {
                    $this->addError($field, "The {$field} may not be larger than {$ruleParam}MB.");
                    return false;
                }
                break;

            case 'mimes':
                if (!empty($value) && !$this->validateMimes($field, $value, $ruleParam)) {
                    $this->addError($field, "The {$field} must be a file of type: {$ruleParam}.");
                    return false;
                }
                break;
        }

        return true;
    }

    private function addError($field, $message) {
        $this->errors[$field] = $message;
    }

    // Static validation methods for individual field validation
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function validatePhone($phone) {
        // Nigerian phone number format: +2348012345678 or 08012345678
        $pattern = '/^(\+234|0)[789][01]\d{8}$/';
        return preg_match($pattern, $phone) === 1;
    }

    public static function validateNIN($nin) {
        // National Identification Number - 11 digits
        return preg_match('/^\d{11}$/', $nin) === 1;
    }

    public static function validateVIN($vin) {
        // Vehicle Identification Number - 17 alphanumeric characters
        $vin = strtoupper($vin);
        return preg_match('/^[A-HJ-NPR-Z0-9]{17}$/i', $vin) === 1;
    }

    public static function validatePasswordStrength($password) {
        // At least 8 chars, 1 uppercase, 1 lowercase, 1 number
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/', $password) === 1;
    }

    public static function validatePlateNumber($plate) {
        // Nigerian plate number format (basic validation)
        return preg_match('/^[A-Z]{2,3}\s?\d{1,4}\s?[A-Z]{0,2}$/i', $plate) === 1;
    }

    public static function validateYear($year) {
        $currentYear = date('Y');
        return is_numeric($year) && $year >= 1900 && $year <= ($currentYear + 1);
    }

    // File validation methods
    public static function validateFileUpload($file, $allowedTypes = [], $maxSize = 5242880) {
        $errors = [];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = self::getUploadErrorMessage($file['error']);
            return $errors;
        }
        
        if ($file['size'] > $maxSize) {
            $errors[] = "File size exceeds maximum allowed size of " . round($maxSize / 1024 / 1024, 2) . "MB";
        }
        
        if (!empty($allowedTypes)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mimeType, $allowedTypes)) {
                $errors[] = "File type not allowed. Allowed types: " . implode(', ', $allowedTypes);
            }
        }
        
        return $errors;
    }

    private static function getUploadErrorMessage($errorCode) {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
            case UPLOAD_ERR_FORM_SIZE:
                return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
            case UPLOAD_ERR_PARTIAL:
                return 'The uploaded file was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'A PHP extension stopped the file upload';
            default:
                return 'Unknown upload error';
        }
    }

    // Helper methods for real-time validation
    public static function formatPhoneNumber($phone) {
        // Format Nigerian phone numbers to standard format
        $phone = preg_replace('/\D/', '', $phone);
        
        if (strlen($phone) === 11 && $phone[0] === '0') {
            $phone = '234' . substr($phone, 1);
        } elseif (strlen($phone) === 10) {
            $phone = '234' . $phone;
        }
        
        return $phone;
    }

    public static function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }
        
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    public static function validateDate($date, $format = 'Y-m-d') {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public static function validateDateTime($datetime, $format = 'Y-m-d H:i:s') {
        $d = \DateTime::createFromFormat($format, $datetime);
        return $d && $d->format($format) === $datetime;
    }

    // Private helper methods for rule validation
    private function validateUnique($field, $value, $table) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM {$table} WHERE {$field} = ? AND deleted_at IS NULL");
        $stmt->execute([$value]);
        return $stmt->fetch()->count === 0;
    }

    private function validateFile($field, $file, $param) {
        return isset($file['error']) && $file['error'] === UPLOAD_ERR_OK;
    }

    private function validateImage($field, $file) {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        return in_array($mimeType, $allowedTypes);
    }

    private function validateMimes($field, $file, $allowedExtensions) {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $allowedExtensions = explode(',', $allowedExtensions);
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        return in_array($fileExtension, $allowedExtensions);
    }
}
?>