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
?>
