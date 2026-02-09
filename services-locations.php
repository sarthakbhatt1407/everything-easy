<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes" />
  <meta name="mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-status-bar-style" content="default" />
  <meta name="theme-color" content="#0066cc" />
  <title>IT Services Across India - Everything Easy</title>
  <meta name="description"
    content="Professional IT services including web development, app development, digital marketing, SEO, and e-commerce solutions across all major cities in India - Delhi, Mumbai, Bangalore, Dehradun, Rishikesh, and more." />
  <meta name="keywords"
    content="IT services India, web development Delhi, app development Mumbai, SEO services Bangalore, digital marketing Dehradun, e-commerce Rishikesh" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet" />
  <link href="css/style.css" rel="stylesheet" />
  <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
  <link rel="manifest" href="site.webmanifest">
</head>

<body>
  <!-- Navigation Container -->
  <?php require_once 'navbar.php'; ?>

  <!-- Page Header -->
  <section class="py-5 bg-gradient-primary text-white" style="padding-top: 120px !important">
    <div class="container">
      <div class="row">
        <div class="col-12 text-center">
          <h1 class="display-4 fw-bold mb-3">Our Services Locations</h1>
          <p class="lead mb-4">
            Professional IT solutions across all major cities in India
          </p>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center bg-transparent">
              <li class="breadcrumb-item">
                <a href="/" class="text-warning">Home</a>
              </li>
              <li class="breadcrumb-item active text-white" aria-current="page">
                Services Locations
              </li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </section>

  <!-- Web Development Services by Location -->
  <section class="py-5">
    <div class="container">
      <div class="row mb-5">
        <div class="col-lg-8 mx-auto text-center">
          <h2 class="fw-bold mb-3">
            <i class="fas fa-globe text-primary me-3"></i>Web Development Services
          </h2>
          <p class="text-muted">
            Professional website development across India
          </p>
        </div>
      </div>

      <?php
      require_once 'backend/config.php';
      $conn = getDBConnection();

      // Fetch all locations
      $locations = mysqli_query($conn, "SELECT location_name, city_name , slug FROM locations ORDER BY id ASC");
      ?>

      <div class="row">
        <?php while ($loc = mysqli_fetch_assoc($locations)) { ?>
          <div class="col-lg-4 col-md-6 mb-4">
            <div class="service-card h-100 p-4 bg-white rounded shadow-sm">
              <h5 class="fw-bold mb-3">
                <a style="color: black; text-decoration: none;" href="/it-services/<?php echo $loc['slug']; ?>">
                  <i class="fas fa-check-circle text-success me-2"></i>
                  <?php echo $loc['meta_title']; ?>
                </a>
              </h5>
            </div>
          </div>
        <?php } ?>
      </div>

    </div>
  </section>


  <!-- CTA Section -->
  <section class="py-5 bg-gradient-primary text-white">
    <div class="container">
      <div class="row">
        <div class="col-lg-8 mx-auto text-center">
          <h2 class="fw-bold mb-4">Ready to Transform Your Business?</h2>
          <p class="lead mb-4">
            Contact us today for professional IT services in your city. We
            serve clients across India with cutting-edge technology solutions.
          </p>
          <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="tel:+918630840577" class="btn btn-warning btn-lg">
              <i class="fas fa-phone me-2"></i>Call Now: +91 86308 40577
            </a>
            <a href="contact.html" class="btn btn-outline-light btn-lg">
              <i class="fas fa-envelope me-2"></i>Get Free Quote
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer Container -->
  <?php require_once 'footer.php'; ?>

  <!-- Scripts -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
  <script src="js/script.js"></script>
</body>

</html>