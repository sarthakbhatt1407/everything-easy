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
        safeLog('Prepare failed: ' . $conn->error);
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
        safeLog('Execute failed: ' . $stmt->error);
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

    safeLog('Exception in submit-quote.php: ' . $e->getMessage() . ' at line ' . $e->getLine());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
}

function safeLog($message) {
    try {
        $logDir = __DIR__ . '/logs';
        $logFile = $logDir . '/error.log';

        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
        @file_put_contents($logFile, $line, FILE_APPEND);
    } catch (Throwable $e) {
        // Last-resort fallback to PHP error log.
        error_log('submit-quote safeLog failure: ' . $e->getMessage());
    }
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
        'akhilgusain65@gmail.com',
    ];

    $fromEmail = 'info@everythingeasy.in';
    $fromName = 'EverythingEasy Leads';

    $adminSubject = 'New Quote Lead #' . $quoteId . ' - ' . $lead['firstName'] . ' ' . $lead['lastName'];
    $adminBody = buildAdminLeadEmailHtml($quoteId, $lead);

    foreach ($adminRecipients as $recipient) {
        $ok = safeSendHtmlMail($recipient, $adminSubject, $adminBody, $fromEmail, $fromName);
        if ($ok) {
            safeLog('SUCCESS: Lead email sent to admin ' . $recipient . ' for Quote ID: ' . $quoteId);
        } else {
            $error = error_get_last();
            safeLog('FAILED: Lead email to admin ' . $recipient . ' for Quote ID: ' . $quoteId . '. Error: ' . ($error ? json_encode($error) : 'Unknown error'));
        }
    }

    $customerSubject = 'Thanks for contacting EverythingEasy - Quote #' . $quoteId;
    $customerBody = buildCustomerThankYouEmailHtml($quoteId, $lead['firstName']);

    $customerOk = safeSendHtmlMail($lead['email'], $customerSubject, $customerBody, $fromEmail, $fromName);
    if ($customerOk) {
        safeLog('SUCCESS: Thank-you email sent to ' . $lead['email'] . ' for Quote ID: ' . $quoteId);
    } else {
        $error = error_get_last();
        safeLog('FAILED: Thank-you email to ' . $lead['email'] . ' for Quote ID: ' . $quoteId . '. Error: ' . ($error ? json_encode($error) : 'Unknown error'));
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
        safeLog('safeSendHtmlMail exception: ' . $e->getMessage());
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
    $year = date('Y');
    
    return '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - EverythingEasy</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f7fa;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f4f7fa;padding:30px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#0066cc 0%,#004499 100%);padding:40px 30px;text-align:center;">
                            <h1 style="margin:0;color:#ffffff;font-size:28px;font-weight:700;letter-spacing:-0.5px;">EverythingEasy</h1>
                            <p style="margin:8px 0 0;color:rgba(255,255,255,0.9);font-size:14px;">Technology Solutions Partner</p>
                        </td>
                    </tr>
                    
                    <!-- Success Icon -->
                    <tr>
                        <td style="padding:40px 30px 20px;text-align:center;">
                            <div style="width:80px;height:80px;margin:0 auto;background-color:#e8f5e9;border-radius:50%;display:inline-block;line-height:80px;">
                                <span style="font-size:40px;color:#4caf50;">&#10003;</span>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Greeting -->
                    <tr>
                        <td style="padding:0 30px 20px;text-align:center;">
                            <h2 style="margin:0 0 10px;color:#222222;font-size:24px;font-weight:600;">Thank You, ' . $safeName . '!</h2>
                            <p style="margin:0;color:#666666;font-size:16px;line-height:1.6;">We have received your quote request and our team is already on it.</p>
                        </td>
                    </tr>
                    
                    <!-- Quote ID Box -->
                    <tr>
                        <td style="padding:0 30px 30px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="background-color:#f8f9fa;border-left:4px solid #0066cc;padding:20px 25px;border-radius:0 8px 8px 0;">
                                        <p style="margin:0 0 5px;color:#888888;font-size:12px;text-transform:uppercase;letter-spacing:1px;">Your Reference Number</p>
                                        <p style="margin:0;color:#0066cc;font-size:28px;font-weight:700;">#' . (int)$quoteId . '</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- What Happens Next -->
                    <tr>
                        <td style="padding:0 30px 30px;">
                            <h3 style="margin:0 0 20px;color:#222222;font-size:18px;font-weight:600;">What Happens Next?</h3>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="padding:12px 0;border-bottom:1px solid #eeeeee;">
                                        <table role="presentation" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="width:40px;vertical-align:top;">
                                                    <div style="width:28px;height:28px;background-color:#e3f2fd;border-radius:50%;text-align:center;line-height:28px;color:#0066cc;font-weight:600;font-size:14px;">1</div>
                                                </td>
                                                <td style="vertical-align:top;">
                                                    <p style="margin:0;color:#333333;font-size:15px;"><strong>Review</strong> - Our experts will analyze your requirements</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0;border-bottom:1px solid #eeeeee;">
                                        <table role="presentation" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="width:40px;vertical-align:top;">
                                                    <div style="width:28px;height:28px;background-color:#e3f2fd;border-radius:50%;text-align:center;line-height:28px;color:#0066cc;font-weight:600;font-size:14px;">2</div>
                                                </td>
                                                <td style="vertical-align:top;">
                                                    <p style="margin:0;color:#333333;font-size:15px;"><strong>Proposal</strong> - We\'ll prepare a customized solution for you</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0;">
                                        <table role="presentation" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="width:40px;vertical-align:top;">
                                                    <div style="width:28px;height:28px;background-color:#e3f2fd;border-radius:50%;text-align:center;line-height:28px;color:#0066cc;font-weight:600;font-size:14px;">3</div>
                                                </td>
                                                <td style="vertical-align:top;">
                                                    <p style="margin:0;color:#333333;font-size:15px;"><strong>Contact</strong> - Expect a call within 24 hours</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- CTA Button -->
                    <tr>
                        <td style="padding:0 30px 30px;text-align:center;">
                            <a href="https://everythingeasy.in" style="display:inline-block;padding:14px 40px;background-color:#0066cc;color:#ffffff;text-decoration:none;font-size:16px;font-weight:600;border-radius:8px;">Visit Our Website</a>
                        </td>
                    </tr>
                    
                    <!-- Contact Info -->
                    <tr>
                        <td style="padding:30px;background-color:#f8f9fa;border-top:1px solid #eeeeee;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="text-align:center;">
                                        <p style="margin:0 0 15px;color:#333333;font-size:16px;font-weight:600;">Need Immediate Help?</p>
                                        <p style="margin:0 0 8px;color:#666666;font-size:14px;">
                                            <span style="color:#0066cc;">&#9742;</span>&nbsp;&nbsp;
                                            <a href="tel:+918630840577" style="color:#333333;text-decoration:none;">+91 86308 40577</a>
                                        </p>
                                        <p style="margin:0;color:#666666;font-size:14px;">
                                            <span style="color:#0066cc;">&#9993;</span>&nbsp;&nbsp;
                                            <a href="mailto:info@everythingeasy.in" style="color:#333333;text-decoration:none;">info@everythingeasy.in</a>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="padding:25px 30px;background-color:#222222;text-align:center;">
                            <p style="margin:0 0 10px;color:#ffffff;font-size:14px;font-weight:600;">EverythingEasy Technology</p>
                            <p style="margin:0 0 15px;color:#aaaaaa;font-size:12px;">Balawala, Dehradun 248001, Uttarakhand, India</p>
                            <p style="margin:0;color:#888888;font-size:11px;">&copy; ' . $year . ' EverythingEasy Technology. All Rights Reserved.</p>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
}
?>

