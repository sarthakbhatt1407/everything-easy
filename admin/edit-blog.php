<?php
require_once '../backend/config.php';

$message = '';
$messageType = '';

$blogId = 0;
if (isset($_GET['id'])) {
    $blogId = intval($_GET['id']);
} elseif (isset($_POST['id'])) {
    $blogId = intval($_POST['id']);
}

if ($blogId <= 0) {
    header('Location: blog-management.php');
    exit;
}

$conn = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawTitle = trim($_POST['title']);
    $title = mysqli_real_escape_string($conn, $rawTitle);
    $excerpt = mysqli_real_escape_string($conn, $_POST['excerpt']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $tags = mysqli_real_escape_string($conn, $_POST['tags']);
    $metaDescription = mysqli_real_escape_string($conn, $_POST['meta_description']);
    $metaKeywords = mysqli_real_escape_string($conn, $_POST['meta_keywords']);

    $slug = generateSlug($rawTitle);
    if (empty($slug)) {
        $message = 'Unable to generate slug from this title. Please use a different title.';
        $messageType = 'danger';
    } else {
        $slugCheckSql = "SELECT id FROM blogs WHERE slug = ? AND id != ? LIMIT 1";
        $slugCheckStmt = $conn->prepare($slugCheckSql);
        $slugCheckStmt->bind_param('si', $slug, $blogId);
        $slugCheckStmt->execute();
        $slugCheckResult = $slugCheckStmt->get_result();

        if ($slugCheckResult->num_rows > 0) {
            $message = 'This title is already in use.';
            $messageType = 'danger';
        }
        $slugCheckStmt->close();
    }

    if (empty($message)) {
        $imageUrl = mysqli_real_escape_string($conn, $_POST['existing_image_url']);

        if (isset($_FILES['blog_image']) && $_FILES['blog_image']['error'] === 0) {
            $uploadDir = '../uploads/blog-images/';
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $maxSize = 5 * 1024 * 1024;

            $fileType = $_FILES['blog_image']['type'];
            $fileSize = $_FILES['blog_image']['size'];

            if (!in_array($fileType, $allowedTypes, true)) {
                $message = 'Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.';
                $messageType = 'danger';
            } elseif ($fileSize > $maxSize) {
                $message = 'File size too large. Maximum size is 5MB.';
                $messageType = 'danger';
            } else {
                $fileExtension = pathinfo($_FILES['blog_image']['name'], PATHINFO_EXTENSION);
                $newFileName = 'blog_' . time() . '_' . uniqid() . '.' . $fileExtension;
                $uploadPath = $uploadDir . $newFileName;

                if (move_uploaded_file($_FILES['blog_image']['tmp_name'], $uploadPath)) {
                    if (!empty($imageUrl) && strpos($imageUrl, 'uploads/blog-images/') === 0) {
                        $oldImagePath = '../' . $imageUrl;
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                    $imageUrl = 'uploads/blog-images/' . $newFileName;
                } else {
                    $message = 'Failed to upload image.';
                    $messageType = 'danger';
                }
            }
        } elseif (!empty($_POST['image_url']) && $_POST['image_url'] !== $_POST['existing_image_url']) {
            $imageUrl = mysqli_real_escape_string($conn, $_POST['image_url']);
        }
    }

    if (empty($message)) {
        $sql = "UPDATE blogs SET
                title = ?,
                slug = ?,
                excerpt = ?,
                content = ?,
                image_url = ?,
                category = ?,
                author = ?,
                status = ?,
                tags = ?,
                meta_description = ?,
                meta_keywords = ?,
                updated_at = CURRENT_TIMESTAMP
                WHERE id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            'sssssssssssi',
            $title,
            $slug,
            $excerpt,
            $content,
            $imageUrl,
            $category,
            $author,
            $status,
            $tags,
            $metaDescription,
            $metaKeywords,
            $blogId
        );

        if ($stmt->execute()) {
            $message = 'Blog post updated successfully!';
            $messageType = 'success';
        } else {
            $message = 'Failed to update blog post.';
            $messageType = 'danger';
        }
        $stmt->close();
    }
}

$blog = null;
$blogSql = 'SELECT * FROM blogs WHERE id = ? LIMIT 1';
$blogStmt = $conn->prepare($blogSql);
$blogStmt->bind_param('i', $blogId);
$blogStmt->execute();
$blogResult = $blogStmt->get_result();

if ($blogResult->num_rows > 0) {
    $blog = $blogResult->fetch_assoc();
}
$blogStmt->close();

closeDBConnection($conn);

if (!$blog) {
    header('Location: blog-management.php');
    exit;
}

function getImageUrl($imageUrl) {
    if (strpos($imageUrl, 'http://') === 0 || strpos($imageUrl, 'https://') === 0) {
        return $imageUrl;
    }
    return '../' . $imageUrl;
}

function generateSlug($title) {
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    $slug = preg_replace('/[\s-]+/', '-', $slug);
    return trim($slug, '-');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin-style.css">

    <script>
        const SESSION_KEY = 'admin_logged_in';
        const SESSION_TIMESTAMP = 'admin_login_time';

        if (sessionStorage.getItem(SESSION_KEY) !== 'true') {
            window.location.href = 'login.html';
        } else {
            const loginTime = sessionStorage.getItem(SESSION_TIMESTAMP);
            const currentTime = new Date().getTime();
            const hoursPassed = (currentTime - loginTime) / (1000 * 60 * 60);

            if (hoursPassed >= 24) {
                sessionStorage.removeItem(SESSION_KEY);
                sessionStorage.removeItem(SESSION_TIMESTAMP);
                alert('Your session has expired. Please login again.');
                window.location.href = 'login.html';
            }
        }
    </script>
</head>

<body>
    <div class="main-content" style="margin-left: 0; width: 100%; min-height: 100vh;">
        <nav class="topnav">
            <div class="topnav-left">
                <a href="blog-management.php" class="btn btn-sm btn-outline-primary me-3">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
                <h4 class="page-title">Edit Blog Post</h4>
            </div>
            <div class="topnav-right">
                <button class="btn btn-sm btn-outline-danger" onclick="logout()" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </div>
        </nav>

        <div class="dashboard-container">
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="content-card">
                <div class="card-header-flex">
                    <h5><i class="fas fa-edit me-2"></i> Edit: <?php echo htmlspecialchars($blog['title']); ?></h5>
                </div>

                <form method="POST" id="editBlogForm" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo (int)$blog['id']; ?>">
                    <input type="hidden" name="existing_image_url" id="existingImageUrl" value="<?php echo htmlspecialchars($blog['image_url']); ?>">

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="blogTitle" class="form-label">Title *</label>
                                <input type="text" class="form-control" id="blogTitle" name="title" value="<?php echo htmlspecialchars($blog['title']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="blogExcerpt" class="form-label">Excerpt (Short Description) *</label>
                                <textarea class="form-control" id="blogExcerpt" name="excerpt" rows="2" required><?php echo htmlspecialchars($blog['excerpt']); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="blogContent" class="form-label">Content *</label>
                                <textarea class="form-control" id="blogContent" name="content" rows="15" required><?php echo htmlspecialchars($blog['content']); ?></textarea>
                                <small class="text-muted">Use the editor toolbar for Word-like formatting.</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Featured Image</label>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="blogImageFile" class="form-label">
                                                <i class="fas fa-upload me-1"></i>Upload New Image
                                            </label>
                                            <input type="file" class="form-control" id="blogImageFile" name="blog_image" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" onchange="previewImage(event)">
                                        </div>

                                        <div class="text-center mb-2">
                                            <strong class="text-muted">OR</strong>
                                        </div>

                                        <div class="mb-3">
                                            <label for="blogImageUrl" class="form-label">
                                                <i class="fas fa-link me-1"></i>Image URL
                                            </label>
                                            <input type="url" class="form-control" id="blogImageUrl" name="image_url" value="<?php echo htmlspecialchars($blog['image_url']); ?>" onchange="previewUrlImage(event)">
                                        </div>

                                        <div id="imagePreview" class="mt-3">
                                            <label class="form-label">Preview:</label>
                                            <img id="previewImg" src="<?php echo htmlspecialchars(getImageUrl($blog['image_url'])); ?>" alt="Preview" class="img-fluid rounded" style="max-height: 200px; width: 100%; object-fit: cover;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="blogCategory" class="form-label">Category *</label>
                                <select class="form-select" id="blogCategory" name="category" required>
                                    <option value="">Select Category</option>
                                    <?php
                                    $categories = ['Web Development', 'Mobile Apps', 'Digital Marketing', 'SEO', 'Technology', 'Business', 'Design'];
                                    foreach ($categories as $category):
                                    ?>
                                        <option value="<?php echo htmlspecialchars($category); ?>" <?php echo $blog['category'] === $category ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="blogAuthor" class="form-label">Author *</label>
                                <input type="text" class="form-control" id="blogAuthor" name="author" value="<?php echo htmlspecialchars($blog['author']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="blogStatus" class="form-label">Status *</label>
                                <select class="form-select" id="blogStatus" name="status" required>
                                    <option value="published" <?php echo $blog['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
                                    <option value="draft" <?php echo $blog['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="blogTags" class="form-label">Tags (comma separated)</label>
                                <input type="text" class="form-control" id="blogTags" name="tags" value="<?php echo htmlspecialchars($blog['tags'] ?? ''); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="blogMetaDescription" class="form-label">Meta Description</label>
                                <textarea class="form-control" id="blogMetaDescription" name="meta_description" rows="3"><?php echo htmlspecialchars($blog['meta_description'] ?? ''); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="blogMetaKeywords" class="form-label">Meta Keywords</label>
                                <input type="text" class="form-control" id="blogMetaKeywords" name="meta_keywords" value="<?php echo htmlspecialchars($blog['meta_keywords'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="blog-management.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Post
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script>
        let blogContentEditor = null;

        document.addEventListener('DOMContentLoaded', function() {
            ClassicEditor
                .create(document.querySelector('#blogContent'), {
                    toolbar: [
                        'heading', '|',
                        'bold', 'italic', 'underline', '|',
                        'link', 'bulletedList', 'numberedList', '|',
                        'blockQuote', 'insertTable', '|',
                        'undo', 'redo'
                    ],
                    table: {
                        contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
                    }
                })
                .then(function(editor) {
                    blogContentEditor = editor;
                })
                .catch(function(error) {
                    console.error('Failed to initialize content editor:', error);
                });
        });

        document.getElementById('editBlogForm').addEventListener('submit', function() {
            if (blogContentEditor) {
                document.getElementById('blogContent').value = blogContentEditor.getData();
            }
        });

        function previewImage(event) {
            const file = event.target.files[0];
            if (!file) {
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                alert('File size too large. Maximum size is 5MB.');
                event.target.value = '';
                return;
            }

            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                alert('Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.');
                event.target.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImg').src = e.target.result;
                document.getElementById('imagePreview').style.display = 'block';
            };
            reader.readAsDataURL(file);

            document.getElementById('blogImageUrl').value = '';
        }

        function previewUrlImage(event) {
            const url = event.target.value;
            if (!url) {
                return;
            }

            document.getElementById('previewImg').src = url;
            document.getElementById('imagePreview').style.display = 'block';
            document.getElementById('blogImageFile').value = '';
        }

        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                sessionStorage.removeItem('admin_logged_in');
                sessionStorage.removeItem('admin_login_time');
                window.location.href = 'login.html';
            }
        }
    </script>
</body>

</html>
