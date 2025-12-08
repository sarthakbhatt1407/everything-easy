<?php
/**
 * Blog API
 * Handles all blog-related operations
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
    
    // Determine action
    $action = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        $action = isset($data['action']) ? $data['action'] : '';
    } else {
        $action = isset($_GET['action']) ? $_GET['action'] : 'getAll';
    }
    
    switch ($action) {
        case 'getAll':
            getAllBlogs($conn);
            break;
        
        case 'getOne':
            if (isset($_GET['id'])) {
                getOneBlog($conn, $_GET['id']);
            } else {
                sendJSONResponse(false, 'Blog ID is required');
            }
            break;
        
        case 'create':
            createBlog($conn, $data);
            break;
        
        case 'update':
            updateBlog($conn, $data);
            break;
        
        case 'delete':
            deleteBlog($conn, $data['id']);
            break;
        
        case 'incrementViews':
            incrementViews($conn, $_GET['id']);
            break;
        
        default:
            sendJSONResponse(false, 'Invalid action');
    }
    
} catch (Exception $e) {
    logError("Exception: " . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
}

// Get All Blogs
function getAllBlogs($conn) {
    $sql = "SELECT * FROM blogs ORDER BY created_at DESC";
    $result = $conn->query($sql);
    
    $blogs = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $blogs[] = $row;
        }
    }
    
    sendJSONResponse(true, 'Blogs retrieved successfully', ['blogs' => $blogs]);
}

// Get One Blog
function getOneBlog($conn, $id) {
    $id = intval($id);
    $sql = "SELECT * FROM blogs WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $blog = $result->fetch_assoc();
        sendJSONResponse(true, 'Blog retrieved successfully', ['blog' => $blog]);
    } else {
        sendJSONResponse(false, 'Blog not found');
    }
}

// Create Blog
function createBlog($conn, $data) {
    // Validate required fields
    $required = ['title', 'excerpt', 'content', 'image_url', 'category', 'author'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            sendJSONResponse(false, "Field '$field' is required");
        }
    }
    
    $title = mysqli_real_escape_string($conn, $data['title']);
    $excerpt = mysqli_real_escape_string($conn, $data['excerpt']);
    $content = mysqli_real_escape_string($conn, $data['content']);
    $imageUrl = mysqli_real_escape_string($conn, $data['image_url']);
    $category = mysqli_real_escape_string($conn, $data['category']);
    $author = mysqli_real_escape_string($conn, $data['author']);
    $status = isset($data['status']) ? mysqli_real_escape_string($conn, $data['status']) : 'draft';
    $tags = isset($data['tags']) ? mysqli_real_escape_string($conn, $data['tags']) : '';
    
    $sql = "INSERT INTO blogs (title, excerpt, content, image_url, category, author, status, tags) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $title, $excerpt, $content, $imageUrl, $category, $author, $status, $tags);
    
    if ($stmt->execute()) {
        sendJSONResponse(true, 'Blog created successfully', ['id' => $stmt->insert_id]);
    } else {
        logError("Create blog error: " . $stmt->error);
        sendJSONResponse(false, 'Failed to create blog');
    }
}

// Update Blog
function updateBlog($conn, $data) {
    if (empty($data['id'])) {
        sendJSONResponse(false, 'Blog ID is required');
    }
    
    $id = intval($data['id']);
    $title = mysqli_real_escape_string($conn, $data['title']);
    $excerpt = mysqli_real_escape_string($conn, $data['excerpt']);
    $content = mysqli_real_escape_string($conn, $data['content']);
    $imageUrl = mysqli_real_escape_string($conn, $data['image_url']);
    $category = mysqli_real_escape_string($conn, $data['category']);
    $author = mysqli_real_escape_string($conn, $data['author']);
    $status = mysqli_real_escape_string($conn, $data['status']);
    $tags = isset($data['tags']) ? mysqli_real_escape_string($conn, $data['tags']) : '';
    
    $sql = "UPDATE blogs SET 
            title = ?, 
            excerpt = ?, 
            content = ?, 
            image_url = ?, 
            category = ?, 
            author = ?, 
            status = ?, 
            tags = ?,
            updated_at = CURRENT_TIMESTAMP
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssi", $title, $excerpt, $content, $imageUrl, $category, $author, $status, $tags, $id);
    
    if ($stmt->execute()) {
        sendJSONResponse(true, 'Blog updated successfully');
    } else {
        logError("Update blog error: " . $stmt->error);
        sendJSONResponse(false, 'Failed to update blog');
    }
}

// Delete Blog
function deleteBlog($conn, $id) {
    $id = intval($id);
    $sql = "DELETE FROM blogs WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        sendJSONResponse(true, 'Blog deleted successfully');
    } else {
        logError("Delete blog error: " . $stmt->error);
        sendJSONResponse(false, 'Failed to delete blog');
    }
}

// Increment Views
function incrementViews($conn, $id) {
    $id = intval($id);
    $sql = "UPDATE blogs SET views = views + 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        sendJSONResponse(true, 'Views incremented');
    } else {
        sendJSONResponse(false, 'Failed to increment views');
    }
}

// Close connection at the end
if (isset($conn)) {
    closeDBConnection($conn);
}
?>
