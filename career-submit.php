<?php
header("Content-Type: application/json");
require_once 'backend/config.php';

// Allow only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit;
}

// Required fields checking
$required = ['fullName', 'email', 'phone', 'position', 'experience', 'coverLetter'];

foreach ($required as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(["status" => "error", "message" => "$field is required"]);
        exit;
    }
}

// Escape inputs
$fullName = mysqli_real_escape_string($mysqli, $_POST['fullName']);
$email = mysqli_real_escape_string($mysqli, $_POST['email']);
$phone = mysqli_real_escape_string($mysqli, $_POST['phone']);
$position = mysqli_real_escape_string($mysqli, $_POST['position']);
$experience = (int) $_POST['experience'];
$portfolio = mysqli_real_escape_string($mysqli, $_POST['portfolio'] ?? '');
$coverLetter = mysqli_real_escape_string($mysqli, $_POST['coverLetter']);

// File Upload Handling
if (!isset($_FILES['resume'])) {
    echo json_encode(["status" => "error", "message" => "Resume file is required"]);
    exit;
}

$resume = $_FILES['resume'];
$allowed_ext = ['pdf'];
$file_ext = strtolower(pathinfo($resume['name'], PATHINFO_EXTENSION));

if (!in_array($file_ext, $allowed_ext)) {
    echo json_encode(["status" => "error", "message" => "Only PDF file allowed"]);
    exit;
}

if ($resume['size'] > 5 * 1024 * 1024) { // 5MB
    echo json_encode(["status" => "error", "message" => "File too large. Max 5MB"]);
    exit;
}

$uploadDir = "upload/";
$fileName = uniqid("RESUME_") . "." . $file_ext;
$uploadPath = $uploadDir . $fileName;

if (!move_uploaded_file($resume['tmp_name'], $uploadPath)) {
    echo json_encode(["status" => "error", "message" => "File upload failed"]);
    exit;
}

// Insert into database
$sql = "INSERT INTO job_applications 
        (full_name, email, phone, position, experience, portfolio, cover_letter, resume_path)
        VALUES 
        ('$fullName', '$email', '$phone', '$position', $experience, '$portfolio', '$coverLetter', '$uploadPath')";

if (mysqli_query($mysqli, $sql)) {
    echo json_encode(["status" => "success", "message" => "Application submitted successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Database error: " . mysqli_error($mysqli)]);
}
?>
