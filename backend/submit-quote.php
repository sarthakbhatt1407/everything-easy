<?php
/**
 * Submit Quote API
 * Simple and robust quote handler.
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once 'config.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    sendJSONResponse(true, 'Submit Quote API is running. Use POST to submit quote data.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    sendJSONResponse(false, 'Invalid request method. Only POST is allowed.');
}

$conn = null;
$stmt = null;

try {
    $data = getRequestData();

    $requiredFields = ['firstName', 'lastName', 'email', 'service', 'message'];
    $missingFields = [];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || trim((string)$data[$field]) === '') {
            $missingFields[] = $field;
        }
    }

    if (!empty($missingFields)) {
        sendJSONResponse(false, 'Missing required fields: ' . implode(', ', $missingFields));
    }

    $firstName = safeString($data, 'firstName');
    $lastName = safeString($data, 'lastName');
    $email = safeString($data, 'email');
    $service = safeString($data, 'service');
    $projectDetails = safeString($data, 'message');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendJSONResponse(false, 'Invalid email address.');
    }

    $phone = nullableString($data, 'phone');
    $company = nullableString($data, 'company');
    $budget = nullableString($data, 'budget');
    $timeline = nullableString($data, 'timeline');
    $newsletter = !empty($data['newsletter']) ? 1 : 0;

    $conn = getDBConnection();

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

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        logError('Prepare failed: ' . $conn->error);
        sendJSONResponse(false, 'Database error occurred. Please try again later.');
    }

    $stmt->bind_param(
        'sssssssssi',
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

    if (!$stmt->execute()) {
        logError('Execute failed: ' . $stmt->error);
        sendJSONResponse(false, 'Failed to submit quote request. Please try again.');
    }

    $quoteId = (int)$stmt->insert_id;

    // Email failure should never break API success response.
    sendLeadNotificationEmails($quoteId, [
        'firstName' => $firstName,
        'lastName' => $lastName,
        'email' => $email,
        'phone' => $phone,
        'company' => $company,
        'service' => $service,
        'budget' => $budget,
        'timeline' => $timeline,
        'message' => $projectDetails,
        'newsletter' => $newsletter,
    ]);

    $stmt->close();
    closeDBConnection($conn);

    sendJSONResponse(true, 'Thank you! Your quote request has been submitted successfully. We will contact you within 24 hours.', [
        'quoteId' => $quoteId,
    ]);
} catch (Throwable $e) {
    if ($stmt instanceof mysqli_stmt) {
        $stmt->close();
    }
    if ($conn instanceof mysqli) {
        closeDBConnection($conn);
    }

    logError('Exception in submit-quote.php: ' . $e->getMessage() . ' at line ' . $e->getLine());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
}

function getRequestData() {
    $input = file_get_contents('php://input');
    $json = json_decode($input, true);
    if (is_array($json)) {
        return $json;
    }
    if (is_array($_POST) && !empty($_POST)) {
        return $_POST;
    }
    return [];
}

function safeString($data, $key) {
    if (!isset($data[$key]) || is_array($data[$key]) || is_object($data[$key])) {
        return '';
    }
    return trim((string)$data[$key]);
}

function nullableString($data, $key) {
    $value = safeString($data, $key);
    return $value === '' ? null : $value;
}

function sendLeadNotificationEmails($quoteId, $lead) {
    $adminRecipients = [
        'info@everythingeasy.in',
        'akhilgusain2@gmail.com',
        'akhilgusain65@gmail.com',
    ];

    $fromEmail = 'info@everythingeasy.in';
    $fromName = 'EverythingEasy Leads';

    $adminSubject = 'New Quote Lead #' . $quoteId . ' - ' . $lead['firstName'] . ' ' . $lead['lastName'];
    $adminBody = buildAdminLeadEmailHtml($quoteId, $lead);

    foreach ($adminRecipients as $recipient) {
        $ok = safeSendHtmlMail($recipient, $adminSubject, $adminBody, $fromEmail, $fromName);
        if ($ok) {
            logError('SUCCESS: Lead email sent to admin ' . $recipient . ' for Quote ID: ' . $quoteId);
        } else {
            $error = error_get_last();
            logError('FAILED: Lead email to admin ' . $recipient . ' for Quote ID: ' . $quoteId . '. Error: ' . ($error ? json_encode($error) : 'Unknown error'));
        }
    }

    $customerSubject = 'Thanks for contacting EverythingEasy - Quote #' . $quoteId;
    $customerBody = buildCustomerThankYouEmailHtml($quoteId, $lead['firstName']);

    $customerOk = safeSendHtmlMail($lead['email'], $customerSubject, $customerBody, $fromEmail, $fromName);
    if ($customerOk) {
        logError('SUCCESS: Thank-you email sent to ' . $lead['email'] . ' for Quote ID: ' . $quoteId);
    } else {
        $error = error_get_last();
        logError('FAILED: Thank-you email to ' . $lead['email'] . ' for Quote ID: ' . $quoteId . '. Error: ' . ($error ? json_encode($error) : 'Unknown error'));
    }
}

function safeSendHtmlMail($to, $subject, $htmlBody, $fromEmail, $fromName) {
    try {
        $safeFromName = str_replace(["\r", "\n"], '', $fromName);
        $safeFromEmail = filter_var($fromEmail, FILTER_VALIDATE_EMAIL) ? $fromEmail : 'noreply@everythingeasy.in';

        $headers = [];
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=UTF-8';
        $headers[] = 'From: ' . $safeFromName . ' <' . $safeFromEmail . '>';
        $headers[] = 'Reply-To: ' . $safeFromEmail;

        return @mail($to, $subject, $htmlBody, implode("\r\n", $headers));
    } catch (Throwable $e) {
        logError('safeSendHtmlMail exception: ' . $e->getMessage());
        return false;
    }
}

function buildAdminLeadEmailHtml($quoteId, $lead) {
    $safe = function ($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    };

    return "
    <html>
    <body style='font-family:Arial,sans-serif;color:#222;'>
      <h2>New Quote Lead Received</h2>
      <p><strong>Quote ID:</strong> #" . $safe($quoteId) . "</p>
      <table cellpadding='8' cellspacing='0' border='1' style='border-collapse:collapse;border-color:#ddd;'>
        <tr><td><strong>First Name</strong></td><td>" . $safe($lead['firstName']) . "</td></tr>
        <tr><td><strong>Last Name</strong></td><td>" . $safe($lead['lastName']) . "</td></tr>
        <tr><td><strong>Email</strong></td><td>" . $safe($lead['email']) . "</td></tr>
        <tr><td><strong>Phone</strong></td><td>" . $safe($lead['phone']) . "</td></tr>
        <tr><td><strong>Company</strong></td><td>" . $safe($lead['company']) . "</td></tr>
        <tr><td><strong>Service</strong></td><td>" . $safe($lead['service']) . "</td></tr>
        <tr><td><strong>Budget</strong></td><td>" . $safe($lead['budget']) . "</td></tr>
        <tr><td><strong>Timeline</strong></td><td>" . $safe($lead['timeline']) . "</td></tr>
        <tr><td><strong>Newsletter</strong></td><td>" . ($lead['newsletter'] ? 'Yes' : 'No') . "</td></tr>
        <tr><td><strong>Message</strong></td><td>" . nl2br($safe($lead['message'])) . "</td></tr>
      </table>
      <p style='margin-top:14px;'>Submitted from EverythingEasy website.</p>
    </body>
    </html>
    ";
}

function buildCustomerThankYouEmailHtml($quoteId, $firstName) {
    $safeName = htmlspecialchars((string)$firstName, ENT_QUOTES, 'UTF-8');
    return "
    <html>
    <body style='font-family:Arial,sans-serif;color:#222;'>
      <h2>Thank you, " . $safeName . "!</h2>
      <p>We received your quote request successfully.</p>
      <p><strong>Your Quote ID:</strong> #" . (int)$quoteId . "</p>
      <p>Our team will contact you soon.</p>
      <p>Regards,<br><strong>EverythingEasy Team</strong></p>
    </body>
    </html>
    ";
}
?>

