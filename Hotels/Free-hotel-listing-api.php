<?php
/**
 * Free Hotel Listing API
 * Returns hotel data from the hotels table as JSON.
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../backend/config.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	http_response_code(200);
	exit;
}

try {
	$conn = getDBConnection();

	if (!tableExists($conn, 'hotels')) {
		sendJSONResponse(false, 'Table "hotels" does not exist. Please import the hotels schema first.');
	}

	$action = isset($_GET['action']) ? strtolower(trim($_GET['action'])) : 'list';

	switch ($action) {
		case 'list':
			getHotels($conn);
			break;

		case 'get':
			$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
			if ($id <= 0) {
				sendJSONResponse(false, 'Hotel ID is required.');
			}

			getHotelById($conn, $id);
			break;

		default:
			sendJSONResponse(false, 'Invalid action. Use "list" or "get".');
	}
} catch (Throwable $e) {
	logError('Hotel API error: ' . $e->getMessage());
	sendJSONResponse(false, 'An error occurred while fetching hotel data.');
}

function getHotels($conn)
{
	$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
	$limit = isset($_GET['limit']) ? max(1, (int) $_GET['limit']) : 2000;
	$offset = ($page - 1) * $limit;

	$filters = [];

	if (!empty($_GET['city'])) {
		$city = mysqli_real_escape_string($conn, trim($_GET['city']));
		$filters[] = "city = '$city'";
	}

	if (!empty($_GET['state'])) {
		$state = mysqli_real_escape_string($conn, trim($_GET['state']));
		$filters[] = "state = '$state'";
	}

	if (!empty($_GET['food_type'])) {
		$foodType = mysqli_real_escape_string($conn, trim($_GET['food_type']));
		$filters[] = "food_type = '$foodType'";
	}

	if (!empty($_GET['cuisine'])) {
		$cuisine = mysqli_real_escape_string($conn, trim($_GET['cuisine']));
		$filters[] = "cuisine = '$cuisine'";
	}

	if (!empty($_GET['search'])) {
		$search = mysqli_real_escape_string($conn, trim($_GET['search']));
		$filters[] = "(name LIKE '%$search%' OR city LIKE '%$search%' OR state LIKE '%$search%' OR address LIKE '%$search%' OR cuisine LIKE '%$search%')";
	}

	$whereClause = count($filters) > 0 ? ' WHERE ' . implode(' AND ', $filters) : '';

	$countSql = "SELECT COUNT(*) AS total FROM hotels" . $whereClause;
	$countResult = $conn->query($countSql);

	if (!$countResult) {
		logError('Hotel count query failed: ' . $conn->error);
		sendJSONResponse(false, 'Failed to fetch hotel data.');
	}

	$totalRecords = (int) $countResult->fetch_assoc()['total'];

	$sql = "SELECT * FROM hotels" . $whereClause . " ORDER BY created_at DESC, id DESC LIMIT $limit OFFSET $offset";
	$result = $conn->query($sql);

	if (!$result) {
		logError('Hotel list query failed: ' . $conn->error);
		sendJSONResponse(false, 'Failed to fetch hotel data.');
	}

	$hotels = [];
	while ($row = $result->fetch_assoc()) {
		$hotels[] = $row;
	}

	sendJSONResponse(true, 'Hotels fetched successfully', [
		'hotels' => $hotels,
		'pagination' => [
			'page' => $page,
			'limit' => $limit,
			'total' => $totalRecords,
			'totalPages' => (int) ceil($totalRecords / $limit),
		],
	]);
}

function getHotelById($conn, $id)
{
	$sql = "SELECT * FROM hotels WHERE id = ? LIMIT 1";
	$stmt = $conn->prepare($sql);

	if (!$stmt) {
		logError('Hotel detail prepare failed: ' . $conn->error);
		sendJSONResponse(false, 'Failed to fetch hotel data.');
	}

	$stmt->bind_param('i', $id);
	$stmt->execute();
	$result = $stmt->get_result();

	if ($result->num_rows === 0) {
		sendJSONResponse(false, 'Hotel not found.');
	}

	sendJSONResponse(true, 'Hotel fetched successfully', [
		'hotel' => $result->fetch_assoc(),
	]);
}

function tableExists($conn, $tableName)
{
	$tableName = mysqli_real_escape_string($conn, $tableName);
	$result = $conn->query("SHOW TABLES LIKE '$tableName'");

	return $result && $result->num_rows > 0;
}
?>
