<?php
/**
 * Submit Quote API
 * Handles form submission from contact page
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to users

// Include database config
require_once 'config.php';

// Set headers for CORS and JSON
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSONResponse(false, 'Invalid request method. Only POST is allowed.');
}

try {
    // Get POST data
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    // If JSON decode fails, try to get data from POST
    if (!$data) {
        $data = $_POST;
    }
    
    // Validate required fields
    $requiredFields = ['firstName', 'lastName', 'email', 'service', 'message'];
    $missingFields = [];
    
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            $missingFields[] = $field;
        }
    }
    
    if (!empty($missingFields)) {
        sendJSONResponse(false, 'Missing required fields: ' . implode(', ', $missingFields));
    }
    
    // Validate email
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        sendJSONResponse(false, 'Invalid email address.');
    }
    
    // Sanitize inputs
    $firstName = mysqli_real_escape_string(getDBConnection(), trim($data['firstName']));
    $lastName = mysqli_real_escape_string(getDBConnection(), trim($data['lastName']));
    $email = mysqli_real_escape_string(getDBConnection(), trim($data['email']));
    $phone = isset($data['phone']) ? mysqli_real_escape_string(getDBConnection(), trim($data['phone'])) : null;
    $company = isset($data['company']) ? mysqli_real_escape_string(getDBConnection(), trim($data['company'])) : null;
    $service = mysqli_real_escape_string(getDBConnection(), trim($data['service']));
    $budget = isset($data['budget']) ? mysqli_real_escape_string(getDBConnection(), trim($data['budget'])) : null;
    $timeline = isset($data['timeline']) ? mysqli_real_escape_string(getDBConnection(), trim($data['timeline'])) : null;
    $projectDetails = mysqli_real_escape_string(getDBConnection(), trim($data['message']));
    $newsletter = isset($data['newsletter']) && $data['newsletter'] ? 1 : 0;
    
    // Get database connection
    $conn = getDBConnection();
    
    // Prepare SQL statement
    $sql = "INSERT INTO quotes (
        first_name, 
        last_name, 
        email, 
        phone, 
        company_name, 
        service, 
        budget, 
        timeline, 
        project_details, 
        newsletter,
        status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
    
    // Prepare statement
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        logError("Prepare failed: " . $conn->error);
        sendJSONResponse(false, 'Database error occurred. Please try again later.');
    }
    
    // Bind parameters
    $stmt->bind_param(
        "sssssssssi",
        $firstName,
        $lastName,
        $email,
        $phone,
        $company,
        $service,
        $budget,
        $timeline,
        $projectDetails,
        $newsletter
    );
    
    // Execute statement
    if ($stmt->execute()) {
        $quoteId = $stmt->insert_id;
        
        // Send thank you email to customer
        sendThankYouEmail($email, $firstName, $quoteId);
        
        // Send success response
        sendJSONResponse(true, 'Thank you! Your quote request has been submitted successfully. We will contact you within 24 hours.', [
            'quoteId' => $quoteId
        ]);
    } else {
        logError("Execute failed: " . $stmt->error);
        sendJSONResponse(false, 'Failed to submit quote request. Please try again.');
    }
    
    // Close statement and connection
    $stmt->close();
    closeDBConnection($conn);
    
} catch (Exception $e) {
    logError("Exception: " . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
}

/**
 * Send thank you email to customer
 */
function sendThankYouEmail($toEmail, $firstName, $quoteId) {
    $subject = "Thank You for Your Quote Request - EverythingEasy Technology";
    
    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: EverythingEasy Technology <info@everythingeasy.in>" . "\r\n";
    $headers .= "Reply-To: info@everythingeasy.in" . "\r\n";
    
    // Email body
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #0066cc 0%, #004499 100%); color: white; padding: 30px; text-align: center; }
            .content { background: #f8f9fa; padding: 30px; }
            .footer { background: #212529; color: #fff; padding: 20px; text-align: center; font-size: 14px; }
            .button { display: inline-block; padding: 12px 30px; background: #0066cc; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .info-box { background: white; padding: 20px; border-left: 4px solid #0066cc; margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>EverythingEasy Technology</h1>
                <p>Your Trusted IT Solutions Partner</p>
            </div>
            
            <div class='content'>
                <h2>Hello " . htmlspecialchars($firstName) . ",</h2>
                
                <p>Thank you for requesting a quote from EverythingEasy Technology!</p>
                
                <div class='info-box'>
                    <strong>Quote Request ID:</strong> #" . $quoteId . "<br>
                    <strong>Status:</strong> Under Review
                </div>
                
                <p>We have received your quote request and our team is already reviewing your requirements. We understand the importance of your project and are committed to providing you with the best solution.</p>
                
                <h3>What happens next?</h3>
                <ul>
                    <li>Our team will review your requirements carefully</li>
                    <li>We will prepare a customized proposal for your project</li>
                    <li>You will receive our detailed quote within 24 hours</li>
                    <li>A project consultant will reach out to discuss your needs</li>
                </ul>
                
                <p style='text-align: center;'>
                    <a href='https://everythingeasy.in' class='button'>Visit Our Website</a>
                </p>
                
                <p>If you have any immediate questions or need to add more information, please don't hesitate to contact us.</p>
                
                <p>Best regards,<br>
                <strong>The EverythingEasy Team</strong></p>
            </div>
            
            <div class='footer'>
                <p><strong>Contact Us</strong></p>
                <p>Email: info@everythingeasy.in | Phone: +91 86308 40577</p>
                <p>EverythingEasy Technology, Balawala, Dehradun 248001, Uttarakhand, India</p>
                <p style='margin-top: 15px; font-size: 12px;'>
                    &copy; " . date('Y') . " EverythingEasy Technology. All Rights Reserved.
                </p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Send email
    try {
        mail($toEmail, $subject, $message, $headers);
    } catch (Exception $e) {
        logError("Email failed: " . $e->getMessage());
        // Don't fail the quote submission if email fails
    }
}
?>

