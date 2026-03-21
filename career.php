<?php
require_once 'backend/config.php';

$formSuccess = false;
$formMessage = '';

$formData = [
  'fullName' => '',
  'email' => '',
  'phone' => '',
  'position' => '',
  'experience' => '',
  'portfolio' => '',
  'coverLetter' => ''
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  foreach ($formData as $key => $value) {
    $formData[$key] = trim($_POST[$key] ?? '');
  }

  // Check if resume exists
  if (!isset($_FILES['resume']) || $_FILES['resume']['error'] === UPLOAD_ERR_NO_FILE) {
    $formMessage = 'Resume file is required';
  }

  if ($formMessage === '') {
    // Validate required fields
    $required = ['fullName', 'email', 'phone', 'position', 'experience', 'coverLetter'];
    foreach ($required as $field) {
      if (empty($_POST[$field])) {
        $formMessage = ucfirst(str_replace('_', ' ', $field)) . ' is required';
        break;
      }
        }
    }

  // Validate email
  if ($formMessage === '' && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $formMessage = 'Invalid email format';
    }

    // Prepare inputs
  $fullName = $formData['fullName'];
  $email = $formData['email'];
  $phone = $formData['phone'];
  $position = $formData['position'];
    $experience = (int) $_POST['experience'];
  $portfolio = $formData['portfolio'];
  $coverLetter = $formData['coverLetter'];

    // Validate and upload resume
  if ($formMessage === '') {
    $resume = $_FILES['resume'];
    $allowed_ext = ['pdf'];
    $file_ext = strtolower(pathinfo($resume['name'], PATHINFO_EXTENSION));

    if (!in_array($file_ext, $allowed_ext)) {
      $formMessage = 'Only PDF files are allowed';
    } elseif ($resume['size'] > 5 * 1024 * 1024) {
      $formMessage = 'File size too large. Maximum is 5MB';
    } else {
      // Create upload directory
      $uploadDir = __DIR__ . '/upload/';
      if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
      }

      // Upload file
      $fileName = uniqid('RESUME_') . '.' . $file_ext;
      $uploadPath = $uploadDir . $fileName;
      $dbFilePath = 'upload/' . $fileName;

      if (!move_uploaded_file($resume['tmp_name'], $uploadPath)) {
        $formMessage = 'Failed to upload resume';
      } else {
        // Insert into database
        $conn = getDBConnection();
        $sql = "INSERT INTO job_applications 
            (full_name, email, phone, position, experience, portfolio, cover_letter, resume_path) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
          $formMessage = 'Database error occurred';
        } else {
          $stmt->bind_param("ssssisss", $fullName, $email, $phone, $position, $experience, $portfolio, $coverLetter, $dbFilePath);

          if ($stmt->execute()) {
            // Send thank you email
            $subject = 'Application Received - Everything Easy Technology';
            $emailBody = "Hi $fullName,\n\n";
            $emailBody .= "Thank you for applying for the position of $position at Everything Easy Technology!\n\n";
            $emailBody .= "We have received your application and will review it shortly. We appreciate your interest in joining our team.\n\n";
            $emailBody .= "If your profile matches our requirements, our HR team will contact you within 5-7 business days.\n\n";
            $emailBody .= "Best regards,\n";
            $emailBody .= "Everything Easy Technology Team\n";
            $emailBody .= "Email: info@everythingeasy.in\n";
            $emailBody .= "Phone: +91-8630840577\n";

            $headers = "From: info@everythingeasy.in\r\n";
            $headers .= "Reply-To: info@everythingeasy.in\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

            @mail($email, $subject, $emailBody, $headers);

            // Also send notification to admin
            $adminSubject = 'New Job Application - ' . $position;
            $adminBody = "New application received:\n\n";
            $adminBody .= "Name: $fullName\n";
            $adminBody .= "Email: $email\n";
            $adminBody .= "Phone: $phone\n";
            $adminBody .= "Position: $position\n";
            $adminBody .= "Experience: $experience years\n";
            $adminBody .= "Portfolio: $portfolio\n";
            $adminBody .= "Cover Letter:\n$coverLetter\n";
            $adminBody .= "\nResume: " . $dbFilePath;

            @mail('info@everythingeasy.in', $adminSubject, $adminBody, $headers);

            $formSuccess = true;
            $formMessage = 'Application submitted successfully! Thank you for applying. A confirmation email has been sent to ' . $email;
            $formData = [
              'fullName' => '',
              'email' => '',
              'phone' => '',
              'position' => '',
              'experience' => '',
              'portfolio' => '',
              'coverLetter' => ''
            ];
          } else {
            $formMessage = 'Failed to submit application';
          }

          $stmt->close();
        }

        closeDBConnection($conn);
      }
    }
  }
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
  <title>Careers - Everything Easy</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet" />
  <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
  <link rel="manifest" href="site.webmanifest">

  <link href="/css/style.css" rel="stylesheet" />
  <script type="application/ld+json">
      {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "career",
        "url": "https://everythingeasy.in/career",
        "logo": "https://everythingeasy.in/assets/logo.png",
        "sameAs": [
          "https://www.facebook.com/profile.php?id=61575148140871",
          "https://www.instagram.com/everythingeasy0/",
          "https://www.linkedin.com/company/106846342/admin/dashboard/"
        ],
        "contactPoint": [
          {
            "@type": "ContactPoint",
            "telephone": "+91-8630840577",
            "contactType": "customer service",
            "areaServed": "IN"
          }
        ]
      }
    </script>
</head>

<body>
  <?php require_once 'navbar.php'; ?>
  <!-- Navigation Container -->

  <!-- Page Header -->
  <section class="py-5 bg-gradient-primary text-white" style="padding-top: 120px !important">
    <div class="container">
      <div class="row">
        <div class="col-12 text-center">
          <h1 class="display-4 fw-bold mb-3">Join Our Team</h1>
          <p class="lead mb-4">
            Build your career with Everything Easy and shape the future of
            technology
          </p>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center bg-transparent">
              <li class="breadcrumb-item">
                <a href="/" class="text-warning">Home</a>
              </li>
              <li class="breadcrumb-item active text-white" aria-current="page">
                Careers
              </li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </section>

  <!-- Why Join Us -->
  <section class="py-5 bg-light">
    <div class="container">
      <div class="row">
        <div class="col-lg-10 mx-auto text-center mb-5">
          <h5 class="text-primary mb-3">💼 WHY JOIN US</h5>
          <h2 class="fw-bold mb-4 display-5">Work With Industry Experts</h2>
          <p class="text-muted lead">
            At Everything Easy, we believe in fostering innovation,
            creativity, and growth. Join a team of passionate professionals
            dedicated to making a difference.
          </p>
        </div>
      </div>

      <!-- Benefits -->
      <div class="row text-center">
        <div class="col-md-3 col-6 mb-4">
          <div class="stat-card bg-white rounded shadow p-3">
            <div
              class="icon-circle bg-primary bg-opacity-10 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mx-auto mb-3"
              style="width: 60px; height: 60px">
              <i class="fas fa-users fa-2x text-primary"></i>
            </div>
            <h6 class="fw-bold mb-1">Great Team</h6>
            <small class="text-muted">Collaborative Culture</small>
          </div>
        </div>
        <div class="col-md-3 col-6 mb-4">
          <div class="stat-card bg-white rounded shadow p-3">
            <div
              class="icon-circle bg-success bg-opacity-10 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mx-auto mb-3"
              style="width: 60px; height: 60px">
              <i class="fas fa-chart-line fa-2x text-success"></i>
            </div>
            <h6 class="fw-bold mb-1">Growth</h6>
            <small class="text-muted">Career Development</small>
          </div>
        </div>
        <div class="col-md-3 col-6 mb-4">
          <div class="stat-card bg-white rounded shadow p-3">
            <div
              class="icon-circle bg-warning bg-opacity-10 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mx-auto mb-3"
              style="width: 60px; height: 60px">
              <i class="fas fa-laptop-code fa-2x text-warning"></i>
            </div>
            <h6 class="fw-bold mb-1">Technology</h6>
            <small class="text-muted">Latest Tools</small>
          </div>
        </div>
        <div class="col-md-3 col-6 mb-4">
          <div class="stat-card bg-white rounded shadow p-3">
            <div
              class="icon-circle bg-info bg-opacity-10 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mx-auto mb-3"
              style="width: 60px; height: 60px">
              <i class="fas fa-gift fa-2x text-info"></i>
            </div>
            <h6 class="fw-bold mb-1">Benefits</h6>
            <small class="text-muted">Competitive Package</small>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Open Positions -->
  <section class="py-5">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 mb-5 text-center">
          <h5 class="text-primary mb-3">🚀 OPEN POSITIONS</h5>
          <h3 class="fw-bold mb-4">Current Job Openings</h3>
        </div>
      </div>
      <div class="row justify-content-center">
        <!-- Full Stack Developer -->
        <div class="col-12 col-lg-10 mb-4">
          <div class="service-detail-card bg-white rounded shadow p-3 p-md-4 h-100">
            <div
              class="d-flex flex-column flex-md-row align-items-center align-items-md-start justify-content-between text-center text-md-start">
              <div class="flex-grow-1 mb-3 mb-md-0 w-100">
                <div class="d-flex flex-column align-items-center align-items-md-start mb-3">
                  <div
                    class="icon-circle bg-primary bg-opacity-10 rounded-circle p-2 d-inline-flex align-items-center justify-content-center mb-2 me-md-3"
                    style="width: 50px; height: 50px">
                    <i class="fas fa-code text-primary"></i>
                  </div>
                  <div class="mt-2 mt-md-0">
                    <h4 class="fw-bold mb-1">Full Stack Developer</h4>
                    <small class="text-muted">
                      <i class="fas fa-map-marker-alt me-1"></i>Dehradun,
                      India
                      <span class="mx-2">•</span>
                      <i class="fas fa-briefcase me-1"></i>Full-time
                      <span class="mx-2">•</span>
                      <i class="fas fa-money-bill-wave me-1"></i>5-12 LPA
                    </small>
                  </div>
                </div>
                <p class="text-muted mb-3">
                  We're looking for an experienced Full Stack Developer to
                  join our team. You'll work on exciting projects using modern
                  technologies like React, Node.js, and cloud platforms.
                </p>
                <div class="mb-2">
                  <span class="badge bg-primary bg-opacity-10 text-primary me-2 mb-2">React</span>
                  <span class="badge bg-primary bg-opacity-10 text-primary me-2 mb-2">Node.js</span>
                  <span class="badge bg-primary bg-opacity-10 text-primary me-2 mb-2">MongoDB</span>
                  <span class="badge bg-primary bg-opacity-10 text-primary me-2 mb-2">AWS</span>
                </div>
              </div>
              <div class="align-self-center mt-3 mt-md-0">
                <a href="#apply" class="btn btn-primary text-nowrap">Apply Now</a>
              </div>
            </div>
          </div>
        </div>

        <!-- UI/UX Designer -->
        <div class="col-12 col-lg-10 mb-4">
          <div class="service-detail-card bg-white rounded shadow p-3 p-md-4 h-100">
            <div
              class="d-flex flex-column flex-md-row align-items-center align-items-md-start justify-content-between text-center text-md-start">
              <div class="flex-grow-1 mb-3 mb-md-0 w-100">
                <div class="d-flex flex-column align-items-center align-items-md-start mb-3">
                  <div
                    class="icon-circle bg-success bg-opacity-10 rounded-circle p-2 d-inline-flex align-items-center justify-content-center mb-2 me-md-3"
                    style="width: 50px; height: 50px">
                    <i class="fas fa-palette text-success"></i>
                  </div>
                  <div class="mt-2 mt-md-0">
                    <h4 class="fw-bold mb-1">UI/UX Designer</h4>
                    <small class="text-muted">
                      <i class="fas fa-map-marker-alt me-1"></i>Dehradun,
                      India
                      <span class="mx-2">•</span>
                      <i class="fas fa-briefcase me-1"></i>Full-time
                      <span class="mx-2">•</span>
                      <i class="fas fa-money-bill-wave me-1"></i>5-9 LPA
                    </small>
                  </div>
                </div>
                <p class="text-muted mb-3">
                  Join our creative team as a UI/UX Designer. Create
                  beautiful, intuitive designs that enhance user experience
                  and drive business results.
                </p>
                <div class="mb-2">
                  <span class="badge bg-success bg-opacity-10 text-success me-2 mb-2">Figma</span>
                  <span class="badge bg-success bg-opacity-10 text-success me-2 mb-2">Adobe XD</span>
                  <span class="badge bg-success bg-opacity-10 text-success me-2 mb-2">Sketch</span>
                  <span class="badge bg-success bg-opacity-10 text-success me-2 mb-2">Prototyping</span>
                </div>
              </div>
              <div class="align-self-center mt-3 mt-md-0">
                <a href="#apply" class="btn btn-primary text-nowrap">Apply Now</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Digital Marketing Specialist -->
        <div class="col-12 col-lg-10 mb-4">
          <div class="service-detail-card bg-white rounded shadow p-3 p-md-4 h-100">
            <div
              class="d-flex flex-column flex-md-row align-items-center align-items-md-start justify-content-between text-center text-md-start">
              <div class="flex-grow-1 mb-3 mb-md-0 w-100">
                <div class="d-flex flex-column align-items-center align-items-md-start mb-3">
                  <div
                    class="icon-circle bg-warning bg-opacity-10 rounded-circle p-2 d-inline-flex align-items-center justify-content-center mb-2 me-md-3"
                    style="width: 50px; height: 50px">
                    <i class="fas fa-bullhorn text-warning"></i>
                  </div>
                  <div class="mt-2 mt-md-0">
                    <h4 class="fw-bold mb-1">Digital Marketing Specialist</h4>
                    <small class="text-muted">
                      <i class="fas fa-map-marker-alt me-1"></i>Dehradun,
                      India
                      <span class="mx-2">•</span>
                      <i class="fas fa-briefcase me-1"></i>Full-time
                      <span class="mx-2">•</span>
                      <i class="fas fa-money-bill-wave me-1"></i>5-11 LPA
                    </small>
                  </div>
                </div>
                <p class="text-muted mb-3">
                  We need a creative Digital Marketing Specialist to develop
                  and execute digital marketing campaigns that drive brand
                  awareness and customer engagement.
                </p>
                <div class="mb-2">
                  <span class="badge bg-warning bg-opacity-10 text-warning me-2 mb-2">SEO</span>
                  <span class="badge bg-warning bg-opacity-10 text-warning me-2 mb-2">Social Media</span>
                  <span class="badge bg-warning bg-opacity-10 text-warning me-2 mb-2">Google Ads</span>
                  <span class="badge bg-warning bg-opacity-10 text-warning me-2 mb-2">Content Strategy</span>
                </div>
              </div>
              <div class="align-self-center mt-3 mt-md-0">
                <a href="#apply" class="btn btn-primary text-nowrap">Apply Now</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Mobile App Developer -->
        <div class="col-12 col-lg-10 mb-4">
          <div class="service-detail-card bg-white rounded shadow p-3 p-md-4 h-100">
            <div
              class="d-flex flex-column flex-md-row align-items-center align-items-md-start justify-content-between text-center text-md-start">
              <div class="flex-grow-1 mb-3 mb-md-0 w-100">
                <div class="d-flex flex-column align-items-center align-items-md-start mb-3">
                  <div
                    class="icon-circle bg-info bg-opacity-10 rounded-circle p-2 d-inline-flex align-items-center justify-content-center mb-2 me-md-3"
                    style="width: 50px; height: 50px">
                    <i class="fas fa-mobile-alt text-info"></i>
                  </div>
                  <div class="mt-2 mt-md-0">
                    <h4 class="fw-bold mb-1">Mobile App Developer</h4>
                    <small class="text-muted">
                      <i class="fas fa-map-marker-alt me-1"></i>Dehradun,
                      India
                      <span class="mx-2">•</span>
                      <i class="fas fa-briefcase me-1"></i>Full-time
                      <span class="mx-2">•</span>
                      <i class="fas fa-money-bill-wave me-1"></i>5-13 LPA
                    </small>
                  </div>
                </div>
                <p class="text-muted mb-3">
                  Seeking a talented Mobile App Developer to build innovative
                  mobile applications for iOS and Android platforms using
                  React Native or Flutter.
                </p>
                <div class="mb-2">
                  <span class="badge bg-info bg-opacity-10 text-info me-2 mb-2">React Native</span>
                  <span class="badge bg-info bg-opacity-10 text-info me-2 mb-2">Flutter</span>
                  <span class="badge bg-info bg-opacity-10 text-info me-2 mb-2">iOS</span>
                  <span class="badge bg-info bg-opacity-10 text-info me-2 mb-2">Android</span>
                </div>
              </div>
              <div class="align-self-center mt-3 mt-md-0">
                <a href="#apply" class="btn btn-primary text-nowrap">Apply Now</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Company Culture -->
  <section class="py-5 bg-light">
    <div class="container">
      <div class="row">
        <div class="col-lg-8 mx-auto text-center mb-5">
          <h5 class="text-primary mb-3">OUR CULTURE</h5>
          <h2 class="fw-bold mb-4">What We Offer</h2>
        </div>
      </div>
      <div class="row justify-content-center">
        <div class="col-12 col-sm-6 col-lg-3 mb-4">
          <div class="text-center p-3">
            <div
              class="icon-circle bg-primary bg-opacity-10 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mx-auto mb-3"
              style="width: 80px; height: 80px">
              <i class="fas fa-graduation-cap fa-2x text-primary"></i>
            </div>
            <h5 class="fw-bold mb-3">Learning & Development</h5>
            <p class="text-muted small">
              Continuous learning opportunities with training programs and
              certifications
            </p>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3 mb-4">
          <div class="text-center p-3">
            <div
              class="icon-circle bg-success bg-opacity-10 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mx-auto mb-3"
              style="width: 80px; height: 80px">
              <i class="fas fa-clock fa-2x text-success"></i>
            </div>
            <h5 class="fw-bold mb-3">Flexible Hours</h5>
            <p class="text-muted small">
              Work-life balance with flexible working hours and remote options
            </p>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3 mb-4">
          <div class="text-center p-3">
            <div
              class="icon-circle bg-warning bg-opacity-10 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mx-auto mb-3"
              style="width: 80px; height: 80px">
              <i class="fas fa-heart fa-2x text-warning"></i>
            </div>
            <h5 class="fw-bold mb-3">Health Benefits</h5>
            <p class="text-muted small">
              Comprehensive health insurance coverage for you and your family
            </p>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3 mb-4">
          <div class="text-center p-3">
            <div
              class="icon-circle bg-info bg-opacity-10 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mx-auto mb-3"
              style="width: 80px; height: 80px">
              <i class="fas fa-rocket fa-2x text-info"></i>
            </div>
            <h5 class="fw-bold mb-3">Innovation</h5>
            <p class="text-muted small">
              Freedom to innovate and work on cutting-edge technologies
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Application Form -->
  <section class="py-5" id="apply">
    <div class="container">
      <div class="row">
        <div class="col-lg-8 mx-auto">
          <div class="bg-white rounded shadow p-4 p-md-5">
            <h3 class="fw-bold mb-4 text-center">Apply for a Position</h3>
            <?php if (!empty($formMessage)): ?>
              <div class="alert <?php echo $formSuccess ? 'alert-success' : 'alert-danger'; ?>" role="alert">
                <?php echo htmlspecialchars($formMessage); ?>
              </div>
            <?php endif; ?>
            <form id="careerForm" method="POST" enctype="multipart/form-data">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="fullName" class="form-label">Full Name *</label>
                  <input type="text" class="form-control" id="fullName" name="fullName" value="<?php echo htmlspecialchars($formData['fullName']); ?>" required />
                </div>
                <div class="col-md-6 mb-3">
                  <label for="email" class="form-label">Email *</label>
                  <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($formData['email']); ?>" required />
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="phone" class="form-label">Phone Number *</label>
                  <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($formData['phone']); ?>" required />
                </div>
                <div class="col-md-6 mb-3">
                  <label for="position" class="form-label">Position Applied For *</label>
                  <select class="form-select" id="position" name="position" required>
                    <option value="">Select Position</option>
                    <option value="Full Stack Developer" <?php echo $formData['position'] === 'Full Stack Developer' ? 'selected' : ''; ?>>
                      Full Stack Developer
                    </option>
                    <option value="UI/UX Designer" <?php echo $formData['position'] === 'UI/UX Designer' ? 'selected' : ''; ?>>UI/UX Designer</option>
                    <option value="Digital Marketing Specialist" <?php echo $formData['position'] === 'Digital Marketing Specialist' ? 'selected' : ''; ?>>
                      Digital Marketing Specialist
                    </option>
                    <option value="Mobile App Developer" <?php echo $formData['position'] === 'Mobile App Developer' ? 'selected' : ''; ?>>
                      Mobile App Developer
                    </option>
                    <option value="Other" <?php echo $formData['position'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                  </select>
                </div>
              </div>
              <div class="mb-3">
                <label for="experience" class="form-label">Years of Experience *</label>
                <input type="number" class="form-control" id="experience" name="experience" min="0" value="<?php echo htmlspecialchars($formData['experience']); ?>" required />
              </div>
              <div class="mb-3">
                <label for="resume" class="form-label">Resume/CV (PDF) *</label>
                <input type="file" class="form-control" id="resume" name="resume" accept=".pdf" required />
              </div>
              <div class="mb-3">
                <label for="portfolio" class="form-label">Portfolio Link (Optional)</label>
                <input type="url" class="form-control" id="portfolio" name="portfolio" value="<?php echo htmlspecialchars($formData['portfolio']); ?>" placeholder="https://" />
              </div>
              <div class="mb-3">
                <label for="coverLetter" class="form-label">Cover Letter *</label>
                <textarea class="form-control" id="coverLetter" name="coverLetter" rows="5"
                  placeholder="Tell us why you'd be a great fit for this role..." required><?php echo htmlspecialchars($formData['coverLetter']); ?></textarea>
              </div>
              <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg">
                  Submit Application
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Call to Action -->
  <section class="py-5 bg-gradient-primary text-white">
    <div class="container">
      <div class="row">
        <div class="col-lg-8 mx-auto text-center">
          <h2 class="fw-bold mb-4">Don't See Your Role?</h2>
          <p class="mb-4">
            We're always looking for talented individuals. Send us your resume
            and we'll keep you in mind for future opportunities.
          </p>
          <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center gap-3">
            <a href="mailto:info@everythingeasy.com" class="btn btn-warning btn-lg">
              <i class="fas fa-envelope me-2"></i>Email Your Resume
            </a>
            <a href="/contact" class="btn btn-outline-light btn-lg">
              Contact Us
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
  <script src="/js/script.js"></script>
</body>

</html>