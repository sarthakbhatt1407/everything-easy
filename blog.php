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

  function getBlogUrl($blog) {
    if (!empty($blog['slug'])) {
      return 'blog/' . rawurlencode($blog['slug']);
    }
    return 'blog-detail.php?id=' . (int)$blog['id'];
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
   <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
  <link rel="manifest" href="site.webmanifest">
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
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
  <div class="container">
    <a class="navbar-brand fw-bold text-primary" href="/">
      <img
        src="/images/logo.webp"
        alt="Everything Easy Logo"
        class="navbar-logo"
      />
      EverythingEasy
    </a>
    <button
      class="navbar-toggler"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#navbarNav"
    >
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="/">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>" href="/about">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'services.php' || basename($_SERVER['PHP_SELF']) == 'it-services.php' ? 'active' : ''; ?>" href="/services">Services</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'portfolio.php' ? 'active' : ''; ?>" href="/portfolio">Portfolio</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'blog.php' || basename($_SERVER['PHP_SELF']) == 'blog-detail.php' ? 'active' : ''; ?>" href="/blog">Blog</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>" href="/contact">Contact</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'career.php' ? 'active' : ''; ?>" href="/career">Career</a>
        </li>
        <li class="nav-item">
          <a class="btn btn-primary ms-2" href="/#quote">Get Quote</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

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
                    <a href="<?php echo htmlspecialchars(getBlogUrl($blog)); ?>" class="text-dark">
                      <?php echo htmlspecialchars($blog['title']); ?>
                    </a>
                  </h4>
                  <p class="text-muted mb-3"><?php echo htmlspecialchars($blog['excerpt']); ?></p>
                  <div class="d-flex justify-content-between align-items-center">
                    <a href="<?php echo htmlspecialchars(getBlogUrl($blog)); ?>" class="btn btn-outline-primary">
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

<!-- Footer -->
<footer class="bg-dark text-white py-5">
  <div class="container">
    <div class="row">
      <div class="col-lg-4 mb-4">
        <div class="footer-about">
<!--        
              <image
        src="https://i.ibb.co/hJRZz403/elogo-removebg-preview.png"
        alt="Everything Easy Logo"
        style="height: 40px; margin-right: -8px"
      /> -->
      EverythingEasy Technology

          <p class="text-muted mb-4">
            Leading IT solutions company providing innovative technology
            services to help businesses grow digitally.
          </p>
          <div class="social-links mb-5">
            <a
              href="https://www.facebook.com/profile.php?id=61575148140871"
              target="_blank"
              class="facebook"
              ><i class="fab fa-facebook fa-lg"></i
            ></a>
            <a
              href="https://x.com/Everythingeasy0"
              target="_blank"
              class="twitter"
              ><i class="fab fa-twitter fa-lg"></i
            ></a>
            <a
              href="https://www.linkedin.com/company/everythingeasy/"
              target="_blank"
              class="linkedin"
              ><i class="fab fa-linkedin fa-lg"></i
            ></a>
            <a
              href="https://www.instagram.com/everythingeasy0/"
              target="_blank"
              class="instagram"
              ><i class="fab fa-instagram fa-lg"></i
            ></a>
          </div>
          <!-- Trustpilot Widget -->
          <div class="trustpilot-widget mt-3" data-locale="en-US" data-template-id="56278e9abfbbba0bdcd568bc" data-businessunit-id="693706f7f444175f88990f6c" data-style-height="52px" data-style-width="100%" data-token="448d2ef3-edd9-486d-8d48-04a5d3ac55b6">
            <a href="https://www.trustpilot.com/review/everythingeasy.in" target="_blank" rel="noopener">Trustpilot</a>
          </div>
        </div>
      </div>
      <div class="col-lg-2 col-md-6 mb-4">
        <div class="footer-links">
          <h6 class="fw-bold mb-3">Quick Links</h6>
          <ul class="list-unstyled">
            <li><a href="/" class="text-muted">Home</a></li>
            <li><a href="/about" class="text-muted">About Us</a></li>
            <li>
              <a href="/services" class="text-muted">Our Services</a>
            </li>
            <li><a href="/portfolio" class="text-muted">Portfolio</a></li>
            <li><a href="/blog" class="text-muted">Blog</a></li>
            <li><a href="/contact" class="text-muted">Contact Us</a></li>
            <li><a href="/career" class="text-muted">Careers</a></li>
          </ul>
        </div>
      </div>
      <div class="col-lg-2 col-md-6 mb-4">
        <div class="footer-links">
          <h6 class="fw-bold mb-3">Services</h6>
          <ul class="list-unstyled">
            <li><a href="#" class="text-muted">Digital Marketing</a></li>
            <li><a href="#" class="text-muted">E-commerce</a></li>
            <li><a href="#" class="text-muted">Web Development</a></li>
            <li><a href="#" class="text-muted">App Development</a></li>
            <li><a href="#" class="text-muted">SEO Optimization</a></li>
            <li><a href="#" class="text-muted">Custom Software</a></li>
            <li>
              <a href="services-locations.php" class="text-muted"
                >Services location</a
              >
            </li>
          </ul>
        </div>
      </div>
      <div class="col-lg-4 mb-4">
        <div class="footer-contact">
          <h6 class="fw-bold mb-3">Get In Touch</h6>
          <div class="contact-item d-flex mb-2">
            <a
              href="https://www.google.com/maps/dir//Bhagwandas+chowk,+Bisht+colony,+ghoda+factory+road,+Balawala,+Dehradun,+Uttarakhand+248019/@30.2657584,78.1273607,13.15z/data=!4m8!4m7!1m0!1m5!1m1!1s0x3909256beff80bed:0x5b9bdb3eed18518d!2m2!1d78.1228234!2d30.2612601?entry=ttu&g_ep=EgoyMDI1MTIwMi4wIKXMDSoASAFQAw%3D%3D"
              target="_blank"
              class="text-muted text-decoration-none"
            >
              <i class="fas fa-map-marker-alt me-2 mt-1"></i>EverythingEasy
              Technology Balawala, Dehradun 248001 Uttarakhand, India</a
            >
          </div>
          <div class="contact-item d-flex mb-2">
            <i class="fas fa-envelope me-2 mt-1"></i>
            <a
              href="mailto:info@everythingeasy.com"
              class="text-muted text-decoration-none"
              >info@everythingeasy.com</a
            >
          </div>
          <div class="contact-item d-flex mb-3">
            <i class="fas fa-phone me-2 mt-1"></i>
            <a href="tel:+918630840577" class="text-muted text-decoration-none"
              >+91 86308 40577</a
            >
          </div>
        </div>
      </div>
    </div>
    <hr class="my-4" />
    <div class="row align-items-center">
      <div class="col-md-6">
        <p class="text-muted mb-0">
          &copy; <?php echo date('Y'); ?> EverythingEasy Technology. All
          Rights Reserved.
        </p>
      </div>
      <div class="col-md-6 text-md-end">
        <p class="text-muted mb-0">
          Designed with <i class="fas fa-heart text-danger"></i> by Everything
          Easy Team
        </p>
      </div>
    </div>
    <!-- TrustBox widget - Review Collector -->
<!-- TrustBox script -->
 <!-- TrustBox widget - Review Collector -->

<!-- End TrustBox widget -->
<script type="text/javascript" src="//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js" async></script>
<!-- End TrustBox script -->
</div>
<!-- End TrustBox widget -->
  </div>
</footer>
<!-- TrustBox script -->

<!-- End TrustBox script -->


  <!-- Scripts -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
  <script src="js/script.js"></script>
</body>

</html>
