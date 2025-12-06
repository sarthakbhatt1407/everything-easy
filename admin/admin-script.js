// Admin Dashboard JavaScript

// API Configuration
const API_BASE_URL = "../backend";

// Global variables
let currentPage = 1;
let currentFilter = "";
let currentSearch = "";
let allQuotes = [];

// Load quotes on page load
document.addEventListener("DOMContentLoaded", function () {
  loadQuotes();
  setupEventListeners();
  setupLogout();
});

// Setup logout functionality
function setupLogout() {
  const logoutBtn = document.getElementById("logoutBtn");
  const logoutBtnTop = document.getElementById("logoutBtnTop");

  if (logoutBtn) {
    logoutBtn.addEventListener("click", function (e) {
      e.preventDefault();
      handleLogout();
    });
  }

  if (logoutBtnTop) {
    logoutBtnTop.addEventListener("click", function (e) {
      e.preventDefault();
      handleLogout();
    });
  }
}

// Handle logout
function handleLogout() {
  if (confirm("Are you sure you want to logout?")) {
    // Clear session
    sessionStorage.removeItem("admin_logged_in");
    sessionStorage.removeItem("admin_login_time");
    localStorage.removeItem("admin_remember");

    // Show notification
    showNotification("Logging out...", "info");

    // Redirect to login after a short delay
    setTimeout(() => {
      window.location.href = "login.html";
    }, 1000);
  }
}

// Load quotes from API
function loadQuotes(page = 1, status = "", search = "") {
  currentPage = page;
  currentFilter = status;
  currentSearch = search;

  let url = `${API_BASE_URL}/get-quotes.php?action=list&page=${page}`;
  if (status) url += `&status=${status}`;
  if (search) url += `&search=${encodeURIComponent(search)}`;

  fetch(url)
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        allQuotes = result.data.quotes;
        updateDashboard(result.data);
      } else {
        showNotification("Failed to load quotes: " + result.message, "error");
      }
    })
    .catch((error) => {
      console.error("Error loading quotes:", error);
      showNotification("Error loading quotes. Please try again.", "error");
    });
}

// Update dashboard with data
function updateDashboard(data) {
  // Update statistics
  if (data.stats) {
    updateStats(data.stats);
  }

  // Update table
  if (data.quotes) {
    updateTable(data.quotes);
  }

  // Update pagination
  if (data.pagination) {
    updatePagination(data.pagination);
  }
}

// Update statistics cards
function updateStats(stats) {
  document.querySelector(".stat-primary h3").textContent = stats.total || 0;
  document.querySelector(".stat-success h3").textContent = stats.pending || 0;
  document.querySelector(".stat-warning h3").textContent =
    stats.in_progress || 0;
  document.querySelector(".stat-info h3").textContent = stats.completed || 0;

  // Update badge count
  const badgeElement = document.querySelector(".sidebar-menu li .badge");
  if (badgeElement) {
    badgeElement.textContent = stats.pending || 0;
  }
}

// Update table with quotes
function updateTable(quotes) {
  const tbody = document.querySelector(".custom-table tbody");

  if (!quotes || quotes.length === 0) {
    tbody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No quote requests found.</p>
                </td>
            </tr>
        `;
    return;
  }

  tbody.innerHTML = quotes
    .map((quote, index) => {
      const serviceClass = getServiceClass(quote.service);
      const statusClass = getStatusClass(quote.status);
      const initials = getInitials(quote.first_name, quote.last_name);
      const formattedDate = formatDate(quote.created_at);

      return `
            <tr data-quote-id="${quote.id}">
                <td>${(currentPage - 1) * 10 + index + 1}</td>
                <td>
                    <div class="user-info">
                        <div class="user-avatar">${initials}</div>
                        <div>
                            <div class="user-name">${quote.first_name} ${
        quote.last_name
      }</div>
                            <div class="user-company">${
                              quote.company_name || "N/A"
                            }</div>
                        </div>
                    </div>
                </td>
                <td>${quote.email}</td>
                <td>${quote.phone || "N/A"}</td>
                <td><span class="service-badge ${serviceClass}">${formatService(
        quote.service
      )}</span></td>
                <td>${formatBudget(quote.budget)}</td>
                <td>${formatTimeline(quote.timeline)}</td>
                <td>${formattedDate}</td>
                <td><span class="status-badge ${statusClass}" data-id="${
        quote.id
      }">${formatStatus(quote.status)}</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-action btn-view" data-id="${
                          quote.id
                        }" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-action btn-edit" data-id="${
                          quote.id
                        }" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-action btn-delete" data-id="${
                          quote.id
                        }" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    })
    .join("");

  // Reattach event listeners
  attachTableEventListeners();
}

// Update pagination
function updatePagination(pagination) {
  const paginationContainer = document.querySelector(".pagination");
  const showingEntries = document.querySelector(".showing-entries");

  // Update showing entries text
  const start = (pagination.page - 1) * pagination.limit + 1;
  const end = Math.min(pagination.page * pagination.limit, pagination.total);
  showingEntries.textContent = `Showing ${start} to ${end} of ${pagination.total} entries`;

  // Generate pagination buttons
  let buttons = "";

  // Previous button
  buttons += `<button class="page-btn" ${
    pagination.page === 1 ? "disabled" : ""
  } data-page="${pagination.page - 1}">
        <i class="fas fa-chevron-left"></i>
    </button>`;

  // Page numbers
  for (let i = 1; i <= pagination.totalPages; i++) {
    if (
      i === 1 ||
      i === pagination.totalPages ||
      (i >= pagination.page - 2 && i <= pagination.page + 2)
    ) {
      buttons += `<button class="page-btn ${
        i === pagination.page ? "active" : ""
      }" data-page="${i}">${i}</button>`;
    } else if (i === pagination.page - 3 || i === pagination.page + 3) {
      buttons += `<button class="page-btn" disabled>...</button>`;
    }
  }

  // Next button
  buttons += `<button class="page-btn" ${
    pagination.page === pagination.totalPages ? "disabled" : ""
  } data-page="${pagination.page + 1}">
        <i class="fas fa-chevron-right"></i>
    </button>`;

  paginationContainer.innerHTML = buttons;

  // Attach pagination event listeners
  attachPaginationListeners();
}

// Setup event listeners
function setupEventListeners() {
  // Search functionality
  const searchInput = document.querySelector(".search-box input");
  if (searchInput) {
    let searchTimeout;
    searchInput.addEventListener("input", function (e) {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        loadQuotes(1, currentFilter, e.target.value);
      }, 500);
    });
  }

  // Filter button
  const filterBtn = document.querySelector(
    ".header-actions .btn-outline-primary"
  );
  if (filterBtn) {
    filterBtn.addEventListener("click", showFilterOptions);
  }

  // Export button
  const exportBtn = document.querySelector(".header-actions .btn-primary");
  if (exportBtn) {
    exportBtn.addEventListener("click", exportToCSV);
  }

  // Sidebar toggle
  const toggleBtn = document.getElementById("toggleSidebar");
  if (toggleBtn) {
    toggleBtn.addEventListener("click", function () {
      const sidebar = document.getElementById("sidebar");
      sidebar.classList.toggle("active");
    });
  }

  // Close sidebar when clicking outside on mobile
  document.addEventListener("click", function (event) {
    const sidebar = document.getElementById("sidebar");
    const toggleBtn = document.getElementById("toggleSidebar");

    if (window.innerWidth <= 992) {
      if (
        !sidebar.contains(event.target) &&
        !toggleBtn.contains(event.target)
      ) {
        sidebar.classList.remove("active");
      }
    }
  });
}

// Attach table event listeners
function attachTableEventListeners() {
  // View buttons
  document.querySelectorAll(".btn-view").forEach((button) => {
    button.addEventListener("click", function () {
      const quoteId = this.getAttribute("data-id");
      viewQuoteDetails(quoteId);
    });
  });

  // Edit buttons (update status)
  document.querySelectorAll(".btn-edit").forEach((button) => {
    button.addEventListener("click", function () {
      const quoteId = this.getAttribute("data-id");
      showStatusUpdateDialog(quoteId);
    });
  });

  // Delete buttons
  document.querySelectorAll(".btn-delete").forEach((button) => {
    button.addEventListener("click", function () {
      const quoteId = this.getAttribute("data-id");
      deleteQuote(quoteId);
    });
  });

  // Status badge click to change status
  document.querySelectorAll(".status-badge").forEach((badge) => {
    badge.addEventListener("click", function () {
      const quoteId = this.getAttribute("data-id");
      showStatusUpdateDialog(quoteId);
    });
    badge.style.cursor = "pointer";
  });
}

// Attach pagination listeners
function attachPaginationListeners() {
  document.querySelectorAll(".page-btn:not([disabled])").forEach((button) => {
    button.addEventListener("click", function () {
      const page = parseInt(this.getAttribute("data-page"));
      if (page && !isNaN(page)) {
        loadQuotes(page, currentFilter, currentSearch);
      }
    });
  });
}

// View quote details
function viewQuoteDetails(quoteId) {
  fetch(`${API_BASE_URL}/get-quotes.php?action=get&id=${quoteId}`)
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        showQuoteModal(result.data.quote);
      } else {
        showNotification("Failed to load quote details", "error");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showNotification("Error loading quote details", "error");
    });
}

// Show quote modal
function showQuoteModal(quote) {
  const modalBody = document.querySelector(
    "#quoteDetailsModal .modal-body .quote-details"
  );

  modalBody.innerHTML = `
        <div class="row mb-3">
            <div class="col-md-6">
                <strong>First Name:</strong>
                <p>${quote.first_name}</p>
            </div>
            <div class="col-md-6">
                <strong>Last Name:</strong>
                <p>${quote.last_name}</p>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <strong>Email Address:</strong>
                <p>${quote.email}</p>
            </div>
            <div class="col-md-6">
                <strong>Phone Number:</strong>
                <p>${quote.phone || "N/A"}</p>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <strong>Company Name:</strong>
                <p>${quote.company_name || "N/A"}</p>
            </div>
            <div class="col-md-6">
                <strong>Service Interested In:</strong>
                <p>${formatService(quote.service)}</p>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <strong>Project Budget:</strong>
                <p>${formatBudget(quote.budget)}</p>
            </div>
            <div class="col-md-6">
                <strong>Project Timeline:</strong>
                <p>${formatTimeline(quote.timeline)}</p>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <strong>Status:</strong>
                <p><span class="status-badge ${getStatusClass(
                  quote.status
                )}">${formatStatus(quote.status)}</span></p>
            </div>
            <div class="col-md-6">
                <strong>Submitted On:</strong>
                <p>${formatDate(quote.created_at)}</p>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <strong>Project Details:</strong>
                <p>${quote.project_details}</p>
            </div>
        </div>
    `;

  const modal = new bootstrap.Modal(
    document.getElementById("quoteDetailsModal")
  );
  modal.show();
}

// Show status update dialog
function showStatusUpdateDialog(quoteId) {
  const quote = allQuotes.find((q) => q.id == quoteId);
  if (!quote) return;

  const newStatus = prompt(
    `Update status for ${quote.first_name} ${
      quote.last_name
    }:\n\nCurrent: ${formatStatus(
      quote.status
    )}\n\nEnter new status:\n1 - Pending\n2 - In Progress\n3 - Completed\n\nEnter number (1-3):`
  );

  if (newStatus) {
    let status;
    switch (newStatus.trim()) {
      case "1":
        status = "pending";
        break;
      case "2":
        status = "in-progress";
        break;
      case "3":
        status = "completed";
        break;
      default:
        showNotification("Invalid status selection", "error");
        return;
    }

    updateQuoteStatus(quoteId, status);
  }
}

// Update quote status
function updateQuoteStatus(quoteId, status) {
  fetch(`${API_BASE_URL}/get-quotes.php?action=update`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ id: quoteId, status: status }),
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        showNotification("Status updated successfully", "success");
        loadQuotes(currentPage, currentFilter, currentSearch);
      } else {
        showNotification("Failed to update status: " + result.message, "error");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showNotification("Error updating status", "error");
    });
}

// Delete quote
function deleteQuote(quoteId) {
  const quote = allQuotes.find((q) => q.id == quoteId);
  if (!quote) return;

  if (
    !confirm(
      `Are you sure you want to delete the quote request from ${quote.first_name} ${quote.last_name}?`
    )
  ) {
    return;
  }

  fetch(`${API_BASE_URL}/get-quotes.php?action=delete`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ id: quoteId }),
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        showNotification("Quote deleted successfully", "success");
        loadQuotes(currentPage, currentFilter, currentSearch);
      } else {
        showNotification("Failed to delete quote: " + result.message, "error");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showNotification("Error deleting quote", "error");
    });
}

// Show filter options
function showFilterOptions() {
  const filter = prompt(
    "Filter by status:\n\n1 - All\n2 - Pending\n3 - In Progress\n4 - Completed\n\nEnter number (1-4):"
  );

  if (filter) {
    let status = "";
    switch (filter.trim()) {
      case "1":
        status = "";
        break;
      case "2":
        status = "pending";
        break;
      case "3":
        status = "in-progress";
        break;
      case "4":
        status = "completed";
        break;
      default:
        showNotification("Invalid filter selection", "error");
        return;
    }

    loadQuotes(1, status, currentSearch);
  }
}

// Export to CSV
function exportToCSV() {
  const table = document.querySelector(".custom-table");
  let csv = [];

  // Get headers
  const headers = [];
  table.querySelectorAll("thead th").forEach((th, index) => {
    if (index < 9) {
      // Exclude Actions column
      headers.push(th.textContent.trim());
    }
  });
  csv.push(headers.join(","));

  // Get data rows
  allQuotes.forEach((quote) => {
    const rowData = [
      quote.id,
      `${quote.first_name} ${quote.last_name}`,
      quote.email,
      quote.phone || "N/A",
      formatService(quote.service),
      formatBudget(quote.budget),
      formatTimeline(quote.timeline),
      formatDate(quote.created_at),
      formatStatus(quote.status),
    ];
    csv.push(rowData.map((field) => `"${field}"`).join(","));
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

// Utility Functions
function getInitials(firstName, lastName) {
  return (firstName.charAt(0) + lastName.charAt(0)).toUpperCase();
}

function getServiceClass(service) {
  const serviceMap = {
    "web-development": "service-web",
    "app-development": "service-mobile",
    seo: "service-seo",
    "cyber-security": "service-ui",
    "data-analytics": "service-ecommerce",
    "cloud-solutions": "service-web",
    consultation: "service-ui",
    other: "service-ecommerce",
  };
  return serviceMap[service] || "service-web";
}

function getStatusClass(status) {
  const statusMap = {
    pending: "status-pending",
    "in-progress": "status-progress",
    completed: "status-completed",
  };
  return statusMap[status] || "status-pending";
}

function formatService(service) {
  const serviceNames = {
    "web-development": "Web Development",
    "app-development": "Mobile App",
    seo: "SEO & Marketing",
    "cyber-security": "Cyber Security",
    "data-analytics": "Data Analytics",
    "cloud-solutions": "Cloud Solutions",
    consultation: "Consultation",
    other: "Other",
  };
  return serviceNames[service] || service;
}

function formatStatus(status) {
  const statusNames = {
    pending: "Pending",
    "in-progress": "In Progress",
    completed: "Completed",
  };
  return statusNames[status] || status;
}

function formatBudget(budget) {
  if (!budget) return "Not specified";

  const budgetNames = {
    "under-5k": "Under $5,000",
    "5k-10k": "$5,000 - $10,000",
    "10k-25k": "$10,000 - $25,000",
    "25k-50k": "$25,000 - $50,000",
    "over-50k": "Over $50,000",
    discuss: "Let's Discuss",
  };
  return budgetNames[budget] || budget;
}

function formatTimeline(timeline) {
  if (!timeline) return "Not specified";

  const timelineNames = {
    urgent: "ASAP",
    "1-month": "1 Month",
    "2-3-months": "2-3 Months",
    "3-6-months": "3-6 Months",
    "6-months-plus": "6+ Months",
    flexible: "Flexible",
  };
  return timelineNames[timeline] || timeline;
}

function formatDate(dateString) {
  const date = new Date(dateString);
  const options = { year: "numeric", month: "short", day: "numeric" };
  return date.toLocaleDateString("en-US", options);
}

// Notification Function
function showNotification(message, type = "info") {
  // Create notification element
  const notification = document.createElement("div");
  notification.className = `notification notification-${type}`;

  const icon =
    type === "success"
      ? "check-circle"
      : type === "error"
      ? "exclamation-circle"
      : "info-circle";
  const bgColor =
    type === "success" ? "#28a745" : type === "error" ? "#dc3545" : "#17a2b8";

  notification.innerHTML = `
        <i class="fas fa-${icon}"></i>
        <span>${message}</span>
    `;

  // Style the notification
  notification.style.cssText = `
        position: fixed;
        top: 90px;
        right: 30px;
        background: ${bgColor};
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
      if (notification.parentNode) {
        document.body.removeChild(notification);
      }
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

// Initialize
console.log("Admin Dashboard initialized successfully");
