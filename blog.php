<?php
require_once 'backend/config.php';

// Get all published blogs
$conn = getDBConnection();
$sql = "SELECT * FROM blogs WHERE status = 'published' ORDER BY created_at DESC";
$result = $conn->query($sql);

$blogs = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $blogs[] = $row;
    }
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
  <title>Blog - EverythingEasy Technology</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet" />
  <link href="css/style.css" rel="stylesheet" />
  <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Blog",
  "name": "EverythingEasy Blog",
  "url": "https://everythingeasy.in/blog",
  "publisher": {
    "@type": "Organization",
    "name": "EverythingEasy",
    "logo": {
      "@type": "ImageObject",
      "url": "https://everythingeasy.in/assets/logo.png"
    }
  }
}
</script>


</head>

<body>
  <?php require_once 'navbar.php'; ?>
  <!-- Navigation Container -->

  <!-- Blog Header -->
  <section class="page-header bg-gradient-primary text-white py-5" style="padding-top: 120px !important">
    <div class="container">
      <div class="row">
        <div class="col-12 text-center">
          <h1 class="display-4 fw-bold mb-3">Our Blog</h1>
          <p class="lead mb-4">
            Stay updated with the latest insights, trends, and tips in IT and
            digital solutions
          </p>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center bg-transparent">
              <li class="breadcrumb-item">
                <a href="index.html" class="text-warning">Home</a>
              </li>
              <li class="breadcrumb-item active text-white" aria-current="page">
                Blog
              </li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </section>

  <!-- Blog Posts Section -->
  <section class="py-5">
    <div class="container">
      <div class="row" id="blogPostsContainer">
        <?php if (count($blogs) === 0): ?>
          <div class="col-12 text-center py-5">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <p class="text-muted">No blog posts available yet. Check back soon!</p>
          </div>
        <?php else: ?>
          <?php foreach ($blogs as $blog): ?>
            <div class="col-lg-4 col-md-6 mb-4">
              <div class="blog-card h-100">
                <div class="blog-image">
                  <img src="<?php echo htmlspecialchars(getImageUrl($blog['image_url'])); ?>" 
                       alt="<?php echo htmlspecialchars($blog['title']); ?>" 
                       class="img-fluid rounded-top" 
                       style="height: 250px; object-fit: cover; width: 100%;" />
                </div>
                <div class="blog-content p-4">
                  <div class="blog-meta mb-3">
                    <span class="text-muted">
                      <i class="fas fa-calendar-alt me-2"></i><?php echo formatDate($blog['created_at']); ?>
                    </span>
                    <span class="text-muted ms-3">
                      <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($blog['author']); ?>
                    </span>
                  </div>
                  <h4 class="blog-title fw-bold mb-3">
                    <a href="blog-detail.php?id=<?php echo $blog['id']; ?>" class="text-dark">
                      <?php echo htmlspecialchars($blog['title']); ?>
                    </a>
                  </h4>
                  <p class="text-muted mb-3"><?php echo htmlspecialchars($blog['excerpt']); ?></p>
                  <div class="d-flex justify-content-between align-items-center">
                    <a href="blog-detail.php?id=<?php echo $blog['id']; ?>" class="btn btn-outline-primary">
                      Read More <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                    <?php if ($blog['views'] > 0): ?>
                      <small class="text-muted">
                        <i class="fas fa-eye me-1"></i><?php echo $blog['views']; ?> views
                      </small>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <?php require_once 'footer.php'; ?>

  <!-- Scripts -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
  <script src="js/script.js"></script>
</body>

</html>
