<?php
require_once 'backend/config.php';

// Allow only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSONResponse(false, "Invalid request method");
}

$conn = getDBConnection();

// Required fields
$required = ['fullName', 'email', 'phone', 'position', 'experience', 'coverLetter'];

//  print all required fields data for debugging hj
foreach ($required as $field) {
    error_log("$field: " . ($_POST[$field] ?? 'NULL'));
}    

foreach ($required as $field) {
    if (empty($_POST[$field])) {
        sendJSONResponse(false, "$field is required");
    }
}

// Validate email format
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    sendJSONResponse(false, "Invalid email format");
}

// Validate and prepare inputs
$fullName = trim($_POST['fullName']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$position = trim($_POST['position']);
$experience = (int) $_POST['experience'];
$portfolio = trim($_POST['portfolio'] ?? '');
$coverLetter = trim($_POST['coverLetter']);

// =============== FILE UPLOAD ================
if (!isset($_FILES['resume'])) {
    sendJSONResponse(false, "Resume file is required");
}

$resume = $_FILES['resume'];
$allowed_ext = ['pdf'];
$file_ext = strtolower(pathinfo($resume['name'], PATHINFO_EXTENSION));

if (!in_array($file_ext, $allowed_ext)) {
    sendJSONResponse(false, "Only PDF files are allowed");
}

if ($resume['size'] > 5 * 1024 * 1024) { // 5MB
    sendJSONResponse(false, "File too large. Maximum allowed size is 5MB");
}

// Create upload folder if not exists
$uploadDir = __DIR__ . "/upload/";

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$fileName = uniqid("RESUME_") . "." . $file_ext;
$uploadPath = $uploadDir . $fileName;
$dbFilePath = "backend/upload/" . $fileName;

// Upload file
if (!move_uploaded_file($resume['tmp_name'], $uploadPath)) {
    logError("File upload failed: " . $resume['name']);
    sendJSONResponse(false, "Failed to upload resume");
}

// =============== INSERT QUERY ================
$sql = "INSERT INTO job_applications 
        (full_name, email, phone, position, experience, portfolio, cover_letter, resume_path) 
        VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    logError("Prepare Error: " . $conn->error);
    sendJSONResponse(false, "Database error occurred");
}

$stmt->bind_param(
    "ssssisss",
    $fullName,
    $email,
    $phone,
    $position,
    $experience,
    $portfolio,
    $coverLetter,
    $dbFilePath
);

if ($stmt->execute()) {
    sendJSONResponse(true, "Application submitted successfully");
} else {
    logError("DB Insert Error: " . $stmt->error);
    sendJSONResponse(false, "Database error occurred");
}

$stmt->close();
closeDBConnection($conn);
?>
