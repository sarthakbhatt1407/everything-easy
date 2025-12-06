<?php
/**
 * Get Quotes API
 * Fetches all quotes for admin panel
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Include database config
require_once 'config.php';

// Set headers for CORS and JSON
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    // Get database connection
    $conn = getDBConnection();
    
    // Handle different actions
    $action = isset($_GET['action']) ? $_GET['action'] : 'list';
    
    switch ($action) {
        case 'list':
            // Get all quotes
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $offset = ($page - 1) * $limit;
            $status = isset($_GET['status']) ? $_GET['status'] : '';
            $search = isset($_GET['search']) ? $_GET['search'] : '';
            
            // Build query
            $sql = "SELECT * FROM quotes WHERE 1=1";
            
            if (!empty($status)) {
                $status = mysqli_real_escape_string($conn, $status);
                $sql .= " AND status = '$status'";
            }
            
            if (!empty($search)) {
                $search = mysqli_real_escape_string($conn, $search);
                $sql .= " AND (first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR email LIKE '%$search%' OR company_name LIKE '%$search%')";
            }
            
            // Get total count
            $countResult = $conn->query($sql);
            $totalRecords = $countResult->num_rows;
            
            // Add pagination
            $sql .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
            
            $result = $conn->query($sql);
            
            if (!$result) {
                logError("Query failed: " . $conn->error);
                sendJSONResponse(false, 'Database error occurred.');
            }
            
            $quotes = [];
            while ($row = $result->fetch_assoc()) {
                $quotes[] = $row;
            }
            
            // Get statistics
            $statsSQL = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'in-progress' THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
                FROM quotes";
            
            $statsResult = $conn->query($statsSQL);
            $stats = $statsResult->fetch_assoc();
            
            sendJSONResponse(true, 'Quotes fetched successfully', [
                'quotes' => $quotes,
                'stats' => $stats,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $totalRecords,
                    'totalPages' => ceil($totalRecords / $limit)
                ]
            ]);
            break;
            
        case 'get':
            // Get single quote by ID
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            
            if ($id <= 0) {
                sendJSONResponse(false, 'Invalid quote ID.');
            }
            
            $sql = "SELECT * FROM quotes WHERE id = $id";
            $result = $conn->query($sql);
            
            if ($result->num_rows === 0) {
                sendJSONResponse(false, 'Quote not found.');
            }
            
            $quote = $result->fetch_assoc();
            sendJSONResponse(true, 'Quote fetched successfully', ['quote' => $quote]);
            break;
            
        case 'update':
            // Update quote status
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                sendJSONResponse(false, 'Invalid request method.');
            }
            
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data) {
                $data = $_POST;
            }
            
            $id = isset($data['id']) ? (int)$data['id'] : 0;
            $status = isset($data['status']) ? $data['status'] : '';
            
            if ($id <= 0) {
                sendJSONResponse(false, 'Invalid quote ID.');
            }
            
            if (!in_array($status, ['pending', 'in-progress', 'completed'])) {
                sendJSONResponse(false, 'Invalid status.');
            }
            
            $status = mysqli_real_escape_string($conn, $status);
            $sql = "UPDATE quotes SET status = '$status' WHERE id = $id";
            
            if ($conn->query($sql)) {
                sendJSONResponse(true, 'Quote status updated successfully.');
            } else {
                logError("Update failed: " . $conn->error);
                sendJSONResponse(false, 'Failed to update quote status.');
            }
            break;
            
        case 'delete':
            // Delete quote
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
                sendJSONResponse(false, 'Invalid request method.');
            }
            
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data) {
                $data = $_POST;
            }
            
            $id = isset($data['id']) ? (int)$data['id'] : 0;
            
            if ($id <= 0) {
                sendJSONResponse(false, 'Invalid quote ID.');
            }
            
            $sql = "DELETE FROM quotes WHERE id = $id";
            
            if ($conn->query($sql)) {
                sendJSONResponse(true, 'Quote deleted successfully.');
            } else {
                logError("Delete failed: " . $conn->error);
                sendJSONResponse(false, 'Failed to delete quote.');
            }
            break;
            
        default:
            sendJSONResponse(false, 'Invalid action.');
    }
    
    closeDBConnection($conn);
    
} catch (Exception $e) {
    logError("Exception: " . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
}
?>
