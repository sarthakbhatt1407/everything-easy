// Load WhatsApp floating button
fetch("whatsapp-float.html")
  .then((response) => response.text())
  .then((data) => {
    const container = document.getElementById("whatsapp-container");
    if (container) {
      container.innerHTML = data;
      
      // Get the WhatsApp button element
      const whatsappBtn = container.querySelector('.whatsapp-float');
      
      if (whatsappBtn) {
        // Hide initially
        whatsappBtn.style.display = 'none';
        
        // Show/hide based on scroll position (same logic as scroll-to-top button)
        window.addEventListener('scroll', function() {
          if (window.pageYOffset > 300) {
            whatsappBtn.style.display = 'flex';
          } else {
            whatsappBtn.style.display = 'none';
          }
        });
      }
    }
  })
  .catch((error) => console.error("Error loading WhatsApp button:", error));
