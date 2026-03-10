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
        'akhilgusain2@gmail.com',
        'sarthakbhatt1407@gmail.com',
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

    $customerSubject = 'Quote Request Received #' . $quoteId;
    $customerBody = buildCustomerThankYouPlainText($quoteId, $lead['firstName']);

    $customerOk = safeSendCustomerThankYouMail($lead['email'], $customerSubject, $customerBody, $fromEmail, $fromName);
    if ($customerOk) {
        safeLog('SUCCESS: Thank-you email sent to ' . $lead['email'] . ' for Quote ID: ' . $quoteId);
    } else {
        $error = error_get_last();
        safeLog('FAILED: Thank-you email to ' . $lead['email'] . ' for Quote ID: ' . $quoteId . '. Error: ' . ($error ? json_encode($error) : 'Unknown error'));
    }
}

function safeSendCustomerThankYouMail($to, $subject, $plainBody, $fromEmail, $fromName) {
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        safeLog('Customer thank-you skipped due to invalid email: ' . $to);
        return false;
    }

    $smtpConfigured = defined('SMTP_HOST') && SMTP_HOST !== ''
        && defined('SMTP_USER') && SMTP_USER !== ''
        && defined('SMTP_PASS') && SMTP_PASS !== '';

    if ($smtpConfigured) {
        $smtpFromEmail = (defined('SMTP_FROM_EMAIL') && SMTP_FROM_EMAIL !== '') ? SMTP_FROM_EMAIL : $fromEmail;
        $smtpFromName = (defined('SMTP_FROM_NAME') && SMTP_FROM_NAME !== '') ? SMTP_FROM_NAME : $fromName;

        $smtpOk = smtpSendPlainTextMail($to, $subject, $plainBody, $smtpFromEmail, $smtpFromName);
        if ($smtpOk) {
            return true;
        }

        safeLog('SMTP failed for customer thank-you, using mail() fallback for: ' . $to);
    } else {
        safeLog('SMTP not configured for customer thank-you, using mail() fallback for: ' . $to);
    }

    return safeSendPlainTextMail($to, $subject, $plainBody, $fromEmail, $fromName);
}

function safeSendPlainTextMail($to, $subject, $plainBody, $fromEmail, $fromName) {
    try {
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            safeLog('safeSendPlainTextMail invalid recipient email: ' . $to);
            return false;
        }

        $safeFromName = str_replace(["\r", "\n"], '', $fromName);
        $safeFromEmail = filter_var($fromEmail, FILTER_VALIDATE_EMAIL) ? $fromEmail : 'noreply@everythingeasy.in';
        $safeSubject = trim(preg_replace('/[\r\n]+/', ' ', $subject));

        $headers = [];
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        $headers[] = 'Content-Transfer-Encoding: 8bit';
        $headers[] = 'From: ' . $safeFromName . ' <' . $safeFromEmail . '>';
        $headers[] = 'Reply-To: ' . $safeFromEmail;
        $headers[] = 'Return-Path: ' . $safeFromEmail;
        $headers[] = 'Date: ' . date(DATE_RFC2822);
        $headers[] = 'Message-ID: <' . uniqid('', true) . '@everythingeasy.in>';
        $headers[] = 'X-Mailer: PHP/' . phpversion();

        $params = '-f ' . $safeFromEmail;
        $headerString = implode("\r\n", $headers);

        $sent = @mail($to, $safeSubject, $plainBody, $headerString, $params);
        if ($sent) {
            return true;
        }

        return @mail($to, $safeSubject, $plainBody, $headerString);
    } catch (Throwable $e) {
        safeLog('safeSendPlainTextMail exception: ' . $e->getMessage());
        return false;
    }
}

function safeSendHtmlMail($to, $subject, $htmlBody, $fromEmail, $fromName) {
    try {
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            safeLog('safeSendHtmlMail invalid recipient email: ' . $to);
            return false;
        }

        $safeFromName = str_replace(["\r", "\n"], '', $fromName);
        $safeFromEmail = filter_var($fromEmail, FILTER_VALIDATE_EMAIL) ? $fromEmail : 'noreply@everythingeasy.in';
        $safeSubject = trim(preg_replace('/[\r\n]+/', ' ', $subject));

        $boundary = 'b1_' . md5((string)microtime(true));
        $textBody = html_entity_decode(strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $htmlBody)), ENT_QUOTES, 'UTF-8');

        $headers = [];
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';
        $headers[] = 'From: ' . $safeFromName . ' <' . $safeFromEmail . '>';
        $headers[] = 'Reply-To: ' . $safeFromEmail;
        $headers[] = 'Return-Path: ' . $safeFromEmail;
        $headers[] = 'Date: ' . date(DATE_RFC2822);
        $headers[] = 'Message-ID: <' . uniqid('', true) . '@everythingeasy.in>';
        $headers[] = 'X-Mailer: PHP/' . phpversion();

        $messageParts = [];
        $messageParts[] = '--' . $boundary;
        $messageParts[] = 'Content-Type: text/plain; charset=UTF-8';
        $messageParts[] = 'Content-Transfer-Encoding: 8bit';
        $messageParts[] = '';
        $messageParts[] = $textBody;
        $messageParts[] = '';
        $messageParts[] = '--' . $boundary;
        $messageParts[] = 'Content-Type: text/html; charset=UTF-8';
        $messageParts[] = 'Content-Transfer-Encoding: 8bit';
        $messageParts[] = '';
        $messageParts[] = $htmlBody;
        $messageParts[] = '';
        $messageParts[] = '--' . $boundary . '--';

        $messageBody = implode("\r\n", $messageParts);

        // Envelope sender improves SPF/DMARC alignment on many hosts.
        $params = '-f ' . $safeFromEmail;
        $headerString = implode("\r\n", $headers);

        $sent = @mail($to, $safeSubject, $messageBody, $headerString, $params);
        if ($sent) {
            return true;
        }

        // Fallback for hosts that disallow 5th parameter.
        return @mail($to, $safeSubject, $messageBody, $headerString);
    } catch (Throwable $e) {
        safeLog('safeSendHtmlMail exception: ' . $e->getMessage());
        return false;
    }
}

function smtpSendHtmlMail($to, $subject, $htmlBody, $fromEmail, $fromName) {
    $host = SMTP_HOST;
    $port = defined('SMTP_PORT') ? (int)SMTP_PORT : 587;
    $user = SMTP_USER;
    $pass = SMTP_PASS;
    $secure = defined('SMTP_SECURE') ? strtolower((string)SMTP_SECURE) : 'tls';

    $remote = $host . ':' . $port;
    if ($secure === 'ssl') {
        $remote = 'ssl://' . $host . ':' . $port;
    }

    $socket = @stream_socket_client($remote, $errno, $errstr, 20, STREAM_CLIENT_CONNECT);
    if (!$socket) {
        safeLog('SMTP connect failed: ' . $errno . ' ' . $errstr);
        return false;
    }

    stream_set_timeout($socket, 20);

    if (!smtpExpect($socket, [220])) {
        fclose($socket);
        return false;
    }

    $helloHost = gethostname() ?: 'localhost';
    smtpWrite($socket, 'EHLO ' . $helloHost);
    if (!smtpExpect($socket, [250])) {
        fclose($socket);
        return false;
    }

    if ($secure === 'tls') {
        smtpWrite($socket, 'STARTTLS');
        if (!smtpExpect($socket, [220])) {
            fclose($socket);
            return false;
        }

        if (!@stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            safeLog('SMTP TLS negotiation failed.');
            fclose($socket);
            return false;
        }

        smtpWrite($socket, 'EHLO ' . $helloHost);
        if (!smtpExpect($socket, [250])) {
            fclose($socket);
            return false;
        }
    }

    smtpWrite($socket, 'AUTH LOGIN');
    if (!smtpExpect($socket, [334])) {
        fclose($socket);
        return false;
    }

    smtpWrite($socket, base64_encode($user));
    if (!smtpExpect($socket, [334])) {
        fclose($socket);
        return false;
    }

    smtpWrite($socket, base64_encode($pass));
    if (!smtpExpect($socket, [235])) {
        fclose($socket);
        return false;
    }

    smtpWrite($socket, 'MAIL FROM:<' . $fromEmail . '>');
    if (!smtpExpect($socket, [250])) {
        fclose($socket);
        return false;
    }

    smtpWrite($socket, 'RCPT TO:<' . $to . '>');
    if (!smtpExpect($socket, [250, 251])) {
        fclose($socket);
        return false;
    }

    smtpWrite($socket, 'DATA');
    if (!smtpExpect($socket, [354])) {
        fclose($socket);
        return false;
    }

    $encodedName = function_exists('mb_encode_mimeheader')
        ? mb_encode_mimeheader($fromName, 'UTF-8')
        : $fromName;

    $safeSubject = trim(preg_replace('/[\r\n]+/', ' ', $subject));
    $headers = [];
    $headers[] = 'Date: ' . date(DATE_RFC2822);
    $headers[] = 'From: ' . $encodedName . ' <' . $fromEmail . '>';
    $headers[] = 'To: <' . $to . '>';
    $headers[] = 'Subject: ' . $safeSubject;
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    $headers[] = 'Content-Transfer-Encoding: 8bit';

    $payload = implode("\r\n", $headers) . "\r\n\r\n" . $htmlBody;
    $payload = str_replace("\r\n.\r\n", "\r\n..\r\n", $payload);

    smtpWrite($socket, $payload . "\r\n.");
    if (!smtpExpect($socket, [250])) {
        fclose($socket);
        return false;
    }

    smtpWrite($socket, 'QUIT');
    smtpExpect($socket, [221]);
    fclose($socket);

    return true;
}

function smtpSendPlainTextMail($to, $subject, $plainBody, $fromEmail, $fromName) {
    $safeBody = nl2br(htmlspecialchars($plainBody, ENT_QUOTES, 'UTF-8'));
    return smtpSendHtmlMail($to, $subject, $safeBody, $fromEmail, $fromName);
}

function smtpWrite($socket, $command) {
    fwrite($socket, $command . "\r\n");
}

function smtpExpect($socket, $expectedCodes) {
    $response = '';

    while (($line = fgets($socket, 515)) !== false) {
        $response .= $line;
        if (isset($line[3]) && $line[3] === ' ') {
            break;
        }
    }

    if ($response === '') {
        safeLog('SMTP empty response from server.');
        return false;
    }

    $code = (int)substr($response, 0, 3);
    if (!in_array($code, $expectedCodes, true)) {
        safeLog('SMTP unexpected response: ' . trim($response));
        return false;
    }

    return true;
}

function buildAdminLeadEmailHtml($quoteId, $lead) {
    $safe = function ($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    };

    $phoneRaw = trim((string)($lead['phone'] ?? ''));
    $phoneDisplay = $phoneRaw !== '' ? $safe($phoneRaw) : 'N/A';
    $phoneDial = preg_replace('/[^0-9+]/', '', $phoneRaw);
    $phoneButton = '';

    if ($phoneDial !== '') {
        $phoneButton = '<br><a href="tel:' . $phoneDial . '" style="display:inline-block;margin-top:8px;padding:6px 10px;background:#0066cc;color:#ffffff;text-decoration:none;border-radius:4px;font-size:12px;">Call Now</a>';
    }

    return "
    <html>
    <body style='font-family:Arial,sans-serif;color:#222;'>
      <h2>New Quote Lead Received</h2>
      <p><strong>Quote ID:</strong> #" . $safe($quoteId) . "</p>
      <table cellpadding='8' cellspacing='0' border='1' style='border-collapse:collapse;border-color:#ddd;'>
        <tr><td><strong>First Name</strong></td><td>" . $safe($lead['firstName']) . "</td></tr>
        <tr><td><strong>Last Name</strong></td><td>" . $safe($lead['lastName']) . "</td></tr>
        <tr><td><strong>Email</strong></td><td>" . $safe($lead['email']) . "</td></tr>
        <tr><td><strong>Phone</strong></td><td>" . $phoneDisplay . $phoneButton . "</td></tr>
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

function buildCustomerThankYouPlainText($quoteId, $firstName) {
    $name = trim((string)$firstName) !== '' ? trim((string)$firstName) : 'Customer';

    return "Hello {$name},\n\n"
        . "Thank you for contacting EverythingEasy Technology.\n"
        . "Your quote request has been received successfully.\n\n"
        . "Quote ID: #{$quoteId}\n"
        . "Expected response time: within 24 hours\n\n"
        . "If you need immediate help, reply to this email or contact us at +91 86308 40577.\n\n"
        . "Regards,\n"
        . "EverythingEasy Technology Team\n"
        . "https://everythingeasy.in\n";
}
?>

