<?php
require_once '../backend/config.php';

$applications = [];
$message = '';
$messageType = '';

$conn = getDBConnection();

$sql = "SELECT id, full_name, email, phone, position, experience, portfolio, cover_letter, resume_path, created_at FROM job_applications ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $applications[] = $row;
    }
} else {
    $message = 'Unable to load applications. Please check the job_applications table.';
    $messageType = 'danger';
    logError('Applications fetch error: ' . $conn->error);
}

$totalApplications = count($applications);
$uniquePositions = count(array_unique(array_column($applications, 'position')));

closeDBConnection($conn);

function formatDate($dateString) {
    $date = new DateTime($dateString);
    return $date->format('M j, Y h:i A');
}

function getResumeUrl($path) {
    if (empty($path)) {
        return '';
    }
    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
        return $path;
    }
    return '../' . ltrim($path, '/');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applications - Admin Dashboard</title>
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
            <li class="active">
                <a href="dashboard.html">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Quote Requests</span>
                    <span class="badge">0</span>
                </a>
            </li>
            <li>
                <a href="blog-management.php">
                    <i class="fas fa-blog"></i>
                    <span>Blog Posts</span>
                </a>
            </li>
            <li class="active">
                <a href="applications-management.php">
                    <i class="fas fa-file-lines"></i>
                    <span>Job Applications</span>
                </a>
            </li>
            <li>
                <a href="locations-management.php">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Service Locations</span>
                </a>
            </li>
            <li>
                <a href="#" id="logoutBtn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <nav class="topnav">
            <div class="topnav-left">
                <button class="toggle-btn" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <h4 class="page-title">Job Applications</h4>
            </div>
            <div class="topnav-right">
                <div class="admin-profile">
                    <img src="../images/logo.jpg" alt="EverythingEasy Logo" class="admin-avatar">
                    <span>Admin</span>
                </div>
                <button class="btn btn-sm btn-outline-danger ms-3" onclick="logout()" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </div>
        </nav>

        <div class="dashboard-container">
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="stat-card stat-primary">
                        <div class="stat-icon">
                            <i class="fas fa-file-lines"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $totalApplications; ?></h3>
                            <p>Total Applications</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stat-card stat-info">
                        <div class="stat-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $uniquePositions; ?></h3>
                            <p>Positions Applied</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-card">
                <div class="card-header-flex">
                    <h5><i class="fas fa-list me-2"></i> All Job Applications</h5>
                </div>

                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Position</th>
                                <th>Experience</th>
                                <th>Portfolio</th>
                                <th>Cover Letter</th>
                                <th>Resume</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($totalApplications === 0): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No applications found yet.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($applications as $index => $application): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($application['full_name']); ?></td>
                                        <td>
                                            <div><?php echo htmlspecialchars($application['email']); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($application['phone']); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($application['position']); ?></td>
                                        <td><?php echo (int)$application['experience']; ?> years</td>
                                        <td>
                                            <?php if (!empty($application['portfolio'])): ?>
                                                <a href="<?php echo htmlspecialchars($application['portfolio']); ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-arrow-up-right-from-square"></i> Open
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="max-width: 260px; white-space: normal;">
                                            <?php echo htmlspecialchars($application['cover_letter']); ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($application['resume_path'])): ?>
                                                <a href="<?php echo htmlspecialchars(getResumeUrl($application['resume_path'])); ?>" target="_blank" rel="noopener" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-file-pdf"></i> View
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars(formatDate($application['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                sessionStorage.removeItem('admin_logged_in');
                sessionStorage.removeItem('admin_login_time');
                window.location.href = 'login.html';
            }
        }

        document.getElementById('toggleSidebar')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('collapsed');
        });
    </script>
</body>

</html>
