<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes"
    />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="default" />
    <meta name="theme-color" content="#0066cc" />
    <title>Portfolio - Everything Easy</title>
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap"
      rel="stylesheet"
    />
    <link href="css/style.css" rel="stylesheet" />
  </head>
  <body>
    <?php require_once 'navbar.php'; ?>
    <!-- Navigation Container -->
    <!-- Navigation Container -->

    <!-- Page Header -->
    <section class="hero-portfolio" style="padding-top: 120px !important">
      <div class="container">
        <div class="row align-items-center min-vh-50">
          <div class="col-lg-6">
            <div class="hero-content">
              <span class="badge bg-warning text-dark px-3 py-2 mb-3">
                <i class="fas fa-rocket me-1"></i>Our Work
              </span>
              <h1 class="display-4 fw-bold text-dark mb-4">
                Creative
                <span class="text-primary">Portfolio</span>
              </h1>
              <p class="lead text-muted mb-4">
                Discover our latest projects and innovative solutions that help
                businesses grow and succeed in the digital world.
              </p>
              <div class="stats-row d-flex flex-wrap gap-4 mb-4">
                <div class="stat-item">
                  <h3 class="fw-bold text-primary mb-0">50+</h3>
                  <small class="text-muted">Projects</small>
                </div>
                <div class="stat-item">
                  <h3 class="fw-bold text-success mb-0">99%</h3>
                  <small class="text-muted">Success Rate</small>
                </div>
                <!-- <div class="stat-item">
                  <h3 class="fw-bold text-warning mb-0">100%</h3>
                  <small class="text-muted">Happy Clients</small>
                </div> -->
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="hero-image text-center">
              <div class="image-stack">
                <img
                  src="https://images.unsplash.com/photo-1559028006-448665bd7c7f?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&h=400&q=80"
                  alt="Portfolio"
                  class="img-fluid rounded shadow-lg portfolio-img-1"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Filter Tabs -->
    <!-- <section class="py-4 bg-light">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="filter-tabs">
              <nav
                class="nav nav-pills justify-content-center flex-wrap"
                id="portfolio-tabs"
                role="tablist"
              >
                <button
                  class="nav-link active filter-tab me-2 mb-2"
                  data-filter="all"
                  type="button"
                >
                  <i class="fas fa-th-large me-2"></i>All Work
                </button>
                <button
                  class="nav-link filter-tab me-2 mb-2"
                  data-filter="web"
                  type="button"
                >
                  <i class="fas fa-globe me-2"></i>Web Apps
                </button>
                <button
                  class="nav-link filter-tab me-2 mb-2"
                  data-filter="mobile"
                  type="button"
                >
                  <i class="fas fa-mobile-alt me-2"></i>Mobile
                </button>
                <button
                  class="nav-link filter-tab me-2 mb-2"
                  data-filter="design"
                  type="button"
                >
                  <i class="fas fa-paint-brush me-2"></i>Design
                </button>
                <button
                  class="nav-link filter-tab me-2 mb-2"
                  data-filter="ecommerce"
                  type="button"
                >
                  <i class="fas fa-shopping-cart me-2"></i>E-commerce
                </button>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </section> -->

    <!-- Portfolio Masonry Grid -->
    <section class="py-5 portfolio-section">
      <div class="container">
        <div class="row g-4 portfolio-masonry" id="portfolio-grid">
          <!-- Project 1 -->
          <div
            class="col-12 col-lg-6 portfolio-item"
            data-category="web design ecommerce"
          >
            <div class="project-card">
              <div class="project-image">
                <img
                  src="https://i.ibb.co/Zp25JnNs/Screenshot-2025-12-09-at-1-19-05-PM.png"
                  alt="CEUTrainers Logo"
                  class="img-fluid"
                />
                <div class="project-overlay">
            <script>
            const projects = [
           
              {
                img: "images/ceu.webp",
                alt: "CEUTrainers Logo",
                category: "Online Learning",
                title: "CEUTrainers Online Courses",
                desc: "Access 5,800+ online courses, certificates, and degrees from top universities and companies. Learn without limits and advance your career remotely.",
                tech: ["Webinars", "Remote Learning", "Professional Certificates"],
                link: "https://ceuservices.com/"
              },
             
              {
                img: "images/5.webp",
                alt: "PowerStroke Drive Platform",
                category: "Auto Parts Marketplace",
                title: "PowerStroke Drive - Used Engines & Transmissions",
                desc: "Buy quality tested, low-mileage used engines and transmissions with nationwide shipping and warranty. Save up to 70% compared to new parts. Get a free quote today!",
                tech: ["Used Engines", "Transmissions", "Nationwide Shipping"],
                link: "https://powerstrokedrive.com/"
              },
              {
                img: "images/6.webp",
                alt: "Rivaaz Films",
                category: "Music & Video Distribution",
                title: "Rivaaz Films - Maximize Your Music's Reach",
                desc: "Distribute your music and videos globally with Rivaaz Films. We offer music publishing, video distribution, lyrics distribution, and social media growth services.",
                tech: ["Music Distribution", "Video Distribution", "Social Media Growth"],
                link: "https://rivaazfilms.com/"
              },
                 {
                img: "images/1.webp",
                alt: "Clothing E-commerce Platform",
                category: "E-commerce Platform",
                title: "Ethnic Fashion Store",
                desc: "Clothing E-commerce Platform is an online ethnic fashion store offering a wide range of sarees, kurtis, frocks, and suits. Enjoy fast delivery, free shipping, secure checkout, and easy replacements. Shop by category and discover feature products with exclusive offers.",
                tech: ["E-commerce", "Fast Delivery", "Free Shipping", "Secure Checkout", "Easy Replacements"],
             
              },
              {
                img: "images/4.webp",
                alt: "Truetop Roofing Ltd",
                category: "Roofing & Property Maintenance",
                title: "Truetop Roofing Ltd",
                desc: "Reliable roofing and property maintenance services in Hounslow. Tailored solutions, innovative techniques, and end-to-end service for all your roofing and guttering needs.",
                tech: ["Roofing", "Guttering", "Property Maintenance"],
                link: "https://truetoproofingltd.com/"
              },
              {
                img: "images/3.webp",
                alt: "RealTimeVoice News Portal",
                category: "News Portal",
                title: "RealTimeVoice",
                desc: "RealTimeVoice delivers real-time news, updates, and in-depth coverage across politics, business, sports, entertainment, and more. Stay informed with trusted journalism and instant alerts.",
                tech: ["Live Updates", "Multi-Category", "Responsive Design"],
                link: "https://realtimevoice.in/"
              },
              // {
              //   img: "https://i.ibb.co/3myYNDbb/Screenshot-2025-12-09-at-1-54-59-PM.png",
              //   alt: "Easy Web Apps USA",
              //   category: "Web & Mobile Solutions",
              //   title: "Easy Web Apps USA",
              //   desc: "Custom web and mobile app development for businesses in the USA. From e-commerce to enterprise solutions, Easy Web Apps USA delivers modern, scalable, and user-friendly platforms tailored to your needs.",
              //   tech: ["Web Apps", "Mobile Apps", "E-commerce"],
              //   link: "https://easywebappsusa.com/"
              // },
              //  {
              //   img: "https://i.ibb.co/1t9fn816/Screenshot-2025-12-09-at-1-22-29-PM.png",
              //   alt: "Future Properties Platform",
              //   category: "Real Estate Platform",
              //   title: "Future Properties - Find Your Dream Home",
              //   desc: "Discover handpicked properties, trending listings, and expert agents in Dehradun. Evaluate, connect, and close deals easily with our user-friendly real estate platform.",
              //   tech: ["Property Listings", "Agent Support", "Online Inquiry"],
              //   link: "https://futureproperties.org/"
              // },
            ];

            const portfolioGrid = document.getElementById('portfolio-grid');
            portfolioGrid.innerHTML = projects.map(project => `
              <div class="col-12 col-md-6 portfolio-item mb-4" data-category="web">
                <div class="project-card">
                  <div class="project-image">
                    <img src="${project.img}" alt="${project.alt}" class="img-fluid" />
                    <div class="project-overlay">
                      <div class="project-info">
                        <span class="project-category">${project.category}</span>
                        <h4 class="project-title">${project.title}</h4>
                        <p class="project-desc">${project.desc}</p>
                        <div class="project-tech">
                          ${project.tech.map(tag => `<span class="tech-tag">${tag}</span>`).join('')}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            `).join('') +
            `<div class='col-12 text-center mt-4'><p class='lead text-muted'>We have delivered many more professional projects. Contact us to see our full portfolio.</p></div>`;
            </script>
                role="status"
              ></span>
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-gradient-primary text-white">
      <div class="container">
        <div class="row">
          <div class="col-lg-8 mx-auto text-center">
            <h2 class="fw-bold mb-4">Ready to Start Your Project?</h2>
            <p class="lead mb-4">
              Let's discuss how we can bring your vision to life with our
              expertise and creativity
            </p>
            <div class="cta-buttons">
              <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center gap-3 w-100">
                <a href="/#contact" class="btn btn-warning btn-lg mx-sm-2">
                  <i class="fas fa-comments me-2"></i>Get in Touch
                </a>
                <a href="/#quote" class="btn btn-outline-light btn-lg mx-sm-2">
                  <i class="fas fa-calculator me-2"></i>Get Free Quote
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer Container -->
    <?php require_once 'footer.php'; ?>
    <div id="whatsapp-container"></div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

    <script src="js/script.js"></script>
    <script src="js/whatsapp-loader.js"></script>
  </body>
</html>
