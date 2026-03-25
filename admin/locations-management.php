<?php
require_once '../backend/config.php';

// Handle form submissions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $rawLocationName = trim($_POST['location_name']);
                $location_name = mysqli_real_escape_string($conn, $rawLocationName);
                $city_name = mysqli_real_escape_string($conn, $_POST['city_name']);
                $state = mysqli_real_escape_string($conn, $_POST['state']);
                $meta_title = mysqli_real_escape_string($conn, $_POST['meta_title']);
                $meta_description = mysqli_real_escape_string($conn, $_POST['meta_description']);
                $service_type = mysqli_real_escape_string($conn, $_POST['service_type']);

                $slug = generateSlug($rawLocationName);
                if (empty($slug)) {
                    $message = 'Unable to generate slug from location name. Please use a different name.';
                    $messageType = 'danger';
                    break;
                }

                $slugCheckSql = "SELECT id FROM locations WHERE slug = ? LIMIT 1";
                $slugCheckStmt = $conn->prepare($slugCheckSql);
                $slugCheckStmt->bind_param("s", $slug);
                $slugCheckStmt->execute();
                $slugCheckResult = $slugCheckStmt->get_result();
                if ($slugCheckResult->num_rows > 0) {
                    $slugCheckStmt->close();
                    $message = 'This location already exists.';
                    $messageType = 'danger';
                    break;
                }
                $slugCheckStmt->close();
                
                // Check if locations table has service_type column
                $sql = "INSERT INTO locations (location_name, city_name, state, slug, meta_title, meta_description, service_type) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                
                if ($stmt === false) {
                    // Table might not have service_type column, try without it
                    $sql = "INSERT INTO locations (location_name, city_name, state, slug, meta_title, meta_description) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    if ($stmt) {
                        $stmt->bind_param("ssssss", $location_name, $city_name, $state, $slug, $meta_title, $meta_description);
                    }
                } else {
                    $stmt->bind_param("sssssss", $location_name, $city_name, $state, $slug, $meta_title, $meta_description, $service_type);
                }
                
                if ($stmt && $stmt->execute()) {
                    $message = 'Location added successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Failed to add location.';
                    $messageType = 'danger';
                }
                if ($stmt) {
                    $stmt->close();
                }
                break;
                
            case 'update':
                $id = intval($_POST['id']);
                $rawLocationName = trim($_POST['location_name']);
                $location_name = mysqli_real_escape_string($conn, $rawLocationName);
                $city_name = mysqli_real_escape_string($conn, $_POST['city_name']);
                $state = mysqli_real_escape_string($conn, $_POST['state']);
                $meta_title = mysqli_real_escape_string($conn, $_POST['meta_title']);
                $meta_description = mysqli_real_escape_string($conn, $_POST['meta_description']);
                $service_type = mysqli_real_escape_string($conn, $_POST['service_type']);

                $slug = generateSlug($rawLocationName);
                if (empty($slug)) {
                    $message = 'Unable to generate slug from location name. Please use a different name.';
                    $messageType = 'danger';
                    break;
                }

                $slugCheckSql = "SELECT id FROM locations WHERE slug = ? AND id != ? LIMIT 1";
                $slugCheckStmt = $conn->prepare($slugCheckSql);
                $slugCheckStmt->bind_param("si", $slug, $id);
                $slugCheckStmt->execute();
                $slugCheckResult = $slugCheckStmt->get_result();
                if ($slugCheckResult->num_rows > 0) {
                    $slugCheckStmt->close();
                    $message = 'This location name is already in use.';
                    $messageType = 'danger';
                    break;
                }
                $slugCheckStmt->close();
                
                $sql = "UPDATE locations SET location_name = ?, city_name = ?, state = ?, slug = ?, meta_title = ?, meta_description = ?, service_type = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt === false) {
                    $sql = "UPDATE locations SET location_name = ?, city_name = ?, state = ?, slug = ?, meta_title = ?, meta_description = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    if ($stmt) {
                        $stmt->bind_param("ssssssi", $location_name, $city_name, $state, $slug, $meta_title, $meta_description, $id);
                    }
                } else {
                    $stmt->bind_param("sssssssi", $location_name, $city_name, $state, $slug, $meta_title, $meta_description, $service_type, $id);
                }
                
                if ($stmt && $stmt->execute()) {
                    $message = 'Location updated successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Failed to update location.';
                    $messageType = 'danger';
                }
                if ($stmt) {
                    $stmt->close();
                }
                break;

            case 'delete':
                $id = intval($_POST['id']);
                $sql = "DELETE FROM locations WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id);
                
                if ($stmt->execute()) {
                    $message = 'Location deleted successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Failed to delete location.';
                    $messageType = 'danger';
                }
                $stmt->close();
                break;
        }
    }
    
    closeDBConnection($conn);
}

// Fetch all locations
$conn = getDBConnection();
$locations = [];
$sql = "SELECT id, location_name, city_name, state, slug, meta_title, meta_description FROM locations ORDER BY id DESC";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $locations[] = $row;
    }
}

$totalLocations = count($locations);
closeDBConnection($conn);

function generateSlug($title) {
    $slug = strtolower($title);
    $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locations Management - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin-style.css">

    <script>
        (function guardAdminSession() {
            const SESSION_KEY = 'admin_logged_in';
            const SESSION_TIMESTAMP = 'admin_login_time';
            const isLoggedIn = sessionStorage.getItem(SESSION_KEY) === 'true';

            if (!isLoggedIn) {
                sessionStorage.removeItem(SESSION_KEY);
                sessionStorage.removeItem(SESSION_TIMESTAMP);
                window.location.replace('login.html');
                return;
            }

            const loginTime = Number(sessionStorage.getItem(SESSION_TIMESTAMP));
            const elapsedMs = Date.now() - loginTime;
            const maxDurationMs = 24 * 60 * 60 * 1000;

            if (!Number.isFinite(loginTime) || loginTime <= 0 || elapsedMs < 0 || elapsedMs >= maxDurationMs) {
                sessionStorage.removeItem(SESSION_KEY);
                sessionStorage.removeItem(SESSION_TIMESTAMP);
                alert('Your session has expired. Please login again.');
                window.location.replace('login.html');
            }
        })();
    </script>
</head>

<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-home"></i> EverythingEasy</h3>
            <p class="text-muted small">Admin Panel</p>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="dashboard.html">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Quote Requests</span>
                </a>
            </li>
            <li>
                <a href="blog-management.php">
                    <i class="fas fa-blog"></i>
                    <span>Blog Posts</span>
                </a>
            </li>
            <li>
                <a href="applications-management.php">
                    <i class="fas fa-file-alt"></i>
                    <span>Job Applications</span>
                </a>
            </li>
            <li class="active">
                <a href="locations-management.php">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Locations</span>
                </a>
            </li>
            <li>
                <a href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <div class="top-navbar">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center">
                    <h2><i class="fas fa-map-marker-alt me-2"></i>Locations Management</h2>
                    <div class="user-info">
                        <i class="fas fa-user-circle"></i>
                        <span>Admin</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid mt-4 mb-5">
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="fw-bold">All Locations (<?php echo $totalLocations; ?>)</h4>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLocationModal">
                        <i class="fas fa-plus me-2"></i>Add New Location
                    </button>
                </div>
            </div>

            <div class="table-responsive bg-white rounded shadow-sm">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Location Name</th>
                            <th>City</th>
                            <th>State</th>
                            <th>Slug</th>
                            <th>Meta Title</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($locations) > 0): ?>
                            <?php foreach ($locations as $loc): ?>
                                <tr>
                                    <td><?php echo $loc['id']; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($loc['location_name']); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($loc['city_name']); ?></td>
                                    <td><?php echo htmlspecialchars($loc['state']); ?></td>
                                    <td>
                                        <code><?php echo htmlspecialchars($loc['slug']); ?></code>
                                    </td>
                                    <td>
                                        <small><?php echo htmlspecialchars(substr($loc['meta_title'], 0, 40)); ?>...</small>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editLocationModal" 
                                                onclick="loadLocationData(<?php echo htmlspecialchars(json_encode($loc)); ?>)">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteLocationModal"
                                                onclick="setDeleteId(<?php echo $loc['id']; ?>)">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No locations found. <br>
                                    <button class="btn btn-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#addLocationModal">
                                        Add First Location
                                    </button>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Location Modal -->
    <div class="modal fade" id="addLocationModal" tabindex="-1" aria-labelledby="addLocationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addLocationModalLabel">Add New Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="location_name_add" class="form-label">Location Name *</label>
                            <input type="text" class="form-control" id="location_name_add" name="location_name" 
                                   placeholder="e.g., Dehradun, Mumbai, Bangalore" required>
                            <small class="text-muted">This will be used to generate the URL slug</small>
                        </div>
                        <div class="mb-3">
                            <label for="city_name_add" class="form-label">City Name *</label>
                            <input type="text" class="form-control" id="city_name_add" name="city_name" 
                                   placeholder="e.g., Dehradun" required>
                        </div>
                        <div class="mb-3">
                            <label for="state_add" class="form-label">State *</label>
                            <input type="text" class="form-control" id="state_add" name="state" 
                                   placeholder="e.g., Uttarakhand" required>
                        </div>
                        <div class="mb-3">
                            <label for="service_type_add" class="form-label">Service Type</label>
                            <select class="form-select" id="service_type_add" name="service_type">
                                <option value="">Both (Web & App)</option>
                                <option value="website">Website Development</option>
                                <option value="application">Application Development</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="meta_title_add" class="form-label">Meta Title *</label>
                            <input type="text" class="form-control" id="meta_title_add" name="meta_title" 
                                   placeholder="SEO title for search engines" maxlength="60" required>
                            <small class="text-muted">Max 60 characters</small>
                        </div>
                        <div class="mb-3">
                            <label for="meta_description_add" class="form-label">Meta Description *</label>
                            <textarea class="form-control" id="meta_description_add" name="meta_description" 
                                      rows="3" placeholder="SEO description for search engines" maxlength="160" required></textarea>
                            <small class="text-muted">Max 160 characters</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <input type="hidden" name="action" value="create">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add Location
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Location Modal -->
    <div class="modal fade" id="editLocationModal" tabindex="-1" aria-labelledby="editLocationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editLocationModalLabel">Edit Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" id="edit_id" name="id">
                        <div class="mb-3">
                            <label for="location_name_edit" class="form-label">Location Name *</label>
                            <input type="text" class="form-control" id="location_name_edit" name="location_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="city_name_edit" class="form-label">City Name *</label>
                            <input type="text" class="form-control" id="city_name_edit" name="city_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="state_edit" class="form-label">State *</label>
                            <input type="text" class="form-control" id="state_edit" name="state" required>
                        </div>
                        <div class="mb-3">
                            <label for="service_type_edit" class="form-label">Service Type</label>
                            <select class="form-select" id="service_type_edit" name="service_type">
                                <option value="">Both (Web & App)</option>
                                <option value="website">Website Development</option>
                                <option value="application">Application Development</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="meta_title_edit" class="form-label">Meta Title *</label>
                            <input type="text" class="form-control" id="meta_title_edit" name="meta_title" maxlength="60" required>
                            <small class="text-muted">Max 60 characters</small>
                        </div>
                        <div class="mb-3">
                            <label for="meta_description_edit" class="form-label">Meta Description *</label>
                            <textarea class="form-control" id="meta_description_edit" name="meta_description" rows="3" maxlength="160" required></textarea>
                            <small class="text-muted">Max 160 characters</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <input type="hidden" name="action" value="update">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Location
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Location Modal -->
    <div class="modal fade" id="deleteLocationModal" tabindex="-1" aria-labelledby="deleteLocationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteLocationModalLabel">Delete Location</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <p>Are you sure you want to delete this location? This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <input type="hidden" id="delete_id" name="id">
                        <input type="hidden" name="action" value="delete">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Delete Location
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to logout?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="logoutBtn">Logout</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="admin-script.js"></script>

    <script>
        function loadLocationData(location) {
            document.getElementById('edit_id').value = location.id;
            document.getElementById('location_name_edit').value = location.location_name;
            document.getElementById('city_name_edit').value = location.city_name;
            document.getElementById('state_edit').value = location.state;
            document.getElementById('meta_title_edit').value = location.meta_title;
            document.getElementById('meta_description_edit').value = location.meta_description;
        }

        function setDeleteId(id) {
            document.getElementById('delete_id').value = id;
        }

        document.getElementById('logoutBtn').addEventListener('click', function() {
            sessionStorage.removeItem('admin_logged_in');
            sessionStorage.removeItem('admin_login_time');
            window.location.replace('login.html');
        });
    </script>
</body>

</html>
