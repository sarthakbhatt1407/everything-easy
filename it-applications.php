<?php include "backend/config.php";
//get slug from url
$slug = $_GET['slug'];
$conn = getDBConnection();
//fetch location details based on slug
$sql = "SELECT * FROM locations_application WHERE slug = '$slug'";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
  $loc = mysqli_fetch_assoc($result);
} else {
  //redirect to services-locations.php if no location found
  header("Location: /services-locations.php");
  exit();
}

echo '';
$address = $loc['location_name'];
$city = $loc['city_name'];
$state = $loc['state'];

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

  <title><?php echo htmlspecialchars($loc['meta_title']); ?></title>

  <meta name="description" content="<?php echo htmlspecialchars($loc['meta_description']); ?>">
  <meta name="keywords"
    content="EverythingEasy, mobile app development, iOS development, Android app, web apps, API development, India app development">
  <meta name="author" content="EverythingEasy">

  <meta name="robots" content="index, follow">
  <meta name="googlebot" content="index, follow">

  <!-- Canonical -->
  <link rel="canonical" href="https://everythingeasy.in/it-applications/<?php echo $loc['slug']; ?>">

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="https://everythingeasy.in/images/app_development.webp">

  <!-- Open Graph / Social -->
  <meta property="og:title" content="<?php echo htmlspecialchars($loc['meta_title'], ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:description"
    content="<?php echo htmlspecialchars($loc['meta_description'], ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:image" content="https://everythingeasy.in/images/app_development.jpg">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:url" content="https://everythingeasy.in/it-applications/<?php echo $loc['slug']; ?>">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="EverythingEasy">

  <!-- Twitter -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?php echo htmlspecialchars($loc['meta_title']); ?>">
  <meta name="twitter:description" content="<?php echo htmlspecialchars($loc['meta_description']); ?>">
  <meta name="twitter:image" content="https://everythingeasy.in/images/app_development.webp">

  <!-- Mobile Meta -->
  <meta name="format-detection" content="telephone=no">
  <meta name="HandheldFriendly" content="true">
  <meta name="MobileOptimized" content="320">

  <!-- Language -->
  <meta name="language" content="English">
  <meta http-equiv="content-language" content="en">

  <!-- Schema Markup -->
  <script type="application/ld+json">
      {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "EverythingEasy",
        "url": "https://everythingeasy.in/it-applications/<?php echo htmlspecialchars($loc['slug']); ?>",
        "logo": "https://everythingeasy.in/image/elogo.png",
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
  <!-- Dynamic LocalBusiness Schema -->

  <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "EverythingEasy Technology - <?php echo htmlspecialchars($loc['location_name']); ?>",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "<?= $address ?>",
    "addressLocality": "<?= $city ?>",
    "addressRegion": "<?= $state ?>",
    "addressCountry": "IN"
  },
  "url": "https://everythingeasy.in/it-applications/<?php echo htmlspecialchars($loc['slug']); ?>",
  "telephone": "8630840577",
  "sameAs": [],
  "image": "https://everythingeasy.in/image/app_development.webp
"
}
</script>

  <!-- CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet" />
  <link href="https://everythingeasy.in/css/style.css" rel="stylesheet" />
  <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
  <link rel="manifest" href="site.webmanifest">
</head>


<body>


  <?php require_once 'navbar.php'; ?>
  <!-- Navigation Container -->

  <!-- Hero Section -->
  <section class="service-hero">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6">
          <div class="hero-content">
            <h5 class="text-warning mb-3">
              PROFESSIONAL APPLICATION DEVELOPMENT
            </h5>
            <h1 class="display-4 fw-bold mb-4 text-white">
              <?php echo $loc['meta_title']; ?>
            </h1>
            <p class="lead mb-4 text-white" style="font-size: 1.35rem;">
              Transform your ideas into powerful mobile and web applications with expert app development services in <?php echo $loc['location_name']; ?>. Our team specializes in creating innovative, scalable, and user-friendly applications that drive business growth. With over 10+ years of experience, we deliver custom app solutions tailored to your unique business needs.
            </p>
          </div>
        </div>

        <div class="col-lg-5 col-md-8 col-sm-10 mx-auto">
          <div class="card shadow-lg border-0" style="
                border-radius: 20px;
                max-width: 450px;
                margin-left: auto;
                margin-right: auto;
              " id="hero-contact-form">
            <div class="card-body p-3 p-md-4">
              <h4 class="fw-bold mb-3 text-center" style="color: #1e3c72; font-size: 1.25rem">
                Get a Free Quote
              </h4>
              <form id="heroQuoteForm" class="hero-quote-form">
                <div class="mb-2">
                  <input type="text" class="form-control" id="heroName" name="name" placeholder="Your Name*" required
                    style="border-radius: 8px; padding: 10px;"/>
                </div>
                <div class="mb-2">
                  <input type="email" class="form-control" id="heroEmail" name="email" placeholder="Your Email*"
                    required style="border-radius: 8px; padding: 10px;"/>
                </div>
                <div class="mb-2">
                  <input type="tel" class="form-control" id="heroPhone" name="phone" placeholder="Phone Number*"
                    required style="border-radius: 8px; padding: 10px;"/>
                </div>
                <div class="mb-2">
                  <select class="form-select" id="heroService" name="service" required style="border-radius: 8px; padding: 10px;">
                    <option value="" selected disabled>Select Service*</option>
                    <option value="Mobile App Development">Mobile App Development</option>
                    <option value="Web Application">Web Application</option>
                    <option value="API Development">API Development</option>
                    <option value="App Maintenance">App Maintenance</option>
                  </select>
                </div>
                <div class="mb-3">
                  <textarea class="form-control" id="heroMessage" name="message" rows="2" placeholder="Brief Description" style="border-radius: 8px; padding: 10px;"></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100" style="
                      border-radius: 8px;
                      padding: 10px;
                      font-size: 15px;
                      font-weight: 600;
                    ">
                  Submit Request
                </button>
                <div id="heroFormResult" class="mt-2 d-none"></div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Metrics Section -->
  <section class="metrics-section">
    <div class="container">
      <div class="row">
        <div class="col-md-3 col-6">
          <div class="metric-box">
            <h2>75+</h2>
            <p>Apps Delivered</p>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="metric-box">
            <h2>98%</h2>
            <p>Client Satisfaction</p>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="metric-box">
            <h2>4+</h2>
            <p>Years Experience</p>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="metric-box">
            <h2>15+</h2>
            <p>Expert Developers</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Case Studies Section -->
  <section class="case-study-section">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="fw-bold">
          Case Studies - App Success Stories in <?php echo $loc['location_name']; ?>
        </h2>
        <p class="lead text-muted">
          We deliver high-quality mobile and web applications that solve real business problems 
          and drive user engagement. Our portfolio includes apps across various industries 
          including startups, e-commerce, fintech, and enterprise solutions in <?php echo $loc['location_name']; ?>.
        </p>
      </div>

      <div class="row">
        <!-- Case Study 1 -->
        <div class="col-lg-6">
          <div class="case-study-card">
            <div class="d-flex align-items-center mb-3">
              <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="
                    width: 80px;
                    height: 80px;
                    font-size: 24px;
                    font-weight: 700;
                  ">
                MA
              </div>
              <div class="ms-3">
                <h5 class="mb-1">Mobile Commerce App</h5>
                <p class="text-muted mb-0">Shopping Made Easy</p>
              </div>
            </div>
            <table class="results-table">
              <thead>
                <tr>
                  <th>Metric</th>
                  <th>Result</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>App Launch Time</td>
                  <td><span class="badge-success">↓ 60% faster</span></td>
                </tr>
                <tr>
                  <td>User Retention</td>
                  <td><span class="badge-success">↑ 85%</span></td>
                </tr>
                <tr>
                  <td>App Performance</td>
                  <td><span class="badge-success">4.8★ Rating</span></td>
                </tr>
                <tr>
                  <td>User Engagement</td>
                  <td><span class="badge-success">↑ 120%</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Case Study 2 -->
        <div class="col-lg-6">
          <div class="case-study-card">
            <div class="d-flex align-items-center mb-3">
              <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="
                    width: 80px;
                    height: 80px;
                    font-size: 24px;
                    font-weight: 700;
                  ">
                FA
              </div>
              <div class="ms-3">
                <h5 class="mb-1">FinTech Mobile App</h5>
                <p class="text-muted mb-0">Digital Payments Platform</p>
              </div>
            </div>
            <table class="results-table">
              <thead>
                <tr>
                  <th>Metric</th>
                  <th>Result</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Security Compliance</td>
                  <td><span class="badge-success">100% PCI-DSS</span></td>
                </tr>
                <tr>
                  <td>Transaction Volume</td>
                  <td><span class="badge-success">↑ 250%</span></td>
                </tr>
                <tr>
                  <td>API Response Time</td>
                  <td><span class="badge-success">↓ 200ms</span></td>
                </tr>
                <tr>
                  <td>User Base Growth</td>
                  <td><span class="badge-success">↑ 300K users</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Case Study 3 -->
        <div class="col-lg-6">
          <div class="case-study-card">
            <div class="d-flex align-items-center mb-3">
              <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="
                    width: 80px;
                    height: 80px;
                    font-size: 24px;
                    font-weight: 700;
                  ">
                SA
              </div>
              <div class="ms-3">
                <h5 class="mb-1">Social Networking App</h5>
                <p class="text-muted mb-0">Community Platform</p>
              </div>
            </div>
            <table class="results-table">
              <thead>
                <tr>
                  <th>Metric</th>
                  <th>Result</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Concurrent Users</td>
                  <td><span class="badge-success">50,000+</span></td>
                </tr>
                <tr>
                  <td>Server Uptime</td>
                  <td><span class="badge-success">99.99%</span></td>
                </tr>
                <tr>
                  <td>Real-time Sync</td>
                  <td><span class="badge-success">Sub-second</span></td>
                </tr>
                <tr>
                  <td>Daily Active Users</td>
                  <td><span class="badge-success">↑ 150%</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Case Study 4 -->
        <div class="col-lg-6">
          <div class="case-study-card">
            <div class="d-flex align-items-center mb-3">
              <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="
                    width: 80px;
                    height: 80px;
                    font-size: 24px;
                    font-weight: 700;
                  ">
                EA
              </div>
              <div class="ms-3">
                <h5 class="mb-1">Enterprise App</h5>
                <p class="text-muted mb-0">Workforce Management</p>
              </div>
            </div>
            <table class="results-table">
              <thead>
                <tr>
                  <th>Metric</th>
                  <th>Result</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Deployment Time</td>
                  <td><span class="badge-success">↓ 70%</span></td>
                </tr>
                <tr>
                  <td>User Adoption</td>
                  <td><span class="badge-success">95% in 30 days</span></td>
                </tr>
                <tr>
                  <td>Productivity Gain</td>
                  <td><span class="badge-success">↑ 45%</span></td>
                </tr>
                <tr>
                  <td>Cost Savings</td>
                  <td><span class="badge-success">↑ 35%</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- How We Can Help Section -->
  <section class="how-we-help">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="fw-bold">
          How We Can Help Your <?php echo $loc['location_name']; ?> Business With App Development
        </h2>
        <p class="lead text-muted">
          Application development is more than just coding. We focus on creating solutions that 
          drive real business value. Here's what sets us apart as an app development partner for 
          <?php echo $loc['location_name']; ?> businesses.
        </p>
      </div>

      <div class="row">
        <div class="col-lg-4 col-md-6">
          <div class="help-card">
            <i class="fas fa-shield-alt"></i>
            <h6>Secure & Scalable Apps</h6>
            <p>
              We build applications with security and scalability at the core. 
              From user authentication to data encryption, we implement enterprise-grade 
              security practices to protect your business and users.
            </p>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="help-card">
            <i class="fas fa-mobile-alt"></i>
            <h6>Multi-Platform Development</h6>
            <p>
              Cross-platform development for iOS, Android, and web. Reach your audience 
              on all devices with apps that work seamlessly across platforms. 
              We use modern frameworks like React Native, Flutter, and more.
            </p>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="help-card">
            <i class="fas fa-users"></i>
            <h6>User-Centric Design</h6>
            <p>
              Beautiful and intuitive interfaces that users love. We conduct extensive 
              UX research and user testing to ensure your app is engaging and easy to use. 
              Every interaction is carefully crafted.
            </p>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="help-card">
            <i class="fas fa-cogs"></i>
            <h6>Custom Solutions</h6>
            <p>
              Every business is unique, and so are our apps. We don't use templates or 
              one-size-fits-all solutions. We develop custom applications tailored to 
              your specific business requirements and goals.
            </p>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="help-card">
            <i class="fas fa-rocket"></i>
            <h6>Fast & Reliable</h6>
            <p>
              Lightning-fast performance is crucial for app success. We optimize every 
              aspect of your application for speed, reliability, and responsiveness. 
              Your users will feel the difference.
            </p>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="help-card">
            <i class="fas fa-handshake"></i>
            <h6>Ongoing Support</h6>
            <p>
              Our relationship doesn't end at app launch. We provide continuous support, 
              updates, maintenance, and feature enhancements. We're here to help your 
              app grow and evolve with your business.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Why Choose Us Section -->
  <section class="why-choose">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="fw-bold">
          Why Choose EverythingEasy Technology for App Development in <?php echo $loc['location_name']; ?>
        </h2>
        <p class="lead text-muted">
          Finding the right app development partner is critical for your success. 
          Here's why <?php echo $loc['location_name']; ?> businesses choose EverythingEasy Technology.
        </p>
      </div>

      <div class="row">
        <div class="col-lg-6">
          <div class="choose-card">
            <h5>
              <i class="fas fa-trophy me-2"></i> Experienced Developers
            </h5>
            <p class="mb-0">
              Our team includes senior developers with 10+ years of experience building 
              successful mobile and web applications. We stay current with latest technologies 
              and best practices to deliver cutting-edge solutions for <?php echo $loc['location_name']; ?> businesses.
            </p>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="choose-card">
            <h5>
              <i class="fas fa-dollar-sign me-2"></i> Competitive Pricing
            </h5>
            <p class="mb-0">
              High-quality app development at affordable rates. We offer flexible engagement 
              models - from fixed price projects to dedicated teams. Get premium development 
              without breaking your budget.
            </p>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="choose-card">
            <h5><i class="fas fa-clock me-2"></i> Agile Development</h5>
            <p class="mb-0">
              We use agile methodology to deliver apps faster while maintaining quality. 
              Regular sprints and feedback loops ensure you're always aligned with the project 
              progress and can adapt to changing requirements quickly.
            </p>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="choose-card">
            <h5>
              <i class="fas fa-sync-alt me-2"></i> Post-Launch Maintenance
            </h5>
            <p class="mb-0">
              We don't just build and disappear. We provide comprehensive post-launch support 
              including bug fixes, performance optimization, feature updates, and platform 
              updates to keep your app running smoothly.
            </p>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="choose-card">
            <h5><i class="fas fa-star me-2"></i> Proven Track Record</h5>
            <p class="mb-0">
              Our <?php echo $loc['location_name']; ?> clients' success speaks for itself. 
              We've delivered 75+ successful applications with 98% client satisfaction. 
              We take pride in our reputation for excellence and reliability.
            </p>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="choose-card">
            <h5>
              <i class="fas fa-laptop-code me-2"></i> Latest Technologies
            </h5>
            <p class="mb-0">
              We work with the latest frameworks and technologies including React Native, 
              Flutter, Swift, Kotlin, Node.js, TypeScript, and more. Our tech-agnostic 
              approach ensures we choose the best tools for your specific app requirements.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="boost-cta">
    <div class="container">
      <h2>Ready to Build Your App in <?php echo $loc['location_name']; ?>?</h2>
      <p>
        Get professional mobile and web application development with experienced developers 
        and proven processes. Let us bring your app idea to life with innovative solutions 
        and dedicated support.
      </p>
      <a href="tel:+918630840577" class="btn btn-warning btn-lg me-3">
        <i class="fas fa-phone-alt me-2"></i>Call Us Now
      </a>
      <a href="#contact" class="btn btn-outline-light btn-lg">
        Get a Free Quote
      </a>
    </div>
  </section>

  <!-- FAQ Section -->
  <section class="faq-section">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="fw-bold">
          App Development in <?php echo $loc['location_name']; ?>: Frequently Asked Questions
        </h2>
        <p class="lead text-muted">
          Got questions about our app development services? Find answers to common questions 
          here. If you need more information, feel free to reach out to us.
        </p>
      </div>

      <div class="row">
        <div class="col-lg-8 mx-auto">
          <div class="faq-item" onclick="toggleFaq(this)">
            <h6>
              How much does it cost to develop an app in <?php echo $loc['location_name']; ?>?
              <i class="fas fa-chevron-down"></i>
            </h6>
            <div class="faq-answer">
              <p>
                App development costs vary based on complexity, features, platforms (iOS/Android/Web), 
                and design requirements. Simple apps might cost 5-10 lakhs, while complex applications 
                can be 20 lakhs or more. After understanding your requirements, we provide a detailed 
                quote with transparent pricing and no hidden costs.
              </p>
            </div>
          </div>

          <div class="faq-item" onclick="toggleFaq(this)">
            <h6>
              How long does it take to develop an app?
              <i class="fas fa-chevron-down"></i>
            </h6>
            <div class="faq-answer">
              <p>
                Timeline depends on app complexity and features. A simple app takes 2-3 months, 
                while a complex application with backend and multiple features can take 4-8 months. 
                We use agile methodology for iterative delivery, so you see progress throughout development. 
                We work to meet your launch timeline while maintaining quality.
              </p>
            </div>
          </div>

          <div class="faq-item" onclick="toggleFaq(this)">
            <h6>
              Do you develop apps for iOS, Android, or both?
              <i class="fas fa-chevron-down"></i>
            </h6>
            <div class="faq-answer">
              <p>
                We develop for both iOS and Android, as well as web applications. For cross-platform 
                development, we use React Native and Flutter to build once and deploy everywhere. 
                We also develop native apps for better performance when needed. We'll recommend 
                the best approach based on your target audience and requirements.
              </p>
            </div>
          </div>

          <div class="faq-item" onclick="toggleFaq(this)">
            <h6>
              What about app maintenance and updates?
              <i class="fas fa-chevron-down"></i>
            </h6>
            <div class="faq-answer">
              <p>
                Yes! We provide comprehensive post-launch support and maintenance for all our 
                <?php echo $loc['location_name']; ?> clients. This includes bug fixes, OS updates, 
                new feature development, performance optimization, and security patches. 
                We believe in building long-term partnerships with our clients.
              </p>
            </div>
          </div>

          <div class="faq-item" onclick="toggleFaq(this)">
            <h6>
              Will my app be secure and handle user data safely?
              <i class="fas fa-chevron-down"></i>
            </h6>
            <div class="faq-answer">
              <p>
                Security is our top priority. We implement end-to-end encryption, secure API 
                development, user authentication best practices, and regular security audits. 
                We comply with data protection regulations and use industry-standard security 
                practices to protect your users' data.
              </p>
            </div>
          </div>

          <div class="faq-item" onclick="toggleFaq(this)">
            <h6>
              Can you integrate third-party services and APIs?
              <i class="fas fa-chevron-down"></i>
            </h6>
            <div class="faq-answer">
              <p>
                Absolutely! We integrate popular third-party services including payment gateways 
                (Stripe, Razorpay), push notifications, analytics, maps, social logins, and more. 
                We also develop custom APIs if you have unique integration needs. Seamless integration 
                is crucial for app success.
              </p>
            </div>
          </div>

          <div class="faq-item" onclick="toggleFaq(this)">
            <h6>
              Do you provide app design services?
              <i class="fas fa-chevron-down"></i>
            </h6>
            <div class="faq-answer">
              <p>
                Yes! Our team includes UX/UI designers who create beautiful, user-friendly interfaces. 
                We conduct user research, create wireframes, design mockups, and iterate based on 
                feedback. Great design is essential for app adoption and user engagement.
              </p>
            </div>
          </div>

          <div class="faq-item" onclick="toggleFaq(this)">
            <h6>
              What is your app development process?
              <i class="fas fa-chevron-down"></i>
            </h6>
            <div class="faq-answer">
              <p>
                Our proven process includes: Discovery & Planning → Requirements Analysis → UI/UX Design 
                → Development → Testing & QA → Deployment → Maintenance & Support. We keep you updated 
                at every stage through regular demos and reports. You can provide feedback and request 
                changes throughout the development process.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Specialized Services Section -->
  <section class="specialized-services">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="fw-bold">
          Our App Development Services in <?php echo $loc['location_name']; ?>
        </h2>
        <p class="lead text-muted">
          Comprehensive application solutions for all your business needs
        </p>
      </div>

      <div class="service-grid">
        <a href="/services#mobile-apps" class="service-card-item">
          <i class="fas fa-mobile-alt"></i>
          <h6>Mobile App Development</h6>
        </a>

        <a href="/services#ios" class="service-card-item">
          <i class="fab fa-apple"></i>
          <h6>iOS App Development</h6>
        </a>

        <a href="/services#android" class="service-card-item">
          <i class="fab fa-android"></i>
          <h6>Android App Development</h6>
        </a>

        <a href="/services#web-apps" class="service-card-item">
          <i class="fas fa-globe"></i>
          <h6>Web Applications</h6>
        </a>

        <a href="/services#api" class="service-card-item">
          <i class="fas fa-plug"></i>
          <h6>API Development</h6>
        </a>

        <a href="/services#backend" class="service-card-item">
          <i class="fas fa-server"></i>
          <h6>Backend Development</h6>
        </a>

        <a href="/services#real-time" class="service-card-item">
          <i class="fas fa-sync"></i>
          <h6>Real-time Applications</h6>
        </a>

        <a href="/services#iot-apps" class="service-card-item">
          <i class="fas fa-microchip"></i>
          <h6>IoT Applications</h6>
        </a>

        <a href="/services#ar-vr" class="service-card-item">
          <i class="fas fa-cube"></i>
          <h6>AR/VR Applications</h6>
        </a>

        <a href="/services#game-dev" class="service-card-item">
          <i class="fas fa-gamepad"></i>
          <h6>Game Development</h6>
        </a>

        <a href="/services#ai-apps" class="service-card-item">
          <i class="fas fa-brain"></i>
          <h6>AI-Powered Apps</h6>
        </a>

        <a href="/services#app-testing" class="service-card-item">
          <i class="fas fa-check-double"></i>
          <h6>App Testing & QA</h6>
        </a>

        <a href="/services#analytics" class="service-card-item">
          <i class="fas fa-chart-line"></i>
          <h6>Analytics Integration</h6>
        </a>

        <a href="/services#devops" class="service-card-item">
          <i class="fas fa-server"></i>
          <h6>DevOps Services</h6>
        </a>

        <a href="/services#migration" class="service-card-item">
          <i class="fas fa-exchange-alt"></i>
          <h6>App Migration</h6>
        </a>

        <a href="/services#app-support" class="service-card-item">
          <i class="fas fa-tools"></i>
          <h6>App Support</h6>
        </a>
      </div>
    </div>
  </section>

  <!-- Why Choose Section - Detailed -->
  <section class="py-5 bg-white">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="fw-bold">
          Application Development Company in <?php echo $loc['location_name']; ?>: Why EverythingEasy Technology?
        </h2>
      </div>

      <div class="mb-5">
        <p class="lead" style="line-height: 1.9; color: #6c757d">
          In today's mobile-first world, having the right app is crucial for business success. 
          Finding a reliable <strong> application development company in <?php echo $loc['location_name']; ?></strong> 
          can make or break your digital transformation journey. At EverythingEasy Technology, 
          we specialize in creating innovative, scalable applications that deliver real business value.
        </p>

        <p style="line-height: 1.9; color: #6c757d">
          Our approach goes beyond just writing code. We deeply understand your business objectives, 
          target audience, and market dynamics in <?php echo $loc['location_name']; ?>. Our team combines 
          technical expertise with business acumen to develop applications that not only work flawlessly 
          but also drive growth and user engagement for your business.
        </p>

        <p style="line-height: 1.9; color: #6c757d">
          Whether you need a mobile app, web application, or a complete platform, we deliver solutions 
          with cutting-edge technology, excellent design, and unwavering commitment to quality. 
          We use modern development frameworks and follow agile methodologies to ensure fast delivery 
          without compromising on excellence.
        </p>

        <p style="line-height: 1.9; color: #6c757d">
          Partner with EverythingEasy Technology, your trusted 
          <strong> application development partner in <?php echo $loc['location_name']; ?> </strong>, 
          and transform your business with innovative app solutions that your users will love.
        </p>
      </div>

      <div class="mb-5">
        <h3 class="fw-bold mb-4">
          App Development Services near Me in <?php echo $loc['location_name']; ?>
        </h3>

        <p style="line-height: 1.9; color: #6c757d">
          Looking for professional <strong>app development services in <?php echo $loc['location_name']; ?></strong>? 
          EverythingEasy Technology offers comprehensive application development services tailored to your needs. 
          From mobile apps to enterprise solutions, we cover all aspects of modern application development.
        </p>

        <p style="line-height: 1.9; color: #6c757d">
          Our local development team understands the <?php echo $loc['location_name']; ?> market and can provide 
          solutions optimized for your target audience. We work closely with you throughout the development process, 
          ensuring your vision becomes reality with professional quality and timely delivery.
        </p>
      </div>

      <div class="mb-5">
        <h3 class="fw-bold mb-4">
          Hire App Developers in <?php echo $loc['location_name']; ?>
        </h3>

        <p style="line-height: 1.9; color: #6c757d">
          Whether you need a full development team or dedicated developers, we have the expertise and resources. 
          Our experienced app developers can build anything from simple utility apps to complex enterprise applications. 
          We customize our engagement model to fit your project needs and budget.
        </p>

        <p style="line-height: 1.9; color: #6c757d">
          With 10+ years of experience and 75+ successful applications delivered, our developers bring 
          valuable expertise to your project. We stay updated with latest technologies and best practices 
          to deliver modern, future-proof applications that scale with your business growth.
        </p>
      </div>

      <div class="mb-5">
        <h3 class="fw-bold mb-4">
          Custom App Development in <?php echo $loc['location_name']; ?>
        </h3>

        <p style="line-height: 1.9; color: #6c757d">
          Every business has unique requirements, and we build custom applications specifically designed 
          for your needs. No templates, no compromises - just bespoke solutions that help your business stand out 
          in the competitive market of <?php echo $loc['location_name']; ?>.
        </p>

        <p style="line-height: 1.9; color: #6c757d">
          Our custom development process starts with thorough requirement analysis and continues through 
          iterative development cycles. We ensure every feature, every design element, and every line of code 
          aligns with your business objectives and user needs.
        </p>
      </div>

      <div class="mb-5">
        <h3 class="fw-bold mb-4">
          Why Choose EverythingEasy Technology for App Development?
        </h3>

        <p style="line-height: 1.9; color: #6c757d">
          We don't just build apps; we build success stories. Our approach focuses on creating applications that 
          solve real problems and drive measurable business results. With transparent communication, agile processes, 
          and dedicated support, we ensure your app development journey is smooth and successful.
        </p>

        <p style="line-height: 1.9; color: #6c757d">
          From initial concept to post-launch support, we're with you every step of the way. 
          Our commitment to excellence, combined with our experience in developing apps across diverse industries, 
          makes us the ideal partner for your app development needs in <?php echo $loc['location_name']; ?>.
        </p>
      </div>
    </div>
  </section>

  <!-- Contact CTA Section -->
  <section class="py-5 bg-light" id="contact">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-8">
          <h3 class="fw-bold mb-3">
            Ready to Build Your App in <?php echo $loc['location_name']; ?>?
          </h3>
          <p class="lead text-muted mb-lg-0">
            Let's discuss how we can develop a powerful application for your 
            <?php echo $loc['location_name']; ?> business. Contact us today for a free 
            consultation and detailed quote.
          </p>
        </div>
        <div class="col-lg-4 text-lg-end">
          <a href="contact.html" class="btn btn-primary btn-lg">
            <i class="fas fa-envelope me-2"></i>Contact Us Now
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer Container -->
 <!-- Footer -->
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
              href="https://www.instagram.com/everythingeasy_technology/"
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
              <a href="/services-locations" class="text-muted"
                >Services location Web</a
              >
            </li>
            <li>
              <a href="/it-applications-location" class="text-muted"
                >Services location App</a
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
              href="mailto:info@everythingeasy.in"
              class="text-muted text-decoration-none"
              >info@everythingeasy.in</a
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
<!--Start of Tawk.to Script--> 
<script type="text/javascript"> var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date(); (function(){ var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0]; s1.async=true; s1.src='https://embed.tawk.to/69d20bf25b6b4c1c37f3d691/1jle7tbuh'; s1.charset='UTF-8'; s1.setAttribute('crossorigin','*'); s0.parentNode.insertBefore(s1,s0); })(); </script> <!--End of Tawk.to Script-->
<!-- TrustBox script -->

<!-- End TrustBox script -->

<!-- TrustBox script -->

<!-- End TrustBox script -->


  <!-- Bootstrap JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

  <!-- FAQ Toggle Script -->
  <script>
    function toggleFaq(element) {
      element.classList.toggle("active");
    }

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
      anchor.addEventListener("click", function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute("href"));
        if (target) {
          target.scrollIntoView({
            behavior: "smooth",
            block: "start",
          });
        }
      });
    });

    // Add animation on scroll
    const observerOptions = {
      threshold: 0.1,
      rootMargin: "0px 0px -50px 0px",
    };

    const observer = new IntersectionObserver(function (entries) {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = "1";
          entry.target.style.transform = "translateY(0)";
        }
      });
    }, observerOptions);

    document
      .querySelectorAll(".case-study-card, .help-card, .choose-card")
      .forEach((el) => {
        el.style.opacity = "0";
        el.style.transform = "translateY(20px)";
        el.style.transition = "opacity 0.6s ease, transform 0.6s ease";
        observer.observe(el);
      });
  </script>

  <!-- Quote Form Handler -->
  <script>
    // Function to submit quote form
    function submitQuoteForm(formId, resultId) {
      const form = document.getElementById(formId);
      const formResult = document.getElementById(resultId);

      // Check if form exists before adding event listener
      if (!form) {
        console.warn(`Form with ID "${formId}" not found`);
        return;
      }

      form.addEventListener("submit", function (e) {
        e.preventDefault();

        // Get form data
        const formData = new FormData(this);

        // Split name into firstName and lastName
        const fullName = formData.get("name").trim().split(" ");
        const firstName = fullName[0];
        const lastName = fullName.slice(1).join(" ") || firstName;

        // Prepare data for API
        const data = {
          firstName: firstName,
          lastName: lastName,
          email: formData.get("email"),
          phone: formData.get("phone"),
          service: formData.get("service"),
          message:
            formData.get("message") || "Quick quote request from app development page",
        };

        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML =
          '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
        submitBtn.disabled = true;

        // Show instant confirmation for better UX while email processing continues.
        if (formResult) {
          formResult.className = "mt-2 alert alert-success";
          formResult.innerHTML =
            '<i class="fas fa-check-circle me-2"></i>Thank you! Your request has been submitted successfully.';
          formResult.classList.remove("d-none");
        }

        // Reset form and button immediately so users are not blocked by mail send time.
        form.reset();
        form.classList.remove("was-validated");
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;

        // Submit via AJAX
        fetch("https://everythingeasy.in/backend/submit-quote.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(data),
        })
          .then((response) => response.json())
          .then((result) => {
            if (!result.success) {
              // Show error message
              if (formResult) {
                formResult.className = "mt-2 alert alert-danger";
                formResult.innerHTML =
                  '<i class="fas fa-exclamation-circle me-2"></i>' +
                  (result.message || "Unable to process your request. Please try again.");
                formResult.classList.remove("d-none");
              }

              // Hide message after 5 seconds
              setTimeout(() => {
                if (formResult) {
                  formResult.classList.add("d-none");
                }
              }, 5000);
            }
          })
          .catch((error) => {
            console.error("Error:", error);

            // Show error message
            if (formResult) {
              formResult.className = "mt-2 alert alert-danger";
              formResult.innerHTML =
                '<i class="fas fa-exclamation-circle me-2"></i>An error occurred. Please try again later.';
              formResult.classList.remove("d-none");
            }

            // Hide message after 5 seconds
            setTimeout(() => {
              if (formResult) {
                formResult.classList.add("d-none");
              }
            }, 5000);
          });
      });
    }

    // Initialize form when DOM is loaded
    document.addEventListener("DOMContentLoaded", function () {
      submitQuoteForm("heroQuoteForm", "heroFormResult");
    });
  </script>
</body>

</html>
