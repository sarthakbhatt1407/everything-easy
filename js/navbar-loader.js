// Load navbar in all pages
function loadNavbar() {
  fetch("navbar.html")
    .then((response) => response.text())
    .then((data) => {
      document.getElementById("navbar-container").innerHTML = data;

      // Set active link after navbar is loaded
      setActiveNavLink();
    })
    .catch((error) => console.error("Error loading navbar:", error));
}

// Function to set active nav link
function setActiveNavLink() {
  // Get current page name
  let currentPage = window.location.pathname.split("/").pop();

  // If no file name, default to index.html
  if (!currentPage || currentPage === "" || currentPage === "/") {
    currentPage = "index.html";
  }

  console.log("Current page detected:", currentPage);

  // Remove active class from all nav links
  document.querySelectorAll(".navbar-nav .nav-link").forEach((link) => {
    link.classList.remove("active");
  });

  // Find and activate the matching link
  document.querySelectorAll(".navbar-nav .nav-link").forEach((link) => {
    const href = link.getAttribute("href");

    // Direct match for exact page names
    if (href === currentPage) {
      link.classList.add("active");
      console.log("Activated link:", href);
    }
    // Special case: it-services.html should activate Services link
    else if (currentPage === "it-services.html" && href === "services.html") {
      link.classList.add("active");
      console.log("Activated Services for it-services.html");
    }
  });
}

// Load navbar when DOM is ready
document.addEventListener("DOMContentLoaded", loadNavbar);
