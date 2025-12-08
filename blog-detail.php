<?php
require_once 'backend/config.php';

// Get blog ID from URL
$blogId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($blogId === 0) {
    header('Location: blog.php');
    exit;
}

// Get blog post
$conn = getDBConnection();

// Increment view count
$updateSql = "UPDATE blogs SET views = views + 1 WHERE id = ?";
$updateStmt = $conn->prepare($updateSql);
$updateStmt->bind_param("i", $blogId);
$updateStmt->execute();
$updateStmt->close();

// Get blog details
$sql = "SELECT * FROM blogs WHERE id = ? AND status = 'published'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $blogId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $blogNotFound = true;
    $blog = null;
} else {
    $blogNotFound = false;
    $blog = $result->fetch_assoc();
}
$stmt->close();

// Get related posts
$relatedBlogs = [];
if ($blog) {
    $relatedSql = "SELECT * FROM blogs WHERE id != ? AND category = ? AND status = 'published' ORDER BY created_at DESC LIMIT 2";
    $relatedStmt = $conn->prepare($relatedSql);
    $relatedStmt->bind_param("is", $blogId, $blog['category']);
    $relatedStmt->execute();
    $relatedResult = $relatedStmt->get_result();
    
    while ($row = $relatedResult->fetch_assoc()) {
        $relatedBlogs[] = $row;
    }
    $relatedStmt->close();
}

closeDBConnection($conn);

function formatDate($dateString) {
    $date = new DateTime($dateString);
    return $date->format('F j, Y');
}

function getImageUrl($imageUrl) {
    // If it's already a full URL (http/https), return as is
    if (strpos($imageUrl, 'http://') === 0 || strpos($imageUrl, 'https://') === 0) {
        return $imageUrl;
    }
    // Otherwise, it's a local path, return as is (relative path)
    return $imageUrl;
}

function getCurrentUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    return $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes" />
  <meta name="mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-status-bar-style" content="default" />
  <meta name="theme-color" content="#0066cc" />
  <title><?php echo $blog ? htmlspecialchars($blog['title']) . ' - ' : ''; ?>EverythingEasy Technology</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet" />
  <link href="css/style.css" rel="stylesheet" />
</head>

<body>
  <div id="navbar-container"></div>
  <script src="js/navbar-loader.js"></script>
  <!-- Navigation Container -->

  <!-- Blog Detail Section -->
  <section class="py-5" style="padding-top: 120px !important">
    <div class="container">
      <div class="row">
        <div class="col-lg-10 mx-auto">
          <!-- Blog Header -->
          <div class="blog-detail-header mb-4">
            <a href="blog.php" class="text-primary mb-3 d-inline-block">
              <i class="fas fa-arrow-left me-2"></i>Back to Blog
            </a>
            
            <?php if ($blogNotFound): ?>
              <h1 class="display-5 fw-bold mb-3">Blog Post Not Found</h1>
              <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Sorry, this blog post could not be found or has been removed.
                <a href="blog.php" class="alert-link">Go back to blog</a>
              </div>
            <?php else: ?>
              <h1 class="display-5 fw-bold mb-3"><?php echo htmlspecialchars($blog['title']); ?></h1>
              <div class="blog-meta mb-4">
                <span class="text-muted me-3">
                  <i class="fas fa-calendar-alt me-2"></i><?php echo formatDate($blog['created_at']); ?>
                </span>
                <span class="text-muted me-3">
                  <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($blog['author']); ?>
                </span>
                <span class="text-muted">
                  <i class="fas fa-clock me-2"></i>5 min read
                </span>
              </div>
            <?php endif; ?>
          </div>

          <?php if (!$blogNotFound): ?>
            <!-- Featured Image -->
            <div class="blog-featured-image mb-5">
              <img src="<?php echo htmlspecialchars(getImageUrl($blog['image_url'])); ?>" 
                   alt="<?php echo htmlspecialchars($blog['title']); ?>" 
                   class="img-fluid rounded shadow" />
            </div>

            <!-- Blog Content -->
            <div class="blog-detail-content" id="blog-content">
              <p class="lead mb-4"><?php echo htmlspecialchars($blog['excerpt']); ?></p>
              
              <div class="mb-4">
                <span class="badge bg-primary me-2"><?php echo htmlspecialchars($blog['category']); ?></span>
                <?php if (!empty($blog['tags'])): ?>
                  <?php $tags = explode(',', $blog['tags']); ?>
                  <?php foreach ($tags as $tag): ?>
                    <span class="badge bg-secondary me-2"><?php echo htmlspecialchars(trim($tag)); ?></span>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
              
              <div class="blog-body">
                <?php echo $blog['content']; ?>
              </div>
            </div>

            <!-- Share Section -->
            <div class="blog-share mt-5 pt-4 border-top">
              <h5 class="fw-bold mb-3">Share this article:</h5>
              <div class="share-buttons">
                <?php 
                $currentUrl = urlencode(getCurrentUrl());
                $title = urlencode($blog['title']);
                ?>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $currentUrl; ?>" 
                   target="_blank" class="btn btn-primary me-2 mb-2">
                  <i class="fab fa-facebook-f me-2"></i>Facebook
                </a>
                <a href="https://twitter.com/intent/tweet?url=<?php echo $currentUrl; ?>&text=<?php echo $title; ?>" 
                   target="_blank" class="btn btn-info text-white me-2 mb-2">
                  <i class="fab fa-twitter me-2"></i>Twitter
                </a>
                <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $currentUrl; ?>&title=<?php echo $title; ?>" 
                   target="_blank" class="btn btn-primary me-2 mb-2">
                  <i class="fab fa-linkedin-in me-2"></i>LinkedIn
                </a>
                <a href="https://api.whatsapp.com/send?text=<?php echo $title; ?>%20<?php echo $currentUrl; ?>" 
                   target="_blank" class="btn btn-success me-2 mb-2">
                  <i class="fab fa-whatsapp me-2"></i>WhatsApp
                </a>
              </div>
            </div>

            <!-- Related Posts -->
            <?php if (count($relatedBlogs) > 0): ?>
              <div class="related-posts mt-5 pt-4 border-top">
                <h4 class="fw-bold mb-4">Related Articles</h4>
                <div class="row">
                  <?php foreach ($relatedBlogs as $relatedBlog): ?>
                    <div class="col-md-6 mb-4">
                      <div class="blog-card">
                        <img src="<?php echo htmlspecialchars(getImageUrl($relatedBlog['image_url'])); ?>" 
                             alt="<?php echo htmlspecialchars($relatedBlog['title']); ?>" 
                             class="img-fluid rounded-top" 
                             style="height: 200px; object-fit: cover; width: 100%;" />
                        <div class="p-3">
                          <h6 class="fw-bold mb-2">
                            <a href="blog-detail.php?id=<?php echo $relatedBlog['id']; ?>" class="text-dark">
                              <?php echo htmlspecialchars($relatedBlog['title']); ?>
                            </a>
                          </h6>
                          <p class="text-muted small mb-0"><?php echo formatDate($relatedBlog['created_at']); ?></p>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <div id="footer-container"></div>

  <!-- Scripts -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
  <script src="js/script.js"></script>
  <script src="js/footer-loader.js"></script>
</body>

</html>
