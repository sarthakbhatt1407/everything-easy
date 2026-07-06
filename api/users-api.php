<?php
/**
 * Users API
 * Handles admin login/logout/session-check for the users table
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once '../backend/config.php';

// Roles allowed to log in through this endpoint.
// Plain 'User' role is intentionally excluded.
$ALLOWED_LOGIN_ROLES = ['Super Admin', 'Admin', 'Editor', 'SEO Manager', 'Content Writer'];

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// CORS: reflect the requesting origin (required for cookies/sessions to work
// cross-origin; wildcard '*' cannot be combined with credentials).
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header("Access-Control-Allow-Origin: $origin");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    $conn = getDBConnection();

    $action = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        if (!is_array($data)) {
            $data = [];
        }
        $action = isset($data['action']) ? $data['action'] : '';
    } else {
        $action = isset($_GET['action']) ? $_GET['action'] : 'me';
    }

    switch ($action) {
        case 'login':
            loginUser($conn, $data, $ALLOWED_LOGIN_ROLES);
            break;

        case 'logout':
            logoutUser();
            break;

        case 'me':
            currentUser($conn);
            break;

        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (Exception $e) {
    logError("Exception: " . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
}

function loginUser($conn, $data, $allowedRoles) {
    $identifier = isset($data['identifier']) ? trim($data['identifier']) : '';
    $password = isset($data['password']) ? $data['password'] : '';

    if ($identifier === '' || $password === '') {
        sendJSONResponse(false, 'Username/email and password are required');
    }

    $stmt = $conn->prepare("SELECT id, name, username, email, password, role, status FROM users WHERE username = ? OR email = ? LIMIT 1");
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $stmt->close();
        sendJSONResponse(false, 'Invalid username or password');
    }

    $user = $result->fetch_assoc();
    $stmt->close();

    if (!password_verify($password, $user['password'])) {
        sendJSONResponse(false, 'Invalid username or password');
    }

    if (intval($user['status']) !== 1) {
        sendJSONResponse(false, 'Your account is inactive. Contact an administrator.');
    }

    if (!in_array($user['role'], $allowedRoles, true)) {
        sendJSONResponse(false, 'This account role is not permitted to log in here');
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $updateStmt = $conn->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP, last_login_ip = ? WHERE id = ?");
    $updateStmt->bind_param("si", $ip, $user['id']);
    $updateStmt->execute();
    $updateStmt->close();

    unset($user['password']);
    sendJSONResponse(true, 'Login successful', ['user' => $user]);
}

function logoutUser() {
    $_SESSION = [];
    session_destroy();
    sendJSONResponse(true, 'Logged out successfully');
}

function currentUser($conn) {
    if (empty($_SESSION['user_id'])) {
        sendJSONResponse(false, 'Not logged in');
    }

    $id = intval($_SESSION['user_id']);
    $stmt = $conn->prepare("SELECT id, name, username, email, phone, role, profile_image, status, last_login FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        sendJSONResponse(false, 'User not found');
    }

    sendJSONResponse(true, 'Session active', ['user' => $result->fetch_assoc()]);
}

if (isset($conn)) {
    closeDBConnection($conn);
}
?>
