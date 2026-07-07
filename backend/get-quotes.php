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
            
        case 'create':
            // Manually add a new lead from the admin panel
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                sendJSONResponse(false, 'Invalid request method.');
            }

            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            if (!is_array($data)) {
                $data = $_POST;
            }

            $required = ['first_name', 'last_name', 'email', 'service', 'project_details'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    sendJSONResponse(false, "Field '$field' is required");
                }
            }

            $status = in_array($data['status'] ?? '', ['pending', 'in-progress', 'completed'], true)
                ? $data['status'] : 'pending';
            $leadCategory = in_array($data['lead_category'] ?? '', ['genuine', 'spam', 'fake', 'internship', 'job'], true)
                ? $data['lead_category'] : 'genuine';

            $stmt = $conn->prepare("INSERT INTO quotes
                (first_name, last_name, email, phone, company_name, service, budget, timeline, lead_source, project_details, status, lead_category, remarks)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $phone = $data['phone'] ?? null;
            $companyName = $data['company_name'] ?? null;
            $budget = $data['budget'] ?? null;
            $timeline = $data['timeline'] ?? null;
            $leadSource = $data['lead_source'] ?? null;
            $remarks = $data['remarks'] ?? null;
            $stmt->bind_param(
                'sssssssssssss',
                $data['first_name'],
                $data['last_name'],
                $data['email'],
                $phone,
                $companyName,
                $data['service'],
                $budget,
                $timeline,
                $leadSource,
                $data['project_details'],
                $status,
                $leadCategory,
                $remarks
            );

            if ($stmt->execute()) {
                sendJSONResponse(true, 'Lead added successfully.', ['id' => $stmt->insert_id]);
            } else {
                logError("Create lead error: " . $stmt->error);
                sendJSONResponse(false, 'Failed to add lead.');
            }
            $stmt->close();
            break;

        case 'update':
            // Update one or more fields on a quote/lead: status, lead_category,
            // lead_source, and/or remarks. Only fields present in the request are changed.
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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

            $setClauses = [];
            $types = '';
            $params = [];

            if (array_key_exists('status', $data)) {
                if (!in_array($data['status'], ['pending', 'in-progress', 'completed'], true)) {
                    sendJSONResponse(false, 'Invalid status.');
                }
                $setClauses[] = 'status = ?';
                $types .= 's';
                $params[] = $data['status'];
            }

            if (array_key_exists('lead_category', $data)) {
                if (!in_array($data['lead_category'], ['genuine', 'spam', 'fake', 'internship', 'job'], true)) {
                    sendJSONResponse(false, 'Invalid lead category.');
                }
                $setClauses[] = 'lead_category = ?';
                $types .= 's';
                $params[] = $data['lead_category'];
            }

            if (array_key_exists('lead_source', $data)) {
                $setClauses[] = 'lead_source = ?';
                $types .= 's';
                $params[] = $data['lead_source'];
            }

            if (array_key_exists('remarks', $data)) {
                $setClauses[] = 'remarks = ?';
                $types .= 's';
                $params[] = $data['remarks'];
            }

            if (empty($setClauses)) {
                sendJSONResponse(false, 'No fields to update.');
            }

            $sql = "UPDATE quotes SET " . implode(', ', $setClauses) . " WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $types .= 'i';
            $params[] = $id;
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                sendJSONResponse(true, 'Lead updated successfully.');
            } else {
                logError("Update failed: " . $stmt->error);
                sendJSONResponse(false, 'Failed to update lead.');
            }
            $stmt->close();
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
