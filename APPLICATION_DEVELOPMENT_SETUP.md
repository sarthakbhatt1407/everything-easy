# Application Development Pages & Location Management

## Overview

This update adds support for application/mobile app development service pages, similar to the existing website development pages. Users can now manage service locations for both website and application development services.

## New Files

### 1. **it-applications.php** (Public Page)
- **Location:** `/it-applications.php`
- **Purpose:** Display application development services by location
- **Route:** `/it-applications/{slug}`
- **Features:**
  - Hero section with call-to-action
  - Metrics showcase (apps delivered, client satisfaction, etc.)
  - Case studies section
  - Service benefits
  - Why choose us section
  - FAQ section
  - Call-to-action buttons
  - Responsive design

**Usage:**
```
https://yourdomain.com/it-applications/dehradun-app-development
https://yourdomain.com/it-applications/mumbai-app-development
```

### 2. **admin/locations-management.php** (Admin Panel)
- **Location:** `/admin/locations-management.php`
- **Purpose:** Manage all service locations for both website and app development
- **Features:**
  - View all locations in a table
  - Add new locations
  - Edit existing locations
  - Delete locations
  - Auto-generate URL slugs from location names
  - Meta title and description management
  - Service type categorization

**Accessing the Admin Panel:**
1. Login to admin panel
2. Click "Service Locations" in the sidebar
3. Manage locations (create, edit, delete)

## Database Schema

### Updated Locations Table

```sql
-- Optional: Add service_type column for categorization
ALTER TABLE locations 
ADD COLUMN service_type VARCHAR(50) DEFAULT '' 
COMMENT 'Service type: website, application, or empty (both)' 
AFTER meta_description;
```

**Table Structure:**
```
locations
├── id (PRIMARY KEY)
├── location_name (string) - e.g., "Dehradun Mobile App Development"
├── city_name (string) - e.g., "Dehradun"
├── state (string) - e.g., "Uttarakhand"
├── slug (string, UNIQUE) - e.g., "dehradun-app-development"
├── meta_title (string) - SEO title
├── meta_description (string) - SEO description
└── service_type (string, optional) - "website", "application", or ""
```

## How to Add a New Location

### Via Admin Panel:
1. Navigate to `/admin/locations-management.php`
2. Click "Add New Location"
3. Fill in the form:
   - **Location Name:** Name for the service location (slug will auto-generate)
   - **City Name:** The city (e.g., "Dehradun")
   - **State:** The state (e.g., "Uttarakhand")
   - **Service Type:** Select "Application Development", "Website Development", or "Both"
   - **Meta Title:** SEO title (max 60 characters)
   - **Meta Description:** SEO description (max 160 characters)
4. Click "Add Location"

### Via SQL:
```sql
INSERT INTO locations 
(location_name, city_name, state, slug, meta_title, meta_description, service_type) 
VALUES
('Dehradun Mobile App Development', 'Dehradun', 'Uttarakhand', 'dehradun-app-development', 
 'Professional Mobile App Development Services in Dehradun | EverythingEasy Technology', 
 'Custom mobile app development for iOS and Android in Dehradun.', 
 'application');
```

## Navigation Updates

The admin sidebar menu now includes:
- Quote Requests
- Blog Posts
- Job Applications
- **Service Locations** ← NEW
- Logout

## Front-End Integration

### URL Structure
- Website development: `/it-services/{slug}`
- Application development: `/it-applications/{slug}`

### Example URLs
```
/it-applications/dehradun-app-development
/it-applications/mumbai-app-development
/it-applications/delhi-app-development
/it-applications/bangalore-app-development
```

## Services Locations Page

Update your services-locations.php or create an applications-locations.php page to display:
```php
<?php
require_once 'backend/config.php';
$conn = getDBConnection();

// Fetch application development locations
$locations = mysqli_query($conn, 
  "SELECT location_name, city_name, slug, meta_title 
   FROM locations 
   WHERE service_type IN ('application', '') 
   ORDER BY id ASC");
?>
```

## SEO Considerations

- **Meta Titles:** Optimized for search engine display
- **Meta Descriptions:** Help users understand the page content in search results
- **Slug Structure:** URL-friendly for better SEO
- **Schema Markup:** JSON-LD schema included in both pages
- **Mobile Responsive:** Both pages are fully responsive

## Customization

### Modify Hero Section
Edit the hero section in `it-applications.php`:
```php
<h5 class="text-warning mb-3">
  PROFESSIONAL APPLICATION DEVELOPMENT
</h5>
```

### Modify Case Studies
Update the case study cards with your actual projects:
```php
<h5 class="mb-1">Your Project Name</h5>
<p class="text-muted mb-0">Project Category</p>
```

### Adjust Metrics
Modify the metrics section to reflect your company stats:
```php
<h2>75+</h2>
<p>Apps Delivered</p>
```

## API Integration

Forms in `it-applications.php` submit to:
```
POST /backend/submit-quote.php
```

Data payload:
```json
{
  "firstName": "string",
  "lastName": "string",
  "email": "string",
  "phone": "string",
  "service": "string",
  "message": "string"
}
```

## Testing

### Add Test Location:
1. Go to `/admin/locations-management.php`
2. Add a location with:
   - Location Name: "Test City"
   - City: "Test"
   - State: "Test"
   - Service Type: "Application Development"

### View Test Page:
```
/it-applications/test-city
```

## Troubleshooting

### Location Not Showing:
1. Check if location is properly saved (view admin panel)
2. Verify slug is correct in URL
3. Check database connectivity

### Form Not Submitting:
1. Check console for JavaScript errors
2. Verify `/backend/submit-quote.php` exists
3. Check form field names match expected data structure

### Styling Issues:
1. Ensure `/css/style.css` is properly linked
2. Verify Bootstrap CDN is accessible
3. Check for conflicting CSS

## Version History

**v1.1** - Application Development Pages
- Added `it-applications.php` public page
- Added `admin/locations-management.php` management interface
- Added service_type column support
- Updated admin sidebar navigation

**v1.0** - Website Development (Existing)
- Initial setup with website development pages
- Location-based services structure
