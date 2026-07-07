<?php
/**
 * Blog Post API
 * Handles all blog_post table operations
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Include database config
require_once '../backend/config.php';

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

// Column definitions: type char for bind_param (s/i/d), whether required on create.
$BLOG_POST_FIELDS = [
    'title'             => ['type' => 's', 'required' => true],
    'slug'              => ['type' => 's', 'required' => true],
    'meta_title'        => ['type' => 's'],
    'meta_description'  => ['type' => 's'],
    'meta_keywords'     => ['type' => 's'],
    'description'       => ['type' => 's'],
    'content'           => ['type' => 's'],
    'publish_date'      => ['type' => 's'],
    'author'            => ['type' => 's'],
    'category'          => ['type' => 's'],
    'tags'              => ['type' => 's'],
    'status'            => ['type' => 's', 'default' => 'published'],
    'image_url'         => ['type' => 's', 'required' => true],
];

// Image upload handling: max upload size and the max width we downscale to
// before re-encoding as WebP.
define('BLOG_IMAGE_MAX_BYTES', 5 * 1024 * 1024);
define('BLOG_IMAGE_MAX_WIDTH', 1600);
define('BLOG_IMAGE_WEBP_QUALITY', 80);

try {
    $conn = getDBConnection();

    $action = '';
    $data = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!empty($_POST) || !empty($_FILES)) {
            // multipart/form-data or x-www-form-urlencoded (used when uploading an image file)
            $data = $_POST;
        } else {
            $input = file_get_contents('php://input');
            $decoded = json_decode($input, true);
            if (is_array($decoded)) {
                $data = $decoded;
            }
        }
        $action = isset($data['action']) ? $data['action'] : '';
    } else {
        $action = isset($_GET['action']) ? $_GET['action'] : 'getAll';
    }

    switch ($action) {
        case 'getAll':
            getAllBlogPosts($conn);
            break;

        case 'getOne':
            if (isset($_GET['id'])) {
                getOneBlogPost($conn, $_GET['id']);
            } else {
                sendJSONResponse(false, 'Blog post ID is required');
            }
            break;

        case 'getBySlug':
            if (isset($_GET['slug'])) {
                getBlogPostBySlug($conn, $_GET['slug']);
            } else {
                sendJSONResponse(false, 'Blog post slug is required');
            }
            break;

        case 'create':
            createBlogPost($conn, $data, $BLOG_POST_FIELDS, $_FILES);
            break;

        case 'update':
            updateBlogPost($conn, $data, $BLOG_POST_FIELDS, $_FILES);
            break;

        case 'delete':
            deleteBlogPost($conn, $data['id'] ?? null);
            break;

        case 'incrementViews':
            if (isset($_GET['id'])) {
                incrementBlogPostViews($conn, $_GET['id']);
            } else {
                sendJSONResponse(false, 'Blog post ID is required');
            }
            break;

        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (Exception $e) {
    logError("Exception: " . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
}

// Get All Blog Posts (paginated, with optional category/search filters)
function getAllBlogPosts($conn) {
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? min(100, max(1, intval($_GET['limit']))) : 20;
    $offset = ($page - 1) * $limit;

    $where = [];
    $types = '';
    $params = [];

    if (isset($_GET['category']) && trim($_GET['category']) !== '') {
        $where[] = 'category = ?';
        $types .= 's';
        $params[] = $_GET['category'];
    }

    if (isset($_GET['search']) && trim($_GET['search']) !== '') {
        $where[] = '(title LIKE ? OR author LIKE ? OR tags LIKE ?)';
        $searchTerm = '%' . $_GET['search'] . '%';
        $types .= 'sss';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    $whereSql = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

    $countSql = "SELECT COUNT(*) as total FROM blog_post $whereSql";
    $countStmt = $conn->prepare($countSql);
    if (count($params) > 0) {
        $countStmt->bind_param($types, ...$params);
    }
    $countStmt->execute();
    $total = intval($countStmt->get_result()->fetch_assoc()['total']);
    $countStmt->close();

    $sql = "SELECT * FROM blog_post $whereSql ORDER BY id DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $listTypes = $types . 'ii';
    $listParams = array_merge($params, [$limit, $offset]);
    $stmt->bind_param($listTypes, ...$listParams);
    $stmt->execute();
    $result = $stmt->get_result();

    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    $stmt->close();

    sendJSONResponse(true, 'Blog posts retrieved successfully', [
        'posts' => $posts,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'total_pages' => (int) ceil($total / $limit),
        ],
    ]);
}

// Get One Blog Post by ID
function getOneBlogPost($conn, $id) {
    $id = intval($id);
    $stmt = $conn->prepare("SELECT * FROM blog_post WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        sendJSONResponse(true, 'Blog post retrieved successfully', ['post' => $result->fetch_assoc()]);
    } else {
        sendJSONResponse(false, 'Blog post not found');
    }
}

// Get One Blog Post by slug (for the public-facing blog page)
function getBlogPostBySlug($conn, $slug) {
    $stmt = $conn->prepare("SELECT * FROM blog_post WHERE slug = ?");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        sendJSONResponse(true, 'Blog post retrieved successfully', ['post' => $result->fetch_assoc()]);
    } else {
        sendJSONResponse(false, 'Blog post not found');
    }
}

// Create Blog Post
function createBlogPost($conn, $data, $fields, $files) {
    $uploadedImage = handleBlogImageUpload($files);
    if ($uploadedImage !== null) {
        $data['image_url'] = $uploadedImage;
    }

    foreach ($fields as $name => $def) {
        if (!empty($def['required']) && $name !== 'slug' && empty($data[$name])) {
            sendJSONResponse(false, "Field '$name' is required");
        }
    }

    $rawSlug = !empty($data['slug']) ? $data['slug'] : $data['title'];
    $slug = generateBlogPostSlug($rawSlug);
    if (empty($slug)) {
        sendJSONResponse(false, 'Unable to generate slug for this blog post.');
    }

    $slugCheckStmt = $conn->prepare("SELECT id FROM blog_post WHERE slug = ? LIMIT 1");
    $slugCheckStmt->bind_param("s", $slug);
    $slugCheckStmt->execute();
    if ($slugCheckStmt->get_result()->num_rows > 0) {
        $slugCheckStmt->close();
        sendJSONResponse(false, 'This slug is already in use.');
    }
    $slugCheckStmt->close();

    $values = buildBlogPostValues($fields, $data, $slug);

    $columns = array_keys($values);
    $placeholders = implode(', ', array_fill(0, count($columns), '?'));
    $types = implode('', array_map(fn($name) => $fields[$name]['type'], $columns));

    $sql = "INSERT INTO blog_post (" . implode(', ', $columns) . ") VALUES ($placeholders)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        logError("Create blog post prepare error: " . $conn->error);
        sendJSONResponse(false, 'Failed to create blog post');
    }

    $stmt->bind_param($types, ...array_values($values));

    if ($stmt->execute()) {
        sendJSONResponse(true, 'Blog post created successfully', ['id' => $stmt->insert_id, 'slug' => $slug]);
    } else {
        logError("Create blog post error: " . $stmt->error);
        sendJSONResponse(false, 'Failed to create blog post');
    }
}

// Update Blog Post (partial update - only fields present in the request are changed)
function updateBlogPost($conn, $data, $fields, $files) {
    if (empty($data['id'])) {
        sendJSONResponse(false, 'Blog post ID is required');
    }
    $id = intval($data['id']);

    $existingStmt = $conn->prepare("SELECT * FROM blog_post WHERE id = ?");
    $existingStmt->bind_param("i", $id);
    $existingStmt->execute();
    $existingResult = $existingStmt->get_result();
    if ($existingResult->num_rows === 0) {
        $existingStmt->close();
        sendJSONResponse(false, 'Blog post not found');
    }
    $existing = $existingResult->fetch_assoc();
    $existingStmt->close();

    $uploadedImage = handleBlogImageUpload($files);
    if ($uploadedImage !== null) {
        $data['image_url'] = $uploadedImage;
        deleteLocalBlogImage($existing['image_url']);
    }

    if (array_key_exists('title', $data) && empty($data['title'])) {
        sendJSONResponse(false, "Field 'title' is required");
    }
    if (array_key_exists('image_url', $data) && empty($data['image_url'])) {
        sendJSONResponse(false, "Field 'image_url' is required");
    }

    if (array_key_exists('slug', $data) && !empty($data['slug'])) {
        $slug = generateBlogPostSlug($data['slug']);
    } else {
        $slug = $existing['slug'];
    }
    if (empty($slug)) {
        sendJSONResponse(false, 'Unable to generate slug for this blog post.');
    }

    if ($slug !== $existing['slug']) {
        $slugCheckStmt = $conn->prepare("SELECT id FROM blog_post WHERE slug = ? AND id != ? LIMIT 1");
        $slugCheckStmt->bind_param("si", $slug, $id);
        $slugCheckStmt->execute();
        if ($slugCheckStmt->get_result()->num_rows > 0) {
            $slugCheckStmt->close();
            sendJSONResponse(false, 'This slug is already in use.');
        }
        $slugCheckStmt->close();
    }

    $merged = array_merge($existing, $data);
    $values = buildBlogPostValues($fields, $merged, $slug);

    $columns = array_keys($values);
    $setSql = implode(', ', array_map(fn($col) => "$col = ?", $columns));
    $types = implode('', array_map(fn($name) => $fields[$name]['type'], $columns)) . 'i';

    $sql = "UPDATE blog_post SET $setSql WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        logError("Update blog post prepare error: " . $conn->error);
        sendJSONResponse(false, 'Failed to update blog post');
    }

    $bindValues = array_values($values);
    $bindValues[] = $id;
    $stmt->bind_param($types, ...$bindValues);

    if ($stmt->execute()) {
        sendJSONResponse(true, 'Blog post updated successfully');
    } else {
        logError("Update blog post error: " . $stmt->error);
        sendJSONResponse(false, 'Failed to update blog post');
    }
}

// Delete Blog Post
function deleteBlogPost($conn, $id) {
    if (empty($id)) {
        sendJSONResponse(false, 'Blog post ID is required');
    }
    $id = intval($id);
    $stmt = $conn->prepare("DELETE FROM blog_post WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            sendJSONResponse(true, 'Blog post deleted successfully');
        } else {
            sendJSONResponse(false, 'Blog post not found');
        }
    } else {
        logError("Delete blog post error: " . $stmt->error);
        sendJSONResponse(false, 'Failed to delete blog post');
    }
}

// Increment Views
function incrementBlogPostViews($conn, $id) {
    $id = intval($id);
    $stmt = $conn->prepare("UPDATE blog_post SET views = views + 1 WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        sendJSONResponse(true, 'Views incremented');
    } else {
        sendJSONResponse(false, 'Failed to increment views');
    }
}

// Handles an uploaded 'image' file: validates it, downscales it if it's
// wider than BLOG_IMAGE_MAX_WIDTH, and re-encodes it as WebP to keep storage
// and page-load size down. Returns the relative path to store in image_url,
// or null if no file was uploaded (caller falls back to a plain image_url).
function handleBlogImageUpload($files) {
    if (!isset($files['image']) || $files['image']['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $file = $files['image'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        sendJSONResponse(false, 'Image upload failed');
    }

    if ($file['size'] > BLOG_IMAGE_MAX_BYTES) {
        sendJSONResponse(false, 'Image is too large. Maximum size is 5MB.');
    }

    $mime = mime_content_type($file['tmp_name']);
    $creators = [
        'image/jpeg' => 'imagecreatefromjpeg',
        'image/png'  => 'imagecreatefrompng',
        'image/gif'  => 'imagecreatefromgif',
        'image/webp' => 'imagecreatefromwebp',
    ];

    if (!isset($creators[$mime])) {
        sendJSONResponse(false, 'Invalid image type. Only JPG, PNG, GIF, and WEBP are allowed.');
    }

    $source = @$creators[$mime]($file['tmp_name']);
    if ($source === false) {
        sendJSONResponse(false, 'Unable to read the uploaded image.');
    }

    $width = imagesx($source);
    $height = imagesy($source);

    if ($width > BLOG_IMAGE_MAX_WIDTH) {
        $newWidth = BLOG_IMAGE_MAX_WIDTH;
        $newHeight = (int) round($height * ($newWidth / $width));

        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($source);
        $source = $resized;
    }

    $uploadDir = '../uploads/blog-images/';
    $fileName = 'blog_' . time() . '_' . uniqid() . '.webp';
    $uploadPath = $uploadDir . $fileName;

    $saved = imagewebp($source, $uploadPath, BLOG_IMAGE_WEBP_QUALITY);
    imagedestroy($source);

    if (!$saved) {
        sendJSONResponse(false, 'Failed to save the uploaded image.');
    }

    return 'uploads/blog-images/' . $fileName;
}

// Deletes a previously uploaded local blog image (no-op for external URLs).
function deleteLocalBlogImage($imageUrl) {
    if (empty($imageUrl) || strpos($imageUrl, 'uploads/blog-images/') !== 0) {
        return;
    }
    $path = '../' . $imageUrl;
    if (file_exists($path)) {
        unlink($path);
    }
}

// Builds the ordered [column => value] map used for INSERT/UPDATE
function buildBlogPostValues($fields, $data, $slug) {
    $values = [];
    foreach ($fields as $name => $def) {
        if ($name === 'slug') {
            $values[$name] = $slug;
            continue;
        }

        $hasValue = array_key_exists($name, $data) && $data[$name] !== '' && $data[$name] !== null;

        if (!$hasValue) {
            $values[$name] = $def['default'] ?? null;
            continue;
        }

        $values[$name] = $data[$name];
    }
    return $values;
}

function generateBlogPostSlug($text) {
    $slug = strtolower(trim($text));
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    $slug = preg_replace('/[\s-]+/', '-', $slug);
    return trim($slug, '-');
}

// Close connection at the end
if (isset($conn)) {
    closeDBConnection($conn);
}
?>
