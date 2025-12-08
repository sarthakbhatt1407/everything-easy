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
                $title = mysqli_real_escape_string($conn, $_POST['title']);
                $excerpt = mysqli_real_escape_string($conn, $_POST['excerpt']);
                $content = mysqli_real_escape_string($conn, $_POST['content']);
                $category = mysqli_real_escape_string($conn, $_POST['category']);
                $author = mysqli_real_escape_string($conn, $_POST['author']);
                $status = mysqli_real_escape_string($conn, $_POST['status']);
                $tags = mysqli_real_escape_string($conn, $_POST['tags']);
                
                // Handle image upload
                $imageUrl = '';
                if (isset($_FILES['blog_image']) && $_FILES['blog_image']['error'] === 0) {
                    $uploadDir = '../uploads/blog-images/';
                    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                    $maxSize = 5 * 1024 * 1024; // 5MB
                    
                    $fileType = $_FILES['blog_image']['type'];
                    $fileSize = $_FILES['blog_image']['size'];
                    
                    if (!in_array($fileType, $allowedTypes)) {
                        $message = 'Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.';
                        $messageType = 'danger';
                        break;
                    }
                    
                    if ($fileSize > $maxSize) {
                        $message = 'File size too large. Maximum size is 5MB.';
                        $messageType = 'danger';
                        break;
                    }
                    
                    $fileExtension = pathinfo($_FILES['blog_image']['name'], PATHINFO_EXTENSION);
                    $newFileName = 'blog_' . time() . '_' . uniqid() . '.' . $fileExtension;
                    $uploadPath = $uploadDir . $newFileName;
                    
                    if (move_uploaded_file($_FILES['blog_image']['tmp_name'], $uploadPath)) {
                        $imageUrl = 'uploads/blog-images/' . $newFileName;
                    } else {
                        $message = 'Failed to upload image.';
                        $messageType = 'danger';
                        break;
                    }
                } elseif (!empty($_POST['image_url'])) {
                    // Use URL if provided and no file uploaded
                    $imageUrl = mysqli_real_escape_string($conn, $_POST['image_url']);
                } else {
                    $message = 'Please provide an image (upload or URL).';
                    $messageType = 'danger';
                    break;
                }
                
                $sql = "INSERT INTO blogs (title, excerpt, content, image_url, category, author, status, tags) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssss", $title, $excerpt, $content, $imageUrl, $category, $author, $status, $tags);
                
                if ($stmt->execute()) {
                    $message = 'Blog post created successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Failed to create blog post.';
                    $messageType = 'danger';
                }
                $stmt->close();
                break;
                
            case 'update':
                $id = intval($_POST['id']);
                $title = mysqli_real_escape_string($conn, $_POST['title']);
                $excerpt = mysqli_real_escape_string($conn, $_POST['excerpt']);
                $content = mysqli_real_escape_string($conn, $_POST['content']);
                $category = mysqli_real_escape_string($conn, $_POST['category']);
                $author = mysqli_real_escape_string($conn, $_POST['author']);
                $status = mysqli_real_escape_string($conn, $_POST['status']);
                $tags = mysqli_real_escape_string($conn, $_POST['tags']);
                
                // Handle image upload for update
                $imageUrl = mysqli_real_escape_string($conn, $_POST['existing_image_url']);
                
                if (isset($_FILES['blog_image']) && $_FILES['blog_image']['error'] === 0) {
                    $uploadDir = '../uploads/blog-images/';
                    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                    $maxSize = 5 * 1024 * 1024; // 5MB
                    
                    $fileType = $_FILES['blog_image']['type'];
                    $fileSize = $_FILES['blog_image']['size'];
                    
                    if (!in_array($fileType, $allowedTypes)) {
                        $message = 'Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.';
                        $messageType = 'danger';
                        break;
                    }
                    
                    if ($fileSize > $maxSize) {
                        $message = 'File size too large. Maximum size is 5MB.';
                        $messageType = 'danger';
                        break;
                    }
                    
                    $fileExtension = pathinfo($_FILES['blog_image']['name'], PATHINFO_EXTENSION);
                    $newFileName = 'blog_' . time() . '_' . uniqid() . '.' . $fileExtension;
                    $uploadPath = $uploadDir . $newFileName;
                    
                    if (move_uploaded_file($_FILES['blog_image']['tmp_name'], $uploadPath)) {
                        // Delete old image if it exists in uploads folder
                        if (!empty($imageUrl) && strpos($imageUrl, 'uploads/blog-images/') === 0) {
                            $oldImagePath = '../' . $imageUrl;
                            if (file_exists($oldImagePath)) {
                                unlink($oldImagePath);
                            }
                        }
                        $imageUrl = 'uploads/blog-images/' . $newFileName;
                    }
                } elseif (!empty($_POST['image_url']) && $_POST['image_url'] !== $_POST['existing_image_url']) {
                    // Use new URL if provided
                    $imageUrl = mysqli_real_escape_string($conn, $_POST['image_url']);
                }
                
                $sql = "UPDATE blogs SET title = ?, excerpt = ?, content = ?, image_url = ?, 
                        category = ?, author = ?, status = ?, tags = ?, updated_at = CURRENT_TIMESTAMP 
                        WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssssi", $title, $excerpt, $content, $imageUrl, $category, $author, $status, $tags, $id);
                
                if ($stmt->execute()) {
                    $message = 'Blog post updated successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Failed to update blog post.';
                    $messageType = 'danger';
                }
                $stmt->close();
                break;
                
            case 'delete':
                $id = intval($_POST['id']);
                $sql = "DELETE FROM blogs WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id);
                
                if ($stmt->execute()) {
                    $message = 'Blog post deleted successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Failed to delete blog post.';
                    $messageType = 'danger';
                }
                $stmt->close();
                break;
        }
    }
    
    closeDBConnection($conn);
}

// Get all blogs
$conn = getDBConnection();
$sql = "SELECT * FROM blogs ORDER BY created_at DESC";
$result = $conn->query($sql);

$blogs = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $blogs[] = $row;
    }
}

// Calculate statistics
$totalBlogs = count($blogs);
$publishedBlogs = count(array_filter($blogs, function($b) { return $b['status'] === 'published'; }));
$draftBlogs = count(array_filter($blogs, function($b) { return $b['status'] === 'draft'; }));
$totalViews = array_sum(array_column($blogs, 'views'));

closeDBConnection($conn);

function formatDate($dateString) {
    $date = new DateTime($dateString);
    return $date->format('M j, Y');
}

function truncateText($text, $length = 50) {
    // Strip HTML tags first, then truncate
    $text = strip_tags($text);
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . '...';
}

function getImageUrl($imageUrl) {
    // If it's already a full URL (http/https), return as is
    if (strpos($imageUrl, 'http://') === 0 || strpos($imageUrl, 'https://') === 0) {
        return $imageUrl;
    }
    // Otherwise, it's a local path relative to project root
    return '../' . $imageUrl;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Management - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin-style.css">
    
    <!-- Session Protection -->
    <script>
        // Check if user is logged in
        const SESSION_KEY = 'admin_logged_in';
        const SESSION_TIMESTAMP = 'admin_login_time';

        if (sessionStorage.getItem(SESSION_KEY) !== 'true') {
            // Not logged in, redirect to login
            window.location.href = 'login.html';
        } else {
            // Check if session is still valid (24 hours)
            const loginTime = sessionStorage.getItem(SESSION_TIMESTAMP);
            const currentTime = new Date().getTime();
            const hoursPassed = (currentTime - loginTime) / (1000 * 60 * 60);

            if (hoursPassed >= 24) {
                // Session expired
                sessionStorage.removeItem(SESSION_KEY);
                sessionStorage.removeItem(SESSION_TIMESTAMP);
                alert('Your session has expired. Please login again.');
                window.location.href = 'login.html';
            }
        }
    </script>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-blog"></i> Everything Easy</h3>
            <p class="text-muted small">Admin Panel</p>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="dashboard.html">
                    <i class="fas fa-quote-right"></i>
                    <span>Quote Requests</span>
                </a>
            </li>
            <li class="active">
                <a href="blog-management.php">
                    <i class="fas fa-blog"></i>
                    <span>Blog Posts</span>
                    <span class="badge"><?php echo $totalBlogs; ?></span>
                </a>
            </li>
            <li>
                <a href="#" onclick="logout(); return false;">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navigation -->
        <nav class="topnav">
            <div class="topnav-left">
                <button class="toggle-btn" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <h4 class="page-title">Blog Management</h4>
            </div>
            <div class="topnav-right">
                <div class="admin-profile">
                    <img src="https://via.placeholder.com/40" alt="Admin">
                    <span>Admin</span>
                </div>
                <button class="btn btn-sm btn-outline-danger ms-3" onclick="logout()" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </div>
        </nav>

        <!-- Dashboard Content -->
        <div class="dashboard-container">
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="stat-card stat-primary">
                        <div class="stat-icon">
                            <i class="fas fa-blog"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $totalBlogs; ?></h3>
                            <p>Total Posts</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card stat-success">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $publishedBlogs; ?></h3>
                            <p>Published</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card stat-warning">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $draftBlogs; ?></h3>
                            <p>Drafts</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card stat-info">
                        <div class="stat-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $totalViews; ?></h3>
                            <p>Total Views</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Blog Posts Section -->
            <div class="content-card">
                <div class="card-header-flex">
                    <h5><i class="fas fa-list me-2"></i> All Blog Posts</h5>
                    <div class="header-actions">
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#blogModal" onclick="resetForm()">
                            <i class="fas fa-plus"></i> New Post
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Author</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Views</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($blogs) === 0): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No blog posts yet. Create your first post!</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($blogs as $index => $blog): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td>
                                            <img src="<?php echo htmlspecialchars(getImageUrl($blog['image_url'])); ?>" 
                                                 alt="<?php echo htmlspecialchars($blog['title']); ?>" 
                                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;"
                                                 onerror="this.src='https://via.placeholder.com/60x60?text=No+Image'">
                                        </td>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars(strip_tags($blog['title'])); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars(truncateText($blog['excerpt'], 50)); ?></small>
                                        </td>
                                        <td><span class="badge bg-info"><?php echo htmlspecialchars($blog['category']); ?></span></td>
                                        <td><?php echo htmlspecialchars($blog['author']); ?></td>
                                        <td><?php echo formatDate($blog['created_at']); ?></td>
                                        <td>
                                            <span class="status-badge <?php echo $blog['status'] === 'published' ? 'status-success' : 'status-warning'; ?>">
                                                <?php echo ucfirst($blog['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $blog['views']; ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="../blog-detail.php?id=<?php echo $blog['id']; ?>" target="_blank" 
                                                   class="btn-action btn-view" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button class="btn-action btn-edit" title="Edit" 
                                                        onclick='editBlog(<?php echo json_encode($blog); ?>)'>
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form method="POST" style="display: inline;" 
                                                      onsubmit="return confirm('Are you sure you want to delete this blog post?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $blog['id']; ?>">
                                                    <button type="submit" class="btn-action btn-delete" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Blog Modal -->
    <div class="modal fade" id="blogModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form method="POST" id="blogForm" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="blogModalTitle">
                            <i class="fas fa-plus-circle me-2"></i>Add New Blog Post
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" id="formAction" value="create">
                        <input type="hidden" name="id" id="blogId">
                        <input type="hidden" name="existing_image_url" id="existingImageUrl">
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="blogTitle" class="form-label">Title *</label>
                                    <input type="text" class="form-control" id="blogTitle" name="title" required>
                                </div>

                                <div class="mb-3">
                                    <label for="blogExcerpt" class="form-label">Excerpt (Short Description) *</label>
                                    <textarea class="form-control" id="blogExcerpt" name="excerpt" rows="2" required></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="blogContent" class="form-label">Content *</label>
                                    <textarea class="form-control" id="blogContent" name="content" rows="15" required></textarea>
                                    <small class="text-muted">You can use HTML tags for formatting</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Featured Image *</label>
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="blogImageFile" class="form-label">
                                                    <i class="fas fa-upload me-1"></i>Upload Image
                                                </label>
                                                <input type="file" class="form-control" id="blogImageFile" name="blog_image" 
                                                       accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" 
                                                       onchange="previewImage(event)">
                                                <small class="text-muted">Max 5MB (JPG, PNG, GIF, WEBP)</small>
                                            </div>
                                            
                                            <div class="text-center mb-2">
                                                <strong class="text-muted">OR</strong>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="blogImageUrl" class="form-label">
                                                    <i class="fas fa-link me-1"></i>Image URL
                                                </label>
                                                <input type="url" class="form-control" id="blogImageUrl" name="image_url" 
                                                       placeholder="https://example.com/image.jpg"
                                                       onchange="previewUrlImage(event)">
                                            </div>
                                            
                                            <div id="imagePreview" class="mt-3" style="display: none;">
                                                <label class="form-label">Preview:</label>
                                                <img id="previewImg" src="" alt="Preview" 
                                                     class="img-fluid rounded" 
                                                     style="max-height: 200px; width: 100%; object-fit: cover;">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="blogCategory" class="form-label">Category *</label>
                                    <select class="form-select" id="blogCategory" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="Web Development">Web Development</option>
                                        <option value="Mobile Apps">Mobile Apps</option>
                                        <option value="Digital Marketing">Digital Marketing</option>
                                        <option value="SEO">SEO</option>
                                        <option value="Technology">Technology</option>
                                        <option value="Business">Business</option>
                                        <option value="Design">Design</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="blogAuthor" class="form-label">Author *</label>
                                    <input type="text" class="form-control" id="blogAuthor" name="author" value="Admin" required>
                                </div>

                                <div class="mb-3">
                                    <label for="blogStatus" class="form-label">Status *</label>
                                    <select class="form-select" id="blogStatus" name="status" required>
                                        <option value="published">Published</option>
                                        <option value="draft">Draft</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="blogTags" class="form-label">Tags (comma separated)</label>
                                    <input type="text" class="form-control" id="blogTags" name="tags" placeholder="web, design, seo">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Save Post
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function resetForm() {
            document.getElementById('blogForm').reset();
            document.getElementById('formAction').value = 'create';
            document.getElementById('blogId').value = '';
            document.getElementById('existingImageUrl').value = '';
            document.getElementById('blogModalTitle').innerHTML = '<i class="fas fa-plus-circle me-2"></i>Add New Blog Post';
            document.getElementById('imagePreview').style.display = 'none';
            
            // Remove required attribute from file input and URL input for new posts
            document.getElementById('blogImageFile').removeAttribute('required');
            document.getElementById('blogImageUrl').removeAttribute('required');
        }

        function editBlog(blog) {
            document.getElementById('formAction').value = 'update';
            document.getElementById('blogId').value = blog.id;
            document.getElementById('blogTitle').value = blog.title;
            document.getElementById('blogExcerpt').value = blog.excerpt;
            document.getElementById('blogContent').value = blog.content;
            document.getElementById('existingImageUrl').value = blog.image_url;
            document.getElementById('blogImageUrl').value = blog.image_url;
            document.getElementById('blogCategory').value = blog.category;
            document.getElementById('blogAuthor').value = blog.author;
            document.getElementById('blogStatus').value = blog.status;
            document.getElementById('blogTags').value = blog.tags || '';
            document.getElementById('blogModalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Blog Post';
            
            // Show existing image preview
            if (blog.image_url) {
                document.getElementById('previewImg').src = '../' + blog.image_url;
                document.getElementById('imagePreview').style.display = 'block';
            }
            
            new bootstrap.Modal(document.getElementById('blogModal')).show();
        }

        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size too large. Maximum size is 5MB.');
                    event.target.value = '';
                    return;
                }
                
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.');
                    event.target.value = '';
                    return;
                }
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('imagePreview').style.display = 'block';
                }
                reader.readAsDataURL(file);
                
                // Clear URL input when file is selected
                document.getElementById('blogImageUrl').value = '';
            }
        }

        function previewUrlImage(event) {
            const url = event.target.value;
            if (url) {
                document.getElementById('previewImg').src = url;
                document.getElementById('imagePreview').style.display = 'block';
                
                // Clear file input when URL is entered
                document.getElementById('blogImageFile').value = '';
            }
        }

        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                sessionStorage.removeItem('admin_logged_in');
                sessionStorage.removeItem('admin_login_time');
                window.location.href = 'login.html';
            }
        }

        // Toggle sidebar
        document.getElementById('toggleSidebar')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('collapsed');
        });
        
        // Form validation before submit
        document.getElementById('blogForm').addEventListener('submit', function(e) {
            const fileInput = document.getElementById('blogImageFile');
            const urlInput = document.getElementById('blogImageUrl');
            const existingUrl = document.getElementById('existingImageUrl');
            
            // For new posts (create), require either file or URL
            if (document.getElementById('formAction').value === 'create') {
                if (!fileInput.files.length && !urlInput.value) {
                    e.preventDefault();
                    alert('Please upload an image or provide an image URL.');
                    return false;
                }
            }
        });
    </script>
</body>

</html>
