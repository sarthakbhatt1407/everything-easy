<?php include "backend/config.php"; 
//get slug from url
$slug = $_GET['slug'];
$conn = getDBConnection();
//fetch location details based on slug
$sql = "SELECT * FROM locations WHERE slug = '$slug'";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
  $loc = mysqli_fetch_assoc($result);
} else {
  //redirect to services-locations.php if no location found
  header("Location: /services-locations.php");
  exit();
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

  <title><?php echo htmlspecialchars($loc['meta_title']); ?></title>

  <meta name="description" content="<?php echo htmlspecialchars($loc['meta_description']); ?>">
  <meta name="keywords" content="EverythingEasy, website development company, web design, SEO company, digital marketing, ecommerce website, India web agency">
  <meta name="author" content="EverythingEasy">

  <meta name="robots" content="index, follow">
  <meta name="googlebot" content="index, follow">

  <!-- Canonical -->
  <link rel="canonical" href="https://everythingeasy.in/it-services/<?php echo $loc['slug']; ?>">

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="https://everythingeasy.in/image/elogo.png">

  <!-- Open Graph / Social -->
  <meta property="og:title" content="<?php echo htmlspecialchars($loc['meta_title']); ?>">
  <meta property="og:description" content="<?php echo htmlspecialchars($loc['meta_description']); ?>">
  <meta property="og:image" content="https://everythingeasy.in/image/elogo.png">
  <meta property="og:url" content="https://everythingeasy.in/it-services/<?php echo $loc['slug']; ?>">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="EverythingEasy">

  <!-- Twitter -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?php echo htmlspecialchars($loc['meta_title']); ?>">
  <meta name="twitter:description" content="<?php echo htmlspecialchars($loc['meta_description']); ?>">
  <meta name="twitter:image" content="https://everythingeasy.in/image/elogo.png">

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
        "url": "https://everythingeasy.in",
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

  <!-- CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet" />
  <link href="https://everythingeasy.in/css/style.css" rel="stylesheet" />
</head>


<body>
  <div id="navbar-container"></div>
  <script src="js/navbar-loader.js"></script>
  <!-- Navigation Container -->

  <!-- Hero Section -->
  <section class="service-hero">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6">
          <div class="hero-content">
            <h5 class="text-warning mb-3">
              PROFESSIONAL WEBSITE DEVELOPMENT
            </h5>
            <h1 class="display-4 fw-bold mb-4 text-white">
              Website Development Services in <?php echo $loc['location_name']; ?>
            </h1>
            <p class="lead mb-4 text-white" style="font-size: 1.35rem;">
             Unleash the power of professional website development and take your business to a higher level with the best web development services in <?php echo $loc['location_name']; ?>. Our service develops successful websites for results with more than 10+ years of experience. Our expert team focuses on boosting your online presence and helping you dominate search results in <?php echo $loc['location_name']; ?>, turning visitors into valuable customers.
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
                    style="
                        border-radius: 8px;
                        width: 100%;
                        box-sizing: border-box;
                        padding: 10px 14px;
                        font-size: 14px;
                      " />
                </div>
                <div class="mb-2">
                  <input type="email" class="form-control" id="heroEmail" name="email" placeholder="Your Email*"
                    required style="
                        border-radius: 8px;
                        width: 100%;
                        box-sizing: border-box;
                        padding: 10px 14px;
                        font-size: 14px;
                      " />
                </div>
                <div class="mb-2">
                  <input type="tel" class="form-control" id="heroPhone" name="phone" placeholder="Phone Number*"
                    required style="
                        border-radius: 8px;
                        width: 100%;
                        box-sizing: border-box;
                        padding: 10px 14px;
                        font-size: 14px;
                      " />
                </div>
                <div class="mb-2">
                  <select class="form-select" id="heroService" name="service" required style="
                        border-radius: 8px;
                        width: 100%;
                        box-sizing: border-box;
                        padding: 10px 14px;
                        font-size: 14px;
                      ">
                    <option value="">Select Service*</option>
                    <option value="web-development">Web Development</option>
                    <option value="app-development">
                      Mobile App Development
                    </option>
                    <option value="cloud-solutions">Cloud Solutions</option>
                    <option value="seo">E-Commerce Development</option>
                    <option value="seo">UI/UX Design</option>
                    <option value="seo">Digital Marketing</option>
                    <option value="other">Other Services</option>
                  </select>
                </div>
                <div class="mb-3">
                  <textarea class="form-control" id="heroMessage" name="message" rows="2"
                    placeholder="Brief about your project (optional)" style="
                        border-radius: 8px;
                        width: 100%;
                        box-sizing: border-box;
                        padding: 10px 14px;
                        font-size: 14px;
                      "></textarea>
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
            <h2>500+</h2>
            <p>Projects Delivered</p>
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
            <h2>10+</h2>
            <p>Years Experience</p>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="metric-box">
            <h2>50+</h2>
            <p>Expert Team</p>
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
          Case Studies - Our Clients' Success Stories in <?php echo $loc['location_name']; ?>
        </h2>
        <p class="lead text-muted">
          We specialize in delivering exceptional and affordable website
          development services that drive significant growth for our clients
          in <?php echo $loc['location_name']; ?>. With extensive experience across various industries
          including hospitality, e-commerce, education, and local businesses,
          we have a proven track record of helping <?php echo $loc['location_name']; ?> companies achieve
          their online goals.
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
                EC
              </div>
              <div class="ms-3">
                <h5 class="mb-1">E-Commerce Platform</h5>
                <p class="text-muted mb-0">Online Retail Solutions</p>
              </div>
            </div>
            <table class="results-table">
              <thead>
                <tr>
                  <th>Metric</th>
                  <th>Improvement</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Page Load Speed</td>
                  <td><span class="badge-success">↑ 75%</span></td>
                </tr>
                <tr>
                  <td>System Uptime</td>
                  <td><span class="badge-success">99.9%</span></td>
                </tr>
                <tr>
                  <td>Transaction Processing</td>
                  <td><span class="badge-success">↑ 200%</span></td>
                </tr>
                <tr>
                  <td>Security Incidents</td>
                  <td><span class="badge-success">↓ 95%</span></td>
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
                HC
              </div>
              <div class="ms-3">
                <h5 class="mb-1">Healthcare Management System</h5>
                <p class="text-muted mb-0">Digital Health Solutions</p>
              </div>
            </div>
            <table class="results-table">
              <thead>
                <tr>
                  <th>Metric</th>
                  <th>Improvement</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Patient Data Processing</td>
                  <td><span class="badge-success">↑ 150%</span></td>
                </tr>
                <tr>
                  <td>Appointment Automation</td>
                  <td><span class="badge-success">90%</span></td>
                </tr>
                <tr>
                  <td>Data Security Compliance</td>
                  <td><span class="badge-success">100%</span></td>
                </tr>
                <tr>
                  <td>Staff Productivity</td>
                  <td><span class="badge-success">↑ 65%</span></td>
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
                FT
              </div>
              <div class="ms-3">
                <h5 class="mb-1">FinTech Application</h5>
                <p class="text-muted mb-0">Financial Technology Platform</p>
              </div>
            </div>
            <table class="results-table">
              <thead>
                <tr>
                  <th>Metric</th>
                  <th>Improvement</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Transaction Security</td>
                  <td><span class="badge-success">Bank-Grade</span></td>
                </tr>
                <tr>
                  <td>API Response Time</td>
                  <td><span class="badge-success">↓ 80%</span></td>
                </tr>
                <tr>
                  <td>User Authentication</td>
                  <td><span class="badge-success">Multi-Factor</span></td>
                </tr>
                <tr>
                  <td>Scalability</td>
                  <td><span class="badge-success">↑ 300%</span></td>
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
                ED
              </div>
              <div class="ms-3">
                <h5 class="mb-1">Education Portal</h5>
                <p class="text-muted mb-0">E-Learning Platform</p>
              </div>
            </div>
            <table class="results-table">
              <thead>
                <tr>
                  <th>Metric</th>
                  <th>Improvement</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Concurrent Users</td>
                  <td><span class="badge-success">10,000+</span></td>
                </tr>
                <tr>
                  <td>Video Streaming Quality</td>
                  <td><span class="badge-success">4K Ready</span></td>
                </tr>
                <tr>
                  <td>Mobile Accessibility</td>
                  <td><span class="badge-success">Cross-Platform</span></td>
                </tr>
                <tr>
                  <td>Content Delivery</td>
                  <td><span class="badge-success">↑ 120%</span></td>
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
          How We Can Help Your <?php echo $loc['location_name']; ?> Business Grow Online
        </h2>
        <p class="lead text-muted">
          Choosing the right website development partner in <?php echo $loc['location_name']; ?> is
          crucial for your business success. Here's what sets us apart and why
          <?php echo $loc['location_name']; ?> businesses trust us with their web development needs.
        </p>
      </div>

      <div class="row">
        <div class="col-lg-4 col-md-6">
          <div class="help-card">
            <i class="fas fa-shield-alt"></i>
            <h6>Security & Compliance</h6>
            <p>
              We prioritize your website security with industry-leading
              practices. Our websites are built with enterprise-grade security
              protocols, SSL certificates, and regular security updates to
              protect your business and customer data.
            </p>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="help-card">
            <i class="fas fa-chart-line"></i>
            <h6>Proven Track Record in <?php echo $loc['location_name']; ?></h6>
            <p>
              With over 10 years of experience and 500+ successful projects,
              we have established partnerships with leading companies across
              <?php echo $loc['location_name']; ?>. Our portfolio speaks for our expertise and commitment
              to excellence in website development.
            </p>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="help-card">
            <i class="fas fa-users"></i>
            <h6>Dedicated Support Team</h6>
            <p>
              Our expert team in <?php echo $loc['location_name']; ?> provides 24/7 support to ensure your
              website runs smoothly. We believe in building long-term
              relationships and being available whenever you need us for
              updates, maintenance, or emergency support.
            </p>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="help-card">
            <i class="fas fa-cogs"></i>
            <h6>Customized Website Solutions</h6>
            <p>
              Every <?php echo $loc['location_name']; ?> business is unique, and so are our websites. We
              take time to understand your specific requirements and create
              tailored web solutions that align with your business goals and
              budget constraints.
            </p>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="help-card">
            <i class="fas fa-rocket"></i>
            <h6>Modern Web Technologies</h6>
            <p>
              Stay ahead of the competition with the latest web technologies.
              We use responsive design, fast loading speeds, SEO optimization,
              and modern frameworks to build scalable, future-proof websites
              for your <?php echo $loc['location_name']; ?> business.
            </p>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="help-card">
            <i class="fas fa-handshake"></i>
            <h6>Transparent Communication</h6>
            <p>
              We believe in complete transparency. Regular progress reports,
              clear documentation, and open communication channels ensure
              you're always informed about your website development status and
              can provide feedback at any stage.
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
          Why Choose EverythingEasy Technology for Website Development in
          <?php echo $loc['location_name']; ?>
        </h2>
        <p class="lead text-muted">
          Finding the right website development partner in <?php echo $loc['location_name']; ?> can make
          or break your online success. Here's why <?php echo $loc['location_name']; ?> businesses choose
          EverythingEasy Technology.
        </p>
      </div>

      <div class="row">
        <div class="col-lg-6">
          <div class="choose-card">
            <h5>
              <i class="fas fa-trophy me-2"></i> Local Expertise in <?php echo $loc['location_name']; ?>
            </h5>
            <p class="mb-0">
              Our team comprises certified web development professionals with
              deep understanding of the <?php echo $loc['location_name']; ?> market. We stay updated with
              the latest web trends to provide you with cutting-edge solutions
              that give your <?php echo $loc['location_name']; ?> business a competitive advantage.
            </p>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="choose-card">
            <h5>
              <i class="fas fa-dollar-sign me-2"></i> Affordable Website
              Development
            </h5>
            <p class="mb-0">
              We offer premium website development services at competitive
              prices in <?php echo $loc['location_name']; ?>. Our flexible pricing models and efficient
              development processes ensure you get maximum value for your
              investment without compromising on quality.
            </p>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="choose-card">
            <h5><i class="fas fa-clock me-2"></i> On-Time Delivery</h5>
            <p class="mb-0">
              We understand the importance of deadlines for <?php echo $loc['location_name']; ?>
              businesses. Our agile methodology and experienced project
              managers ensure timely delivery of your website projects without
              sacrificing quality or functionality.
            </p>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="choose-card">
            <h5>
              <i class="fas fa-sync-alt me-2"></i> Ongoing Website Maintenance
            </h5>
            <p class="mb-0">
              Our relationship doesn't end at website launch. We provide
              comprehensive maintenance and support services to ensure your
              website continues to perform optimally and evolves with your
              <?php echo $loc['location_name']; ?> business needs.
            </p>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="choose-card">
            <h5><i class="fas fa-star me-2"></i> Client Testimonials</h5>
            <p class="mb-0">
              Don't just take our word for it. Our <?php echo $loc['location_name']; ?> clients' success
              stories and testimonials reflect our commitment to excellence.
              We take pride in the long-term partnerships we've built based on
              trust and results.
            </p>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="choose-card">
            <h5>
              <i class="fas fa-laptop-code me-2"></i> Modern Web Technologies
            </h5>
            <p class="mb-0">
              We work with the latest and most reliable web technologies
              including React, WordPress, PHP, HTML5, CSS3, and more. Our
              tech-agnostic approach ensures we choose the right tools for
              your specific website requirements in <?php echo $loc['location_name']; ?>.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="boost-cta">
    <div class="container">
      <h2>Ready to Launch Your Website in <?php echo $loc['location_name']; ?>?</h2>
      <p>
        Get affordable and 100% result-oriented website development services
        with the latest technologies and best practices. Let us help you boost
        your online presence and stand out in <?php echo $loc['location_name']; ?> with innovative web
        solutions.
      </p>
      <a href="tel:+1234567890" class="btn btn-warning btn-lg me-3">
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
          Website Development in <?php echo $loc['location_name']; ?>: Frequently Asked Questions
        </h2>
        <p class="lead text-muted">
          Welcome to our FAQ section, where we aim to provide answers to
          common questions about our website development services in <?php echo $loc['location_name']; ?>.
          If you have a question that's not covered here, please feel free to
          reach out to us directly.
        </p>
      </div>

      <div class="row">
        <div class="col-lg-8 mx-auto">
          <div class="faq-item" onclick="toggleFaq(this)">
            <h6>
              How much does website development cost in <?php echo $loc['location_name']; ?>?
              <i class="fas fa-chevron-down"></i>
            </h6>
            <div class="faq-answer">
              <p>
                The cost of website development in <?php echo $loc['location_name']; ?> varies depending
                on project scope, complexity, features, and design
                requirements. We offer flexible pricing models from basic
                business websites to advanced e-commerce platforms. After
                understanding your requirements, we provide a detailed quote
                with transparent pricing and no hidden costs.
              </p>
            </div>
          </div>

          <div class="faq-item" onclick="toggleFaq(this)">
            <h6>
              How long does it take to build a website in <?php echo $loc['location_name']; ?>?
              <i class="fas fa-chevron-down"></i>
            </h6>
            <div class="faq-answer">
              <p>
                Website development timelines depend on complexity and
                requirements. A simple business website might take 2-4 weeks,
                while e-commerce or custom applications can take 6-12 weeks.
                We use agile methodology for iterative delivery, ensuring you
                see progress throughout the development cycle. We work closely
                with <?php echo $loc['location_name']; ?> businesses to meet their launch deadlines.
              </p>
            </div>
          </div>

          <div class="faq-item" onclick="toggleFaq(this)">
            <h6>
              What technologies do you use for website development?
              <i class="fas fa-chevron-down"></i>
            </h6>
            <div class="faq-answer">
              <p>
                We work with a wide range of modern web technologies including
                WordPress, React, HTML5, CSS3, JavaScript, PHP, and responsive
                frameworks like Bootstrap. We also specialize in e-commerce
                platforms like WooCommerce and Shopify. We choose technologies
                based on your specific needs and budget in <?php echo $loc['location_name']; ?>.
              </p>
            </div>
          </div>

          <div class="faq-item" onclick="toggleFaq(this)">
            <h6>
              Do you provide ongoing support after website launch?
              <i class="fas fa-chevron-down"></i>
            </h6>
            <div class="faq-answer">
              <p>
                Yes! We offer comprehensive post-launch support and
                maintenance services for all our <?php echo $loc['location_name']; ?> clients. This
                includes bug fixes, security updates, content updates,
                performance optimization, and 24/7 technical support. We
                believe in building long-term partnerships with <?php echo $loc['location_name']; ?>
                businesses.
              </p>
            </div>
          </div>

          <div class="faq-item" onclick="toggleFaq(this)">
            <h6>
              Will my website be mobile-friendly and SEO optimized?
              <i class="fas fa-chevron-down"></i>
            </h6>
            <div class="faq-answer">
              <p>
                Absolutely! All our websites are fully responsive and
                mobile-friendly, working perfectly on all devices. We also
                include basic SEO optimization (meta tags, structured data,
                fast loading, mobile optimization) to help your <?php echo $loc['location_name']; ?>
                business rank better in search engines.
              </p>
            </div>
          </div>

          <div class="faq-item" onclick="toggleFaq(this)">
            <h6>
              How do you ensure website security?
              <i class="fas fa-chevron-down"></i>
            </h6>
            <div class="faq-answer">
              <p>
                Security is our top priority for all <?php echo $loc['location_name']; ?> clients. We
                implement SSL certificates, secure hosting, regular backups,
                security plugins, firewall protection, and regular security
                updates. All our websites follow industry-standard security
                practices to protect your business and customer data.
              </p>
            </div>
          </div>

          <div class="faq-item" onclick="toggleFaq(this)">
            <h6>
              Do you work with small businesses in <?php echo $loc['location_name']; ?>?
              <i class="fas fa-chevron-down"></i>
            </h6>
            <div class="faq-answer">
              <p>
                Yes! We work with businesses of all sizes in <?php echo $loc['location_name']; ?> – from
                small local shops to large enterprises. For small businesses,
                we offer affordable starter websites, scalable solutions, and
                cost-effective packages. We understand the unique needs of
                <?php echo $loc['location_name']; ?>'s local business community.
              </p>
            </div>
          </div>

          <div class="faq-item" onclick="toggleFaq(this)">
            <h6>
              What is your website development process?
              <i class="fas fa-chevron-down"></i>
            </h6>
            <div class="faq-answer">
              <p>
                We follow a proven process: Consultation & Planning → Design &
                Wireframing → Development → Content Integration → Testing & QA
                → Launch → Training & Support. You'll have regular updates and
                opportunities for feedback throughout. We use project
                management tools to keep <?php echo $loc['location_name']; ?> clients informed at every
                stage.
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
          Our Specialized Website Development Services in <?php echo $loc['location_name']; ?>
        </h2>
        <p class="lead text-muted">
          Comprehensive web solutions tailored to <?php echo $loc['location_name']; ?> businesses
        </p>
      </div>

      <div class="service-grid">
        <a href="services.html#web-development" class="service-card-item">
          <i class="fas fa-laptop-code"></i>
          <h6>Web Development Services</h6>
        </a>

        <a href="services.html#mobile-apps" class="service-card-item">
          <i class="fas fa-mobile-alt"></i>
          <h6>Mobile App Development</h6>
        </a>

        <a href="services.html#cloud" class="service-card-item">
          <i class="fas fa-cloud"></i>
          <h6>Cloud Solutions</h6>
        </a>

        <a href="services.html#ecommerce" class="service-card-item">
          <i class="fas fa-shopping-cart"></i>
          <h6>E-Commerce Development</h6>
        </a>

        <a href="services.html#ai" class="service-card-item">
          <i class="fas fa-brain"></i>
          <h6>AI & Machine Learning</h6>
        </a>

        <a href="services.html#devops" class="service-card-item">
          <i class="fas fa-server"></i>
          <h6>DevOps Services</h6>
        </a>

        <a href="services.html#cybersecurity" class="service-card-item">
          <i class="fas fa-shield-alt"></i>
          <h6>Cybersecurity Solutions</h6>
        </a>

        <a href="services.html#ui-ux" class="service-card-item">
          <i class="fas fa-paint-brush"></i>
          <h6>UI/UX Design</h6>
        </a>

        <a href="services.html#database" class="service-card-item">
          <i class="fas fa-database"></i>
          <h6>Database Management</h6>
        </a>

        <a href="services.html#api" class="service-card-item">
          <i class="fas fa-plug"></i>
          <h6>API Development</h6>
        </a>

        <a href="services.html#blockchain" class="service-card-item">
          <i class="fas fa-link"></i>
          <h6>Blockchain Solutions</h6>
        </a>

        <a href="services.html#iot" class="service-card-item">
          <i class="fas fa-microchip"></i>
          <h6>IoT Development</h6>
        </a>

        <a href="services.html#consulting" class="service-card-item">
          <i class="fas fa-comments"></i>
          <h6>IT Consulting</h6>
        </a>

        <a href="services.html#qa" class="service-card-item">
          <i class="fas fa-check-double"></i>
          <h6>QA & Testing Services</h6>
        </a>

        <a href="services.html#maintenance" class="service-card-item">
          <i class="fas fa-tools"></i>
          <h6>Maintenance & Support</h6>
        </a>

        <a href="services.html#digital-transformation" class="service-card-item">
          <i class="fas fa-chart-line"></i>
          <h6>Digital Transformation</h6>
        </a>
      </div>
    </div>
  </section>

  <!-- Why Choose Section - Detailed -->
  <section class="py-5 bg-white">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="fw-bold">
          Website Development Company in <?php echo $loc['location_name']; ?>: Why Choose EverythingEasy Technology?
        </h2>
      </div>

      <div class="mb-5">
        <p class="lead" style="line-height: 1.9; color: #6c757d">
          Web development plays an important role in enhancing one's visibility or online presence. In that case,
          finding the most appropriate website development company in <?php echo $loc['location_name']; ?> can either make or break your business.
          This is what we at EverythingEasy Technology proudly do: we dive into the intricacies of web development and
          how it avails the growth of your business.
        </p>

        <p style="line-height: 1.9; color: #6c757d">
          Consequently, we intend to develop processes with specific characteristics of each client company as a
          <strong> website development company in <?php echo $loc['location_name']; ?>.</strong> Our people have both the technical know-how and the
          imagination required to make your site perform at the top of the search engine result pages. Driving organic
          traffic, enhancing visibility, and most especially turning the clicks into repeat customers, has been the core
          objective for many years of practice.

        </p>

        <p style="line-height: 1.9; color: #6c757d">
          What makes us different, however, is how we approach this differently: we pursue transparency and strive to provide results that are quantifiable. EverythingEasy Technology applies the most recent analytics and technologies to offer screenshots of analysis and implementable roadmaps. Services are offered to all kinds of businesses: whether just coming up or fully functional, we provide services to different types of businesses.
        </p>

        <p style="line-height: 1.9; color: #6c757d">
         Whether it's responsive design, e-commerce development, or custom web applications, we take a holistic approach towards each of these strategies to ensure long-term success for your brand. Try EverythingEasy Technology, a reliable <strong> website development company in <?php echo $loc['location_name']; ?> </strong>, and watch your business grow.

        </p>
      </div>

      <div class="mb-5">
        <h3 class="fw-bold mb-4">
          Finding the Best Website Development Service Provider in <?php echo $loc['location_name']; ?>
        </h3>

        <p style="line-height: 1.9; color: #6c757d">
          While looking towards <?php echo $loc['location_name']; ?> for any <strong>website development service provider </strong>, finding a specialist who is able to appreciate your business objectives is very important. EverythingEasy Technology acts as the perfect partner for making any online presence better.
        </p>

        <p style="line-height: 1.9; color: #6c757d">
          Be it a<strong> Website Development Service near me</strong> or custom solutions, we have everything one may need: from UI/UX design and responsive development to e-commerce solutions. Thanks to our local know-how in <?php echo $loc['location_name']; ?>, we make sure the targeting of your business covers <?php echo $loc['location_name']; ?> and its outskirts.
        </p>

        <p style="line-height: 1.9; color: #6c757d">
          We are here to ensure tested approaches, with quantifiable results, to see that your business attains set goals in the sphere of higher conversions and growth.
        </p>
      </div>

      <div class="mb-5">
        <h3 class="fw-bold mb-4">
         Hire the Best  Web Developer in <?php echo $loc['location_name']; ?> for Your Business Growth
        </h3>

        <p style="line-height: 1.9; color: #6c757d">
          In today's scenario, having an online presence isn't just more imperative but a necessity. Be it just starting as a small business or running an established one, the <strong>best web developer in <?php echo $loc['location_name']; ?></strong> best web developer in <?php echo $loc['location_name']; ?> will make all the difference between success and failure for your brand. Everything Easy has built excellence in this area by providing result-oriented <strong>website development services near me</strong> according to specific needs of the business.

        </p>

        <p style="line-height: 1.9; color: #6c757d">
         A local web development expert will know how the <?php echo $loc['location_name']; ?> market is, how people search for services, and what the audience prefers for that particular area. At EverythingEasy Technology, the focus is also on establishing local businesses and ensuring your website performs at the top for searches related to services offered in <?php echo $loc['location_name']; ?>. It ranges from site speed and mobile responsiveness to SEO-friendly structure-just anything so that your business gets viewed by customers around you.
        </p>
      </div>

      <div class="mb-5">
        <h3 class="fw-bold mb-4">
          Your trusted web development expert in <?php echo $loc['location_name']; ?>
        </h3>

        <p style="line-height: 1.9; color: #6c757d">
          With years of experience, EverythingEasy Technology has emerged as a <strong>website development company in <?php echo $loc['location_name']; ?></strong> . Our primary focus is to enhance organic traffic and user engagement while creating conversion-driven designs which are structured for analysis and growth.
        </p>

        <p style="line-height: 1.9; color: #6c757d">
          If you are searching for where I can find  <strong>website development service in <?php echo $loc['location_name']; ?></strong>, we provide clear functional solutions. This means a result-oriented approach with responsive design, modern frameworks, content management systems, and technical optimization. We are committed to helping your <?php echo $loc['location_name']; ?> business achieve higher return on investment than what it is getting currently.
        </p>

        <p style="line-height: 1.9; color: #6c757d">
        Let EverythingEasy Technology help you enhance your business' website and win in the digital space. Call us now for success!
        </p>
      </div>

      <div class="mb-5">
        <h3 class="fw-bold mb-4">
          Why EverythingEasy Technology is the Best Website Development Agency in <?php echo $loc['location_name']; ?>
        </h3>

        <p style="line-height: 1.9; color: #6c757d">
          It's now very pertinent to choose an appropriate <strong>website development agency in <?php echo $loc['location_name']; ?></strong> for enhancing one's online existence. Among the agencies, EverythingEasy Technology proves to be one of the most trusted and efficient ones. Also, personalized and result-oriented strategies bring in business growth and better visibility on the web.
        </p>

        <h5 class="fw-bold mt-4 mb-3" style="color: #1e3c72">
          Benefits of a Local Website Development Agency in <?php echo $loc['location_name']; ?>
        </h5>

        <p style="line-height: 1.9; color: #6c757d">
         As the <strong>best website development company in <?php echo $loc['location_name']; ?></strong>  , we understand the requirements that businesses have while working within the city. We have customized web development services, starting from helping small businesses make a proper online presence to larger enterprises improving their digital infrastructure. Our web developer near me works on making the best use of design, functionality, and optimization so that your website's performance is enhanced.

        </p>
      </div>

      <div class="mb-5">
        <h3 class="fw-bold mb-4">
          Why Developing Websites Locally Matters for Businesses in <?php echo $loc['location_name']; ?>
        </h3>

        <p style="line-height: 1.9; color: #6c757d">
         Having a full-fledged online presence is a must for any <?php echo $loc['location_name']; ?>-based company in today's digitized economy. The whole concept of local website development actually denotes the creation of websites that are optimized for the local market of <?php echo $loc['location_name']; ?>, hence assuring better targeting and higher conversion rates from the local customers.
        </p>

        <h5 class="fw-bold mt-4 mb-3" style="color: #1e3c72">
         Recognizing the Need for Professional Website Development in <?php echo $loc['location_name']; ?>
        </h5>

        <p style="line-height: 1.9; color: #6c757d">
         <strong> Professional website development in <?php echo $loc['location_name']; ?> </strong>refers to creating a site for representing your business effectively in the local market. Location-based optimization helps in making it easier for companies operating in <?php echo $loc['location_name']; ?> to appear in searches like 'best services in <?php echo $loc['location_name']; ?>' or '<?php echo $loc['location_name']; ?> businesses'. With a well-developed website, one is sure to have more footfall at their physical locations and better quality leads.
        </p>

        <p style="line-height: 1.9; color: #6c757d">
         This means that with modern web development strategies, your business is going to be better placed in local searches and found much more quickly by customers. Emphasizing responsive design, fast loading speeds, mobile optimization, and user-friendly interfaces will definitely leave a mark on the local audience.
        </p>
      </div>

      <div class="mb-5">
        <h3 class="fw-bold mb-4">
          Important Website Development Services for <?php echo $loc['location_name']; ?> Businesses
        </h3>

        <p style="line-height: 1.9; color: #6c757d">
         During recent years, more businesses in <?php echo $loc['location_name']; ?> have come to understand the importance of having professional website development. Primary services include custom website design, e-commerce development, content management systems, and mobile app integrations. Rich in content, modern in design, and technically optimized to provide a better user experience.
    By emphasizing these aspects, you will ensure that your business ranks at the top of the search results, which would further be helpful in increasing customer engagement, online sales, and brand recognition in <?php echo $loc['location_name']; ?>.
        </p>

        <p style="line-height: 1.9; color: #6c757d">
          Website Development Companies in <?php echo $loc['location_name']; ?>: What Sets Us Apart
      There are numerous website development firms around me, but Everything Easy stands out due to the fact that we don't just build a website; we craft digital experiences. We aim to create long-term strategies that achieve success. Our professionals work from planning and design through development and optimization to make sure that your website delivers results.
        </p>
      </div>

      <div class="mb-5">
        <h3 class="fw-bold mb-4">
          Finding Website Development Services Near Me
        </h3>

        <p style="line-height: 1.9; color: #6c757d">
        While choosing the <strong> best website development company in <?php echo $loc['location_name']; ?> </strong>, one needs to look out for a company having experience in contemporary web technologies and with deep insights into your business. It will help small business entrepreneurs and large enterprises alike to increase their presence, drive traffic, and generate leads.
        </p>
      </div>

      <div class="mb-5">
        <h3 class="fw-bold mb-4">
          Finding Website Development Services Near Me
        </h3>

       

        <p style="line-height: 1.9; color: #6c757d">
          Professional web development ensures that the results will be noticeable and effective, impacting your business's success. A good website can ensure the growth of your <?php echo $loc['location_name']; ?> business in competitive markets by focusing on improving online visibility and providing seamless user experiences. How to Choose the Right Web Development Consultant for Your Business in <?php echo $loc['location_name']; ?> In this digital age, when everything has reached a tipping point, the need to approach the best web development consultant in <?php echo $loc['location_name']; ?> becomes apparent for any firm in <?php echo $loc['location_name']; ?> desiring increased visibility. A web development consultant will guarantee to give your website the much-required skills and techniques to edge it ahead of your competitors, increase its visitor numbers, and achieve more sales due to increased conversions. Even small businesses operating out of <?php echo $loc['location_name']; ?> or around it have plenty of reasons to hire the services of a web development consultant for professional services in their organization. They will study the market along with your audience and competitors to ensure your website not only looks great but performs exceptionally well. Best Website Development Services in <?php echo $loc['location_name']; ?>: What to Look For When it concerns the best website development services near me, one should ensure that the selected consultant or agency is knowledgeable about modern web technologies and chalks out strategies to achieve long-term results. Factors that need to be considered include: Portfolio and experience are the things that any reliable web development consultant should have in store while delivering websites to other companies. Customized Solutions: No two businesses are similar. Better services provide strategies designed to meet your specific business niche. Transparency: The contractor shall practice open communication and shall apprise the updates regarding the project progress. Modern Technology Stack: Ensure they use current frameworks, responsive design, and follow best practices. Post-launch support: The reason behind long-term success is ongoing maintenance and support. Conclusion Working with EverythingEasy Technology, a professional website development company in <?php echo $loc['location_name']; ?>, can facilitate positive results for your business. Be it custom web development, e-commerce solutions, or website redesign services in <?php echo $loc['location_name']; ?>; partnering with qualified professionals will help strategize aptly to improve your online presence, attract quality traffic, and expand your business. A web development consultant for your <?php echo $loc['location_name']; ?> business aspires to success by building a plan based on market characteristics, optimizing for local searches, and practicing clear communication.
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
            Ready to Start Your Website Project in <?php echo $loc['location_name']; ?>?
          </h3>
          <p class="lead text-muted mb-lg-0">
            Let's discuss how we can help transform your <?php echo $loc['location_name']; ?> business
            with an innovative website. Contact us today for a free
            consultation and quote.
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
  <div id="footer-container"></div>

  <!-- Bootstrap JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

  <!-- Navbar Loader -->
  <!-- <script src="https://everythingeasy.in/js/navbar-loader.js"></script> -->

  <!-- Footer Loader -->
  <!-- <script src="https://everythingeasy.in/js/footer-loader.js"></script> -->

  <!-- FAQ Toggle Script -->
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const navbar = document.getElementById("navbar-container");
      if (!navbar) return;

      fetch("https://everythingeasy.in/navbar.html")
        .then((res) => {
          if (!res.ok) throw new Error("Navbar not found");
          return res.text();
        })
        .then((data) => {
          navbar.innerHTML = data;
          console.log("Navbar loaded successfully");
        })
        .catch((err) => console.error("Navbar Load Error:", err));
    });

    document.addEventListener("DOMContentLoaded", () => {
      const footer = document.getElementById("footer-container");
      if (!footer) return;

      fetch("https://everythingeasy.in/footer.html")
        .then((res) => {
          if (!res.ok) throw new Error("Footer not found");
          return res.text();
        })
        .then((data) => {
          footer.innerHTML = data;
          console.log("Footer loaded successfully");
        })
        .catch((err) => console.error("Footer Load Error:", err));
    });

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
            formData.get("message") || "Quick quote request from homepage",
        };

        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML =
          '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
        submitBtn.disabled = true;

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
            // Reset button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;

            if (result.success) {
              // Show success message
              formResult.className = "mt-2 alert alert-success";
              formResult.innerHTML =
                '<i class="fas fa-check-circle me-2"></i>' + result.message;
              formResult.classList.remove("d-none");

              // Reset form
              form.reset();
              form.classList.remove("was-validated");
            } else {
              // Show error message
              formResult.className = "mt-2 alert alert-danger";
              formResult.innerHTML =
                '<i class="fas fa-exclamation-circle me-2"></i>' +
                result.message;
              formResult.classList.remove("d-none");
            }

            // Hide message after 5 seconds
            setTimeout(() => {
              formResult.classList.add("d-none");
            }, 5000);
          })
          .catch((error) => {
            console.error("Error:", error);

            // Reset button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;

            // Show error message
            formResult.className = "mt-2 alert alert-danger";
            formResult.innerHTML =
              '<i class="fas fa-exclamation-circle me-2"></i>An error occurred. Please try again later.';
            formResult.classList.remove("d-none");

            // Hide message after 5 seconds
            setTimeout(() => {
              formResult.classList.add("d-none");
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