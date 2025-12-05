<?php
/**
 * Email Helper Functions
 * Handles all email sending functionality using PHPMailer
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Send email verification message
 */
function sendVerificationEmail($email, $verificationToken) {
    $subject = "Verify Your Email - Vehicle Tracker";
    
    $verificationUrl = base_url("verify-email/{$verificationToken}");
    
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #007bff; color: white; padding: 20px; text-align: center; }
            .content { background: #f9f9f9; padding: 20px; }
            .button { display: inline-block; padding: 12px 24px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; }
            .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Vehicle Tracker</h1>
            </div>
            <div class='content'>
                <h2>Verify Your Email Address</h2>
                <p>Thank you for registering with Vehicle Tracker. Please verify your email address by clicking the button below:</p>
                <p style='text-align: center;'>
                    <a href='{$verificationUrl}' class='button'>Verify Email Address</a>
                </p>
                <p>If the button doesn't work, you can copy and paste the following link into your browser:</p>
                <p><a href='{$verificationUrl}'>{$verificationUrl}</a></p>
                <p>This verification link will expire in 24 hours.</p>
                <p>If you didn't create an account with Vehicle Tracker, please ignore this email.</p>
            </div>
            <div class='footer'>
                <p>&copy; " . date('Y') . " Vehicle Tracker. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($email, $subject, $message);
}

/**
 * Send password reset email
 */
function sendPasswordResetEmail($email, $resetToken) {
    $subject = "Reset Your Password - Vehicle Tracker";
    
    $resetUrl = base_url("reset-password/{$resetToken}");
    
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #dc3545; color: white; padding: 20px; text-align: center; }
            .content { background: #f9f9f9; padding: 20px; }
            .button { display: inline-block; padding: 12px 24px; background: #dc3545; color: white; text-decoration: none; border-radius: 4px; }
            .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Vehicle Tracker</h1>
            </div>
            <div class='content'>
                <h2>Reset Your Password</h2>
                <p>We received a request to reset your password for your Vehicle Tracker account. Click the button below to reset your password:</p>
                <p style='text-align: center;'>
                    <a href='{$resetUrl}' class='button'>Reset Password</a>
                </p>
                <p>If the button doesn't work, you can copy and paste the following link into your browser:</p>
                <p><a href='{$resetUrl}'>{$resetUrl}</a></p>
                <p>This password reset link will expire in 1 hour.</p>
                <p>If you didn't request a password reset, please ignore this email. Your password will remain unchanged.</p>
            </div>
            <div class='footer'>
                <p>&copy; " . date('Y') . " Vehicle Tracker. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($email, $subject, $message);
}

/**
 * Send vehicle transfer notification
 */
function sendTransferNotification($recipientEmail, $vehicle, $fromUser) {
    $subject = "Vehicle Transfer Request - Vehicle Tracker";
    
    $vehicleDetails = "
        <ul>
            <li><strong>VIN:</strong> {$vehicle->vin}</li>
            <li><strong>Plate Number:</strong> {$vehicle->current_plate_number}</li>
            <li><strong>Make:</strong> {$vehicle->make}</li>
            <li><strong>Model:</strong> {$vehicle->model}</li>
            <li><strong>Year:</strong> {$vehicle->year}</li>
        </ul>
    ";
    
    $dashboardUrl = base_url("dashboard");
    
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #28a745; color: white; padding: 20px; text-align: center; }
            .content { background: #f9f9f9; padding: 20px; }
            .vehicle-details { background: white; padding: 15px; border-left: 4px solid #28a745; margin: 15px 0; }
            .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Vehicle Tracker</h1>
            </div>
            <div class='content'>
                <h2>Vehicle Transfer Request</h2>
                <p>You have received a vehicle transfer request from <strong>{$fromUser->email}</strong>.</p>
                
                <div class='vehicle-details'>
                    <h3>Vehicle Details:</h3>
                    {$vehicleDetails}
                </div>
                
                <p>Please log in to your Vehicle Tracker account to accept or reject this transfer request.</p>
                
                <p><a href='{$dashboardUrl}'>Go to Dashboard</a></p>
                
                <p><strong>Note:</strong> You must accept the transfer within 7 days, after which the request will expire.</p>
            </div>
            <div class='footer'>
                <p>&copy; " . date('Y') . " Vehicle Tracker. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($recipientEmail, $subject, $message);
}

/**
 * Send transfer accepted notification
 */
function sendTransferAcceptedNotification($previousOwnerEmail, $vehicleId) {
    $subject = "Vehicle Transfer Accepted - Vehicle Tracker";
    
    $vehicleUrl = base_url("vehicles");
    
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #17a2b8; color: white; padding: 20px; text-align: center; }
            .content { background: #f9f9f9; padding: 20px; }
            .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Vehicle Tracker</h1>
            </div>
            <div class='content'>
                <h2>Vehicle Transfer Completed</h2>
                <p>Your vehicle transfer request has been accepted by the recipient.</p>
                <p>The vehicle has been successfully transferred and is no longer listed in your account.</p>
                <p>You can view your remaining vehicles in your dashboard.</p>
                <p><a href='{$vehicleUrl}'>View Your Vehicles</a></p>
            </div>
            <div class='footer'>
                <p>&copy; " . date('Y') . " Vehicle Tracker. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($previousOwnerEmail, $subject, $message);
}

/**
 * Send vehicle status change notification
 */
function sendVehicleStatusChangeNotification($ownerEmail, $vehicle, $newStatus) {
    $subject = "Vehicle Status Updated - Vehicle Tracker";
    
    $statusLabels = [
        'none' => 'Normal',
        'stolen' => 'Stolen',
        'no_customs_duty' => 'No Customs Duty',
        'changed_engine' => 'Changed Engine',
        'changed_color' => 'Changed Color'
    ];
    
    $newStatusLabel = $statusLabels[$newStatus] ?? ucfirst($newStatus);
    
    $vehicleUrl = base_url("vehicles/details/{$vehicle->id}");
    
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #ffc107; color: #333; padding: 20px; text-align: center; }
            .content { background: #f9f9f9; padding: 20px; }
            .status-update { background: white; padding: 15px; border-left: 4px solid #ffc107; margin: 15px 0; }
            .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Vehicle Tracker</h1>
            </div>
            <div class='content'>
                <h2>Vehicle Status Update</h2>
                <p>The status of your vehicle has been updated by the system administrator.</p>
                
                <div class='status-update'>
                    <h3>Vehicle: {$vehicle->make} {$vehicle->model} ({$vehicle->year})</h3>
                    <p><strong>VIN:</strong> {$vehicle->vin}</p>
                    <p><strong>Plate Number:</strong> {$vehicle->current_plate_number}</p>
                    <p><strong>New Status:</strong> <strong>{$newStatusLabel}</strong></p>
                </div>
                
                <p>You can view detailed information about your vehicle by clicking the link below:</p>
                <p><a href='{$vehicleUrl}'>View Vehicle Details</a></p>
                
                <p>If you believe this status change is incorrect, please contact the system administrator.</p>
            </div>
            <div class='footer'>
                <p>&copy; " . date('Y') . " Vehicle Tracker. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($ownerEmail, $subject, $message);
}

/**
 * Send admin alert email
 */
function sendAdminAlert($subject, $message, $priority = 'normal') {
    $adminEmail = $_ENV['ADMIN_EMAIL'] ?? $_ENV['MAIL_USERNAME'] ?? 'admin@vehicletracker.com';
    
    $priorityColors = [
        'low' => '#17a2b8',
        'normal' => '#007bff',
        'high' => '#dc3545',
        'critical' => '#dc3545'
    ];
    
    $color = $priorityColors[$priority] ?? '#007bff';
    
    $formattedMessage = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: {$color}; color: white; padding: 20px; text-align: center; }
            .content { background: #f9f9f9; padding: 20px; }
            .alert { background: white; padding: 15px; border-left: 4px solid {$color}; margin: 15px 0; }
            .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Vehicle Tracker - Admin Alert</h1>
                <p>Priority: " . ucfirst($priority) . "</p>
            </div>
            <div class='content'>
                <div class='alert'>
                    {$message}
                </div>
                <p>This is an automated alert from the Vehicle Tracker system.</p>
            </div>
            <div class='footer'>
                <p>&copy; " . date('Y') . " Vehicle Tracker. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($adminEmail, "ADMIN ALERT: {$subject}", $formattedMessage);
}

/**
 * Core email sending function using PHPMailer
 */
function sendEmail($to, $subject, $message, $attachments = []) {
    try {
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['MAIL_USERNAME'] ?? '';
        $mail->Password = $_ENV['MAIL_PASSWORD'] ?? '';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $_ENV['MAIL_PORT'] ?? 587;
        
        // Recipients
        $mail->setFrom($_ENV['MAIL_FROM'] ?? 'no-reply@vehicletracker.com', 'Vehicle Tracker');
        $mail->addAddress($to);
        
        // Reply-to address
        $mail->addReplyTo($_ENV['MAIL_REPLY_TO'] ?? $_ENV['MAIL_FROM'] ?? 'support@vehicletracker.com', 'Vehicle Tracker Support');
        
        // Attachments
        foreach ($attachments as $attachment) {
            if (file_exists($attachment['path'])) {
                $mail->addAttachment($attachment['path'], $attachment['name'] ?? basename($attachment['path']));
            }
        }
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = strip_tags($message);
        
        // Send email
        $mail->send();
        
        // Log successful email sending
        error_log("Email sent successfully to: {$to}, Subject: {$subject}");
        
        return true;
    } catch (Exception $e) {
        // Log email sending error
        error_log("Email sending failed to: {$to}, Error: {$mail->ErrorInfo}");
        
        // Fallback to basic mail() function if PHPMailer fails
        return sendEmailFallback($to, $subject, $message);
    }
}

/**
 * Fallback email function using PHP's mail()
 */
function sendEmailFallback($to, $subject, $message) {
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=utf-8',
        'From: ' . ($_ENV['MAIL_FROM'] ?? 'no-reply@vehicletracker.com'),
        'Reply-To: ' . ($_ENV['MAIL_REPLY_TO'] ?? 'support@vehicletracker.com'),
        'X-Mailer: PHP/' . phpversion()
    ];
    
    $result = mail($to, $subject, $message, implode("\r\n", $headers));
    
    if ($result) {
        error_log("Fallback email sent to: {$to}");
    } else {
        error_log("Fallback email failed to: {$to}");
    }
    
    return $result;
}

/**
 * Send bulk emails with rate limiting
 */
function sendBulkEmails($emails, $subject, $messageTemplate, $placeholders = [], $delay = 1) {
    $results = [];
    $sentCount = 0;
    
    foreach ($emails as $email) {
        // Replace placeholders in message
        $message = $messageTemplate;
        foreach ($placeholders as $key => $value) {
            $message = str_replace("{{{$key}}}", $value, $message);
        }
        
        // Send email
        $result = sendEmail($email, $subject, $message);
        $results[] = [
            'email' => $email,
            'success' => $result
        ];
        
        $sentCount++;
        
        // Rate limiting delay
        if ($delay > 0 && $sentCount < count($emails)) {
            sleep($delay);
        }
    }
    
    return $results;
}

/**
 * Test email configuration
 */
function testEmailConfiguration() {
    $testEmail = $_ENV['MAIL_USERNAME'] ?? 'test@vehicletracker.com';
    $subject = "Vehicle Tracker - Email Configuration Test";
    $message = "
    <html>
    <body>
        <h2>Email Configuration Test</h2>
        <p>This is a test email sent from the Vehicle Tracker system.</p>
        <p>If you're receiving this email, your email configuration is working correctly.</p>
        <p>Timestamp: " . date('Y-m-d H:i:s') . "</p>
    </body>
    </html>
    ";
    
    return sendEmail($testEmail, $subject, $message);
}

/**
 * Get email template
 */
function getEmailTemplate($templateName, $variables = []) {
    $templates = [
        'welcome' => [
            'subject' => 'Welcome to Vehicle Tracker!',
            'message' => '
            <h2>Welcome to Vehicle Tracker!</h2>
            <p>Thank you for joining Vehicle Tracker. Your account has been successfully created.</p>
            <p>You can now register your vehicles, transfer ownership, and search for vehicle information.</p>
            <p><a href="{{login_url}}">Login to Your Account</a></p>
            '
        ],
        
        'account_verified' => [
            'subject' => 'Account Verified - Vehicle Tracker',
            'message' => '
            <h2>Account Verified Successfully</h2>
            <p>Your Vehicle Tracker account has been verified and is now fully active.</p>
            <p>You can access all features of the platform.</p>
            <p><a href="{{dashboard_url}}">Go to Dashboard</a></p>
            '
        ],
        
        'password_changed' => [
            'subject' => 'Password Changed - Vehicle Tracker',
            'message' => '
            <h2>Password Changed Successfully</h2>
            <p>Your Vehicle Tracker password has been changed successfully.</p>
            <p>If you did not make this change, please contact support immediately.</p>
            <p><a href="{{support_url}}">Contact Support</a></p>
            '
        ]
    ];
    
    $template = $templates[$templateName] ?? null;
    
    if (!$template) {
        return null;
    }
    
    // Replace variables in template
    foreach ($variables as $key => $value) {
        $template['subject'] = str_replace("{{{$key}}}", $value, $template['subject']);
        $template['message'] = str_replace("{{{$key}}}", $value, $template['message']);
    }
    
    return $template;
}
?>