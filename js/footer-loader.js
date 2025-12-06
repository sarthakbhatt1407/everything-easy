// Load footer in all pages
function loadFooter() {
  fetch("footer.html")
    .then((response) => response.text())
    .then((data) => {
      document.getElementById("footer-container").innerHTML = data;

      // Set current year after footer is loaded
      const yearElement = document.getElementById("currentYear");
      if (yearElement) {
        yearElement.textContent = new Date().getFullYear();
      }
    })
    .catch((error) => console.error("Error loading footer:", error));
}

// Load footer when DOM is ready
document.addEventListener("DOMContentLoaded", loadFooter);
