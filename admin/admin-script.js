// Admin Dashboard JavaScript

// Toggle Sidebar
document.getElementById("toggleSidebar").addEventListener("click", function () {
  const sidebar = document.getElementById("sidebar");
  sidebar.classList.toggle("active");
});

// Close sidebar when clicking outside on mobile
document.addEventListener("click", function (event) {
  const sidebar = document.getElementById("sidebar");
  const toggleBtn = document.getElementById("toggleSidebar");

  if (window.innerWidth <= 992) {
    if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
      sidebar.classList.remove("active");
    }
  }
});

// View Details Button Handler
document.querySelectorAll(".btn-view").forEach((button) => {
  button.addEventListener("click", function () {
    // Get the row data
    const row = this.closest("tr");
    const cells = row.querySelectorAll("td");

    // Show modal with details
    const modal = new bootstrap.Modal(
      document.getElementById("quoteDetailsModal")
    );
    modal.show();

    console.log("Viewing details for:", cells[1].textContent.trim());
  });
});

// Edit Button Handler
document.querySelectorAll(".btn-edit").forEach((button) => {
  button.addEventListener("click", function () {
    const row = this.closest("tr");
    const cells = row.querySelectorAll("td");

    console.log("Editing quote:", cells[1].textContent.trim());
    alert("Edit functionality will be implemented with backend integration");
  });
});

// Delete Button Handler
document.querySelectorAll(".btn-delete").forEach((button) => {
  button.addEventListener("click", function () {
    const row = this.closest("tr");
    const userName = row.querySelector(".user-name").textContent;

    if (
      confirm(
        `Are you sure you want to delete the quote request from ${userName}?`
      )
    ) {
      // Animation before removal
      row.style.transition = "all 0.3s ease";
      row.style.opacity = "0";
      row.style.transform = "translateX(100px)";

      setTimeout(() => {
        row.remove();
        console.log("Deleted quote for:", userName);
        // Show success message
        showNotification("Quote request deleted successfully", "success");
      }, 300);
    }
  });
});

// Pagination Button Handlers
document.querySelectorAll(".page-btn").forEach((button) => {
  button.addEventListener("click", function () {
    if (!this.disabled && !this.classList.contains("active")) {
      // Remove active class from all buttons
      document.querySelectorAll(".page-btn").forEach((btn) => {
        btn.classList.remove("active");
      });

      // Add active class to clicked button (if it's a number)
      if (!isNaN(this.textContent)) {
        this.classList.add("active");
      }

      console.log("Loading page:", this.textContent);
      // Here you would load the data for the selected page
    }
  });
});

// Search Functionality
const searchInput = document.querySelector(".search-box input");
if (searchInput) {
  searchInput.addEventListener("input", function (e) {
    const searchTerm = e.target.value.toLowerCase();
    const tableRows = document.querySelectorAll(".custom-table tbody tr");

    tableRows.forEach((row) => {
      const text = row.textContent.toLowerCase();
      if (text.includes(searchTerm)) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    });
  });
}

// Filter Button Handler
document
  .querySelector(".header-actions .btn-outline-primary")
  .addEventListener("click", function () {
    console.log("Opening filter options");
    alert("Filter functionality will be implemented with backend integration");
  });

// Export Button Handler
document
  .querySelector(".header-actions .btn-primary")
  .addEventListener("click", function () {
    console.log("Exporting data");
    exportToCSV();
  });

// Export to CSV Function
function exportToCSV() {
  const table = document.querySelector(".custom-table");
  let csv = [];

  // Get headers
  const headers = [];
  table.querySelectorAll("thead th").forEach((th) => {
    headers.push(th.textContent.trim());
  });
  csv.push(headers.join(","));

  // Get data rows
  table.querySelectorAll("tbody tr").forEach((row) => {
    const rowData = [];
    row.querySelectorAll("td").forEach((td, index) => {
      // Skip the actions column
      if (index < headers.length - 1) {
        rowData.push('"' + td.textContent.trim().replace(/"/g, '""') + '"');
      }
    });
    csv.push(rowData.join(","));
  });

  // Create download link
  const csvContent = csv.join("\n");
  const blob = new Blob([csvContent], { type: "text/csv" });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download =
    "quote-requests-" + new Date().toISOString().split("T")[0] + ".csv";
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  window.URL.revokeObjectURL(url);

  showNotification("Data exported successfully", "success");
}

// Notification Function
function showNotification(message, type = "info") {
  // Create notification element
  const notification = document.createElement("div");
  notification.className = `notification notification-${type}`;
  notification.innerHTML = `
        <i class="fas fa-${
          type === "success" ? "check-circle" : "info-circle"
        }"></i>
        <span>${message}</span>
    `;

  // Style the notification
  notification.style.cssText = `
        position: fixed;
        top: 90px;
        right: 30px;
        background: ${type === "success" ? "#28a745" : "#17a2b8"};
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        gap: 10px;
        z-index: 10000;
        animation: slideIn 0.3s ease;
    `;

  document.body.appendChild(notification);

  // Remove after 3 seconds
  setTimeout(() => {
    notification.style.animation = "slideOut 0.3s ease";
    setTimeout(() => {
      document.body.removeChild(notification);
    }, 300);
  }, 3000);
}

// Add CSS animations
const style = document.createElement("style");
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Sidebar Menu Active State
document.querySelectorAll(".sidebar-menu li a").forEach((link) => {
  link.addEventListener("click", function (e) {
    e.preventDefault();

    // Remove active class from all items
    document.querySelectorAll(".sidebar-menu li").forEach((item) => {
      item.classList.remove("active");
    });

    // Add active class to clicked item
    this.parentElement.classList.add("active");

    // Update page title
    const title = this.querySelector("span").textContent;
    document.querySelector(".page-title").textContent = title;

    console.log("Navigating to:", title);
  });
});

// Status Badge Click Handler (Change Status)
document.querySelectorAll(".status-badge").forEach((badge) => {
  badge.addEventListener("click", function () {
    const currentStatus = this.textContent;
    const statuses = ["Pending", "In Progress", "Completed"];
    const currentIndex = statuses.indexOf(currentStatus);
    const nextIndex = (currentIndex + 1) % statuses.length;
    const nextStatus = statuses[nextIndex];

    // Update badge
    this.textContent = nextStatus;
    this.className =
      "status-badge status-" + nextStatus.toLowerCase().replace(" ", "");

    console.log("Status changed to:", nextStatus);
    showNotification(`Status updated to ${nextStatus}`, "success");
  });

  // Add pointer cursor
  badge.style.cursor = "pointer";
});

// Real-time clock in notifications area
function updateTime() {
  const now = new Date();
  const timeString = now.toLocaleTimeString("en-US", {
    hour: "2-digit",
    minute: "2-digit",
    hour12: true,
  });

  // You can add this to the topnav if needed
  console.log("Current time:", timeString);
}

// Update time every minute
setInterval(updateTime, 60000);

// Initialize tooltips if Bootstrap is available
if (typeof bootstrap !== "undefined") {
  const tooltipTriggerList = [].slice.call(
    document.querySelectorAll("[title]")
  );
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
}

// Table row click to highlight
document.querySelectorAll(".custom-table tbody tr").forEach((row) => {
  row.addEventListener("click", function (e) {
    // Don't highlight if clicking on action buttons
    if (!e.target.closest(".action-buttons")) {
      // Remove highlight from all rows
      document.querySelectorAll(".custom-table tbody tr").forEach((r) => {
        r.style.backgroundColor = "";
      });

      // Highlight clicked row
      this.style.backgroundColor = "#e3f2fd";
    }
  });
});

// Keyboard shortcuts
document.addEventListener("keydown", function (e) {
  // Ctrl/Cmd + K for search focus
  if ((e.ctrlKey || e.metaKey) && e.key === "k") {
    e.preventDefault();
    if (searchInput) {
      searchInput.focus();
    }
  }

  // Escape to close sidebar on mobile
  if (e.key === "Escape") {
    const sidebar = document.getElementById("sidebar");
    if (window.innerWidth <= 992) {
      sidebar.classList.remove("active");
    }
  }
});

// Initialize page
console.log("Admin Dashboard initialized successfully");
showNotification("Welcome to Everything Easy Admin Dashboard", "success");
