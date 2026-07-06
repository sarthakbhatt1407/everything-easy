<?php
/**
 * Locations API
 * Handles all location page (SEO landing page) operations
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

// Handle preflight requestss
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Column definitions: type char for bind_param (s/i/d), whether required on create,
// and a default applied when the column is omitted on create.
$LOCATION_FIELDS = [
    'location_name'     => ['type' => 's', 'required' => true],
    'city_name'         => ['type' => 's', 'required' => true],
    'state'             => ['type' => 's', 'required' => true],
    'country'           => ['type' => 's', 'required' => true],
    'postal_code'       => ['type' => 's'],
    'latitude'          => ['type' => 'd'],
    'longitude'         => ['type' => 'd'],
    'slug'              => ['type' => 's', 'required' => true],
    'meta_title'        => ['type' => 's'],
    'meta_description'  => ['type' => 's'],
    'meta_keyword'      => ['type' => 's'],
    'h1_title'          => ['type' => 's'],
    'page_title'        => ['type' => 's'],
    'short_description' => ['type' => 's'],
    'content'           => ['type' => 's'],
    'og_title'          => ['type' => 's'],
    'og_description'    => ['type' => 's'],
    'og_image'          => ['type' => 's'],
    'canonical_url'     => ['type' => 's'],
    'robots'            => ['type' => 's', 'default' => 'index, follow'],
    'schema_type'       => ['type' => 's'],
    'schema_json'       => ['type' => 's', 'json' => true],
    'faq_json'          => ['type' => 's', 'json' => true],
    'area_served'       => ['type' => 's'],
    'image'             => ['type' => 's'],
    'image_alt'         => ['type' => 's'],
    'banner_image'      => ['type' => 's'],
    'review_rating'     => ['type' => 'd'],
    'review_count'      => ['type' => 'i', 'default' => 0],
    'priority'          => ['type' => 'd', 'default' => 0.8],
    'changefreq'        => ['type' => 's', 'default' => 'monthly'],
    'lastmod'           => ['type' => 's'],
    'status'            => ['type' => 'i', 'default' => 1],
];

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
        $action = isset($_GET['action']) ? $_GET['action'] : 'getAll';
    }

    switch ($action) {
        case 'getAll':
            getAllLocations($conn);
            break;

        case 'getOne':
            if (isset($_GET['id'])) {
                getOneLocation($conn, $_GET['id']);
            } else {
                sendJSONResponse(false, 'Location ID is required');
            }
            break;

        case 'getBySlug':
            if (isset($_GET['slug'])) {
                getLocationBySlug($conn, $_GET['slug']);
            } else {
                sendJSONResponse(false, 'Location slug is required');
            }
            break;

        case 'create':
            createLocation($conn, $data, $LOCATION_FIELDS);
            break;

        case 'update':
            updateLocation($conn, $data, $LOCATION_FIELDS);
            break;

        case 'delete':
            deleteLocation($conn, $data['id'] ?? null);
            break;

        default:
            sendJSONResponse(false, 'Invalid action');
    }
} catch (Exception $e) {
    logError("Exception: " . $e->getMessage());
    sendJSONResponse(false, 'An error occurred. Please try again later.');
}

// Get All Locations (paginated, with optional status/search filters)
function getAllLocations($conn) {
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? min(100, max(1, intval($_GET['limit']))) : 20;
    $offset = ($page - 1) * $limit;

    $where = [];
    $types = '';
    $params = [];

    if (isset($_GET['status']) && $_GET['status'] !== '') {
        $where[] = 'status = ?';
        $types .= 'i';
        $params[] = intval($_GET['status']);
    }

    if (isset($_GET['search']) && trim($_GET['search']) !== '') {
        $where[] = '(location_name LIKE ? OR city_name LIKE ? OR state LIKE ?)';
        $searchTerm = '%' . $_GET['search'] . '%';
        $types .= 'sss';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    $whereSql = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

    $countSql = "SELECT COUNT(*) as total FROM locations $whereSql";
    $countStmt = $conn->prepare($countSql);
    if (count($params) > 0) {
        $countStmt->bind_param($types, ...$params);
    }
    $countStmt->execute();
    $total = intval($countStmt->get_result()->fetch_assoc()['total']);
    $countStmt->close();

    $sql = "SELECT * FROM locations $whereSql ORDER BY id DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $listTypes = $types . 'ii';
    $listParams = array_merge($params, [$limit, $offset]);
    $stmt->bind_param($listTypes, ...$listParams);
    $stmt->execute();
    $result = $stmt->get_result();

    $locations = [];
    while ($row = $result->fetch_assoc()) {
        $locations[] = $row;
    }
    $stmt->close();

    sendJSONResponse(true, 'Locations retrieved successfully', [
        'locations' => $locations,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'total_pages' => (int) ceil($total / $limit),
        ],
    ]);
}

// Get One Location by ID
function getOneLocation($conn, $id) {
    $id = intval($id);
    $stmt = $conn->prepare("SELECT * FROM locations WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        sendJSONResponse(true, 'Location retrieved successfully', ['location' => $result->fetch_assoc()]);
    } else {
        sendJSONResponse(false, 'Location not found');
    }
}

// Get One Location by slug (for the public-facing location page)
function getLocationBySlug($conn, $slug) {
    $stmt = $conn->prepare("SELECT * FROM locations WHERE slug = ? AND status = 1");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        sendJSONResponse(true, 'Location retrieved successfully', ['location' => $result->fetch_assoc()]);
    } else {
        sendJSONResponse(false, 'Location not found');
    }
}

// Create Location
function createLocation($conn, $data, $fields) {
    foreach ($fields as $name => $def) {
        if (!empty($def['required']) && $name !== 'slug' && empty($data[$name])) {
            sendJSONResponse(false, "Field '$name' is required");
        }
    }

    $rawSlug = !empty($data['slug']) ? $data['slug'] : $data['location_name'];
    $slug = generateLocationSlug($rawSlug);
    if (empty($slug)) {
        sendJSONResponse(false, 'Unable to generate slug for this location.');
    }

    $slugCheckStmt = $conn->prepare("SELECT id FROM locations WHERE slug = ? LIMIT 1");
    $slugCheckStmt->bind_param("s", $slug);
    $slugCheckStmt->execute();
    if ($slugCheckStmt->get_result()->num_rows > 0) {
        $slugCheckStmt->close();
        sendJSONResponse(false, 'This location slug already exists.');
    }
    $slugCheckStmt->close();

    $values = buildFieldValues($fields, $data, $slug);

    $columns = array_keys($values);
    $placeholders = implode(', ', array_fill(0, count($columns), '?'));
    $types = implode('', array_map(fn($name) => $fields[$name]['type'], $columns));

    $sql = "INSERT INTO locations (" . implode(', ', $columns) . ") VALUES ($placeholders)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        logError("Create location prepare error: " . $conn->error);
        sendJSONResponse(false, 'Failed to create location');
    }

    $stmt->bind_param($types, ...array_values($values));

    if ($stmt->execute()) {
        sendJSONResponse(true, 'Location created successfully', ['id' => $stmt->insert_id, 'slug' => $slug]);
    } else {
        logError("Create location error: " . $stmt->error);
        sendJSONResponse(false, 'Failed to create location');
    }
}

// Update Location (partial update - only fields present in the request are changed)
function updateLocation($conn, $data, $fields) {
    if (empty($data['id'])) {
        sendJSONResponse(false, 'Location ID is required');
    }
    $id = intval($data['id']);

    $existingStmt = $conn->prepare("SELECT * FROM locations WHERE id = ?");
    $existingStmt->bind_param("i", $id);
    $existingStmt->execute();
    $existingResult = $existingStmt->get_result();
    if ($existingResult->num_rows === 0) {
        $existingStmt->close();
        sendJSONResponse(false, 'Location not found');
    }
    $existing = $existingResult->fetch_assoc();
    $existingStmt->close();

    if (array_key_exists('location_name', $data) && empty($data['location_name'])) {
        sendJSONResponse(false, "Field 'location_name' is required");
    }
    foreach (['city_name', 'state', 'country'] as $required) {
        if (array_key_exists($required, $data) && empty($data[$required])) {
            sendJSONResponse(false, "Field '$required' is required");
        }
    }

    if (array_key_exists('slug', $data) && !empty($data['slug'])) {
        $slug = generateLocationSlug($data['slug']);
    } else {
        $slug = $existing['slug'];
    }
    if (empty($slug)) {
        sendJSONResponse(false, 'Unable to generate slug for this location.');
    }

    if ($slug !== $existing['slug']) {
        $slugCheckStmt = $conn->prepare("SELECT id FROM locations WHERE slug = ? AND id != ? LIMIT 1");
        $slugCheckStmt->bind_param("si", $slug, $id);
        $slugCheckStmt->execute();
        if ($slugCheckStmt->get_result()->num_rows > 0) {
            $slugCheckStmt->close();
            sendJSONResponse(false, 'This location slug already exists.');
        }
        $slugCheckStmt->close();
    }

    $merged = array_merge($existing, $data);
    $values = buildFieldValues($fields, $merged, $slug);

    $columns = array_keys($values);
    $setSql = implode(', ', array_map(fn($col) => "$col = ?", $columns));
    $types = implode('', array_map(fn($name) => $fields[$name]['type'], $columns)) . 'i';

    $sql = "UPDATE locations SET $setSql, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        logError("Update location prepare error: " . $conn->error);
        sendJSONResponse(false, 'Failed to update location');
    }

    $bindValues = array_values($values);
    $bindValues[] = $id;
    $stmt->bind_param($types, ...$bindValues);

    if ($stmt->execute()) {
        sendJSONResponse(true, 'Location updated successfully');
    } else {
        logError("Update location error: " . $stmt->error);
        sendJSONResponse(false, 'Failed to update location');
    }
}

// Delete Location
function deleteLocation($conn, $id) {
    if (empty($id)) {
        sendJSONResponse(false, 'Location ID is required');
    }
    $id = intval($id);
    $stmt = $conn->prepare("DELETE FROM locations WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            sendJSONResponse(true, 'Location deleted successfully');
        } else {
            sendJSONResponse(false, 'Location not found');
        }
    } else {
        logError("Delete location error: " . $stmt->error);
        sendJSONResponse(false, 'Failed to delete location');
    }
}

// Builds the ordered [column => value] map used for INSERT/UPDATE, applying
// defaults and JSON-encoding array values for schema_json/faq_json.
function buildFieldValues($fields, $data, $slug) {
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

        $value = $data[$name];

        if (!empty($def['json']) && is_array($value)) {
            $value = json_encode($value);
        }

        if ($def['type'] === 'i') {
            $value = intval($value);
        } elseif ($def['type'] === 'd') {
            $value = floatval($value);
        }

        $values[$name] = $value;
    }
    return $values;
}

function generateLocationSlug($text) {
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
