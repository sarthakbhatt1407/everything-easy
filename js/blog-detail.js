// Load blog post based on URL parameter
document.addEventListener("DOMContentLoaded", function () {
  const urlParams = new URLSearchParams(window.location.search);
  const blogId = urlParams.get("id");

  if (blogId) {
    loadBlogPost(blogId);
    incrementViews(blogId);
  } else {
    // Redirect to blog page if no ID
    window.location.href = "blog.html";
  }
});

// Load blog post from database
function loadBlogPost(id) {
  fetch(`backend/blog-api.php?action=getOne&id=${id}`)
    .then((response) => response.json())
    .then((data) => {
      if (data.success && data.blog) {
        displayBlogPost(data.blog);
        loadRelatedPosts(data.blog.category, id);
      } else {
        showError();
      }
    })
    .catch((error) => {
      console.error("Error loading blog:", error);
      showError();
    });
}

// Display blog post
function displayBlogPost(blog) {
  document.getElementById("blog-title").textContent = blog.title;
  document.getElementById("blog-date").textContent = formatDate(
    blog.created_at
  );
  document.getElementById("blog-image").src = blog.image_url;
  document.getElementById("blog-image").alt = blog.title;

  // Set content
  const contentDiv = document.getElementById("blog-content");
  contentDiv.innerHTML = `
    <p class="lead mb-4">${blog.excerpt}</p>
    <div class="mb-4">
      <span class="badge bg-primary me-2">${blog.category}</span>
      ${
        blog.tags
          ? blog.tags
              .split(",")
              .map(
                (tag) =>
                  `<span class="badge bg-secondary me-2">${tag.trim()}</span>`
              )
              .join("")
          : ""
      }
    </div>
    <div class="blog-body">${blog.content}</div>
  `;

  // Update page title
  document.title = blog.title + " - EverythingEasy Technology";

  // Update share buttons
  const currentUrl = encodeURIComponent(window.location.href);
  const title = encodeURIComponent(blog.title);

  const shareButtons = document.querySelector(".share-buttons");
  shareButtons.innerHTML = `
    <a href="https://www.facebook.com/sharer/sharer.php?u=${currentUrl}" target="_blank" class="btn btn-primary me-2 mb-2">
      <i class="fab fa-facebook-f me-2"></i>Facebook
    </a>
    <a href="https://twitter.com/intent/tweet?url=${currentUrl}&text=${title}" target="_blank" class="btn btn-info text-white me-2 mb-2">
      <i class="fab fa-twitter me-2"></i>Twitter
    </a>
    <a href="https://www.linkedin.com/shareArticle?mini=true&url=${currentUrl}&title=${title}" target="_blank" class="btn btn-primary me-2 mb-2">
      <i class="fab fa-linkedin-in me-2"></i>LinkedIn
    </a>
    <a href="https://api.whatsapp.com/send?text=${title}%20${currentUrl}" target="_blank" class="btn btn-success me-2 mb-2">
      <i class="fab fa-whatsapp me-2"></i>WhatsApp
    </a>
  `;
}

// Load related posts
function loadRelatedPosts(category, currentId) {
  fetch("backend/blog-api.php?action=getAll")
    .then((response) => response.json())
    .then((data) => {
      if (data.success && data.blogs) {
        const relatedBlogs = data.blogs
          .filter(
            (blog) =>
              blog.id != currentId &&
              blog.status === "published" &&
              blog.category === category
          )
          .slice(0, 2);

        if (relatedBlogs.length > 0) {
          displayRelatedPosts(relatedBlogs);
        }
      }
    })
    .catch((error) => {
      console.error("Error loading related posts:", error);
    });
}

// Display related posts
function displayRelatedPosts(blogs) {
  const relatedContainer = document.querySelector(".related-posts .row");

  relatedContainer.innerHTML = blogs
    .map(
      (blog) => `
    <div class="col-md-6 mb-4">
      <div class="blog-card">
        <img src="${blog.image_url}" alt="${
        blog.title
      }" class="img-fluid rounded-top" style="height: 200px; object-fit: cover; width: 100%;" />
        <div class="p-3">
          <h6 class="fw-bold mb-2">
            <a href="blog-detail.html?id=${blog.id}" class="text-dark">${
        blog.title
      }</a>
          </h6>
          <p class="text-muted small mb-0">${formatDate(blog.created_at)}</p>
        </div>
      </div>
    </div>
  `
    )
    .join("");
}

// Increment view count
function incrementViews(id) {
  fetch(`backend/blog-api.php?action=incrementViews&id=${id}`, {
    method: "GET",
  }).catch((error) => {
    console.error("Error incrementing views:", error);
  });
}

// Format date
function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
}

// Show error message
function showError() {
  document.getElementById("blog-title").textContent = "Blog Post Not Found";
  document.getElementById("blog-content").innerHTML = `
    <div class="alert alert-warning">
      <i class="fas fa-exclamation-triangle me-2"></i>
      Sorry, this blog post could not be found or has been removed.
      <a href="blog.html" class="alert-link">Go back to blog</a>
    </div>
  `;
  document.querySelector(".blog-featured-image").style.display = "none";
  document.querySelector(".blog-share").style.display = "none";
  document.querySelector(".related-posts").style.display = "none";
}
