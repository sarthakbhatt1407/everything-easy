// Everything Easy - IT Solutions Company JavaScript

document.addEventListener("DOMContentLoaded", function () {
  // Initialize all functionality
  initNavbar();
  initCounters();
  initAnimations();
  initContactForm();
  initQuoteForm();
  initSmoothScrolling();
  initPricingToggle();

  // Navbar scroll effect
  function initNavbar() {
    const navbar = document.querySelector(".navbar");

    window.addEventListener("scroll", function () {
      if (window.scrollY > 50) {
        navbar.classList.add("scrolled");
        navbar.style.background = "rgba(255, 255, 255, 0.95)";
        navbar.style.backdropFilter = "blur(10px)";
      } else {
        navbar.classList.remove("scrolled");
        navbar.style.background = "rgba(255, 255, 255, 1)";
        navbar.style.backdropFilter = "none";
      }
    });
  }

  // Pricing toggle functionality
  function initPricingToggle() {
    const monthlyToggle = document.getElementById("monthly");
    const yearlyToggle = document.getElementById("yearly");

    if (monthlyToggle && yearlyToggle) {
      monthlyToggle.addEventListener("change", function () {
        if (this.checked) {
          togglePricing("monthly");
        }
      });

      yearlyToggle.addEventListener("change", function () {
        if (this.checked) {
          togglePricing("yearly");
        }
      });
    }
  }

  function togglePricing(period) {
    const monthlyPrices = document.querySelectorAll(".price-monthly");
    const yearlyPrices = document.querySelectorAll(".price-yearly");

    if (period === "monthly") {
      monthlyPrices.forEach((price) => {
        price.classList.remove("d-none");
      });
      yearlyPrices.forEach((price) => {
        price.classList.add("d-none");
      });
    } else {
      monthlyPrices.forEach((price) => {
        price.classList.add("d-none");
      });
      yearlyPrices.forEach((price) => {
        price.classList.remove("d-none");
      });
    }
  }

  // Counter animation
  function initCounters() {
    const counters = document.querySelectorAll(".counter");
    const observerOptions = {
      threshold: 0.7,
    };

    const observer = new IntersectionObserver(function (entries) {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const counter = entry.target;
          const target = parseInt(counter.getAttribute("data-target"));
          animateCounter(counter, target);
          observer.unobserve(counter);
        }
      });
    }, observerOptions);

    counters.forEach((counter) => {
      observer.observe(counter);
    });
  }

  function animateCounter(element, target) {
    let current = 0;
    const increment = target / 100;
    const timer = setInterval(() => {
      current += increment;
      if (current >= target) {
        current = target;
        clearInterval(timer);
      }
      element.textContent = Math.floor(current);
    }, 20);
  }

  // Scroll animations
  function initAnimations() {
    const animatedElements = document.querySelectorAll(
      ".service-card, .team-card, .testimonial-card, .pricing-card"
    );

    const observer = new IntersectionObserver(
      function (entries) {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = "0";
            entry.target.style.transform = "translateY(30px)";

            setTimeout(() => {
              entry.target.style.transition = "all 0.6s ease";
              entry.target.style.opacity = "1";
              entry.target.style.transform = "translateY(0)";
            }, 100);

            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.1 }
    );

    animatedElements.forEach((el) => {
      observer.observe(el);
    });
  }

  // Contact form handling
  function initContactForm() {
    const contactForm = document.querySelector("#contact form");
    if (contactForm) {
      contactForm.addEventListener("submit", function (e) {
        e.preventDefault();

        // Get form data
        const formData = new FormData(contactForm);
        const data = Object.fromEntries(formData);

        // Show loading state
        const submitBtn = contactForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = "Sending...";
        submitBtn.disabled = true;

        // Simulate form submission
        setTimeout(() => {
          showNotification(
            "Thank you! Your message has been sent successfully.",
            "success"
          );
          contactForm.reset();
          submitBtn.textContent = originalText;
          submitBtn.disabled = false;
        }, 1500);
      });
    }
  }

  // Quote form handling
  function initQuoteForm() {
    const quoteForm = document.querySelector("#quote form");
    if (quoteForm) {
      quoteForm.addEventListener("submit", function (e) {
        e.preventDefault();

        // Get form data
        const formData = new FormData(quoteForm);
        const data = Object.fromEntries(formData);

        // Show loading state
        const submitBtn = quoteForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = "Sending...";
        submitBtn.disabled = true;

        // Simulate form submission
        setTimeout(() => {
          showNotification(
            "Thank you! Your quote request has been submitted. We'll get back to you within 24 hours.",
            "success"
          );
          quoteForm.reset();
          submitBtn.textContent = originalText;
          submitBtn.disabled = false;
        }, 1500);
      });
    }
  }

  // Newsletter subscription
  const newsletterForm = document.querySelector(".newsletter");
  if (newsletterForm) {
    const newsletterBtn = newsletterForm.querySelector("button");
    const newsletterInput = newsletterForm.querySelector('input[type="email"]');

    newsletterBtn.addEventListener("click", function () {
      const email = newsletterInput.value.trim();

      if (!email) {
        showNotification("Please enter your email address.", "error");
        return;
      }

      if (!isValidEmail(email)) {
        showNotification("Please enter a valid email address.", "error");
        return;
      }

      // Show loading state
      newsletterBtn.textContent = "Subscribing...";
      newsletterBtn.disabled = true;

      // Simulate subscription
      setTimeout(() => {
        showNotification(
          "Thank you for subscribing to our newsletter!",
          "success"
        );
        newsletterInput.value = "";
        newsletterBtn.textContent = "Subscribe";
        newsletterBtn.disabled = false;
      }, 1000);
    });
  }

  // Smooth scrolling for navigation links
  function initSmoothScrolling() {
    const navLinks = document.querySelectorAll('a[href^="#"]');

    navLinks.forEach((link) => {
      link.addEventListener("click", function (e) {
        e.preventDefault();

        const targetId = this.getAttribute("href");
        const targetElement = document.querySelector(targetId);

        if (targetElement) {
          const offsetTop = targetElement.offsetTop - 80; // Account for fixed navbar

          window.scrollTo({
            top: offsetTop,
            behavior: "smooth",
          });

          // Close mobile menu if open
          const navbarCollapse = document.querySelector(".navbar-collapse");
          if (navbarCollapse.classList.contains("show")) {
            const toggleBtn = document.querySelector(".navbar-toggler");
            toggleBtn.click();
          }
        }
      });
    });
  }

  // Active navigation highlighting
  function updateActiveNav() {
    const sections = document.querySelectorAll("section[id]");
    const navLinks = document.querySelectorAll(".navbar-nav .nav-link");

    let currentSection = "";

    sections.forEach((section) => {
      const sectionTop = section.offsetTop - 100;
      const sectionHeight = section.clientHeight;

      if (
        window.scrollY >= sectionTop &&
        window.scrollY < sectionTop + sectionHeight
      ) {
        currentSection = section.getAttribute("id");
      }
    });

    navLinks.forEach((link) => {
      link.classList.remove("active");
      if (link.getAttribute("href") === "#" + currentSection) {
        link.classList.add("active");
      }
    });
  }

  window.addEventListener("scroll", updateActiveNav);

  // Utility functions
  function showNotification(message, type = "info") {
    // Remove existing notifications
    const existingNotification = document.querySelector(".notification");
    if (existingNotification) {
      existingNotification.remove();
    }

    // Create notification element
    const notification = document.createElement("div");
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${
                  type === "success"
                    ? "check-circle"
                    : type === "error"
                    ? "exclamation-triangle"
                    : "info-circle"
                }"></i>
                <span>${message}</span>
                <button class="notification-close">&times;</button>
            </div>
        `;

    // Add styles
    notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            background: ${
              type === "success"
                ? "#28a745"
                : type === "error"
                ? "#dc3545"
                : "#007bff"
            };
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            transform: translateX(100%);
            transition: transform 0.3s ease;
            max-width: 400px;
        `;

    const content = notification.querySelector(".notification-content");
    content.style.cssText = `
            display: flex;
            align-items: center;
            gap: 10px;
        `;

    const closeBtn = notification.querySelector(".notification-close");
    closeBtn.style.cssText = `
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            margin-left: auto;
        `;

    // Add to document
    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => {
      notification.style.transform = "translateX(0)";
    }, 100);

    // Auto remove
    const autoRemoveTimer = setTimeout(() => {
      removeNotification(notification);
    }, 5000);

    // Close button handler
    closeBtn.addEventListener("click", () => {
      clearTimeout(autoRemoveTimer);
      removeNotification(notification);
    });
  }

  function removeNotification(notification) {
    notification.style.transform = "translateX(100%)";
    setTimeout(() => {
      if (notification.parentNode) {
        notification.parentNode.removeChild(notification);
      }
    }, 300);
  }

  function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  // Preloader (if you want to add one)
  function hidePreloader() {
    const preloader = document.querySelector(".preloader");
    if (preloader) {
      preloader.style.opacity = "0";
      setTimeout(() => {
        preloader.style.display = "none";
      }, 300);
    }
  }

  // Call hidePreloader after everything is loaded
  window.addEventListener("load", hidePreloader);

  // Add loading class to body initially
  document.body.classList.add("loading");

  // Remove loading class after DOM is ready
  setTimeout(() => {
    document.body.classList.remove("loading");
  }, 100);

  // Back to top button
  function initBackToTop() {
    const backToTopBtn = document.createElement("button");
    backToTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
    backToTopBtn.className = "back-to-top";
    backToTopBtn.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #0066cc;
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 9998;
            box-shadow: 0 4px 15px rgba(0, 102, 204, 0.3);
        `;

    document.body.appendChild(backToTopBtn);

    window.addEventListener("scroll", function () {
      if (window.scrollY > 300) {
        backToTopBtn.style.opacity = "1";
        backToTopBtn.style.visibility = "visible";
      } else {
        backToTopBtn.style.opacity = "0";
        backToTopBtn.style.visibility = "hidden";
      }
    });

    backToTopBtn.addEventListener("click", function () {
      window.scrollTo({
        top: 0,
        behavior: "smooth",
      });
    });
  }

  initBackToTop();
});

// Service worker registration (optional, for PWA features)
if ("serviceWorker" in navigator) {
  window.addEventListener("load", function () {
    navigator.serviceWorker
      .register("/sw.js")
      .then(function (registration) {
        console.log("ServiceWorker registration successful");
      })
      .catch(function (error) {
        console.log("ServiceWorker registration failed");
      });
  });
}
