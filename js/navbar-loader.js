// Load navbar in all pages
function loadNavbar() {
  fetch("navbar.html")
    .then((response) => response.text())
    .then((data) => {
      document.getElementById("navbar-container").innerHTML = data;
    })
    .catch((error) => console.error("Error loading navbar:", error));
}

// Load navbar when DOM is ready
document.addEventListener("DOMContentLoaded", loadNavbar);
