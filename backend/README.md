# Everything Easy - Backend Setup Instructions

## Database Setup

### Step 1: Import Database

1. Open phpMyAdmin or MySQL Workbench
2. Create a new database (or the SQL file will create it automatically)
3. Import the SQL file: `backend/database.sql`
4. The database `everything_easy` will be created with the `quotes` table

### Step 2: Configure Database Connection

1. Open `backend/config.php`
2. Update the database credentials if needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');        // Change if different
   define('DB_PASS', '');            // Add your password
   define('DB_NAME', 'everything_easy');
   ```

## Server Setup

### Option 1: Using XAMPP/WAMP

1. Install XAMPP or WAMP
2. Copy the entire project to:
   - XAMPP: `C:\xampp\htdocs\everything-easy\`
   - WAMP: `C:\wamp64\www\everything-easy\`
3. Start Apache and MySQL from XAMPP/WAMP Control Panel
4. Import database using phpMyAdmin (http://localhost/phpmyadmin)
5. Access the site:
   - Frontend: `http://localhost/everything-easy/`
   - Admin Panel: `http://localhost/everything-easy/admin/`

### Option 2: Using PHP Built-in Server

1. Open terminal in project directory
2. Start PHP server:
   ```bash
   php -S localhost:8000
   ```
3. Make sure MySQL is running
4. Access the site:
   - Frontend: `http://localhost:8000/`
   - Admin Panel: `http://localhost:8000/admin/`

## File Structure

```
everything-easy/
├── admin/
│   ├── index.php           # Admin dashboard
│   ├── admin-style.css     # Admin styles
│   └── admin-script.js     # Admin JavaScript
├── backend/
│   ├── database.sql        # Database schema & sample data
│   ├── config.php          # Database configuration
│   ├── submit-quote.php    # API to submit quote
│   └── get-quotes.php      # API to fetch/manage quotes
├── contact.html            # Contact form page
└── ... (other files)
```

## Testing the System

### Test Form Submission

1. Go to `http://localhost/everything-easy/contact.html`
2. Fill out the quote request form
3. Submit the form
4. Check for success message

### Test Admin Panel

1. Go to `http://localhost/everything-easy/admin/`
2. View the dashboard with statistics
3. See all quote requests in the table
4. Test features:
   - View details (click eye icon)
   - Update status (click edit icon)
   - Delete quote (click trash icon)
   - Search quotes (use search box)
   - Filter by status (click Filter button)
   - Export to CSV (click Export button)

## API Endpoints

### Submit Quote

- **URL**: `backend/submit-quote.php`
- **Method**: POST
- **Content-Type**: application/json
- **Body**:

```json
{
  "firstName": "John",
  "lastName": "Doe",
  "email": "john@example.com",
  "phone": "+1234567890",
  "company": "Company Name",
  "service": "web-development",
  "budget": "5k-10k",
  "timeline": "2-3-months",
  "message": "Project details...",
  "newsletter": true
}
```

### Get Quotes (List)

- **URL**: `backend/get-quotes.php?action=list&page=1&limit=10`
- **Method**: GET
- **Query Parameters**:
  - `action=list` (required)
  - `page` (optional, default: 1)
  - `limit` (optional, default: 10)
  - `status` (optional: pending, in-progress, completed)
  - `search` (optional: search term)

### Get Single Quote

- **URL**: `backend/get-quotes.php?action=get&id=1`
- **Method**: GET
- **Query Parameters**:
  - `action=get` (required)
  - `id` (required: quote ID)

### Update Quote Status

- **URL**: `backend/get-quotes.php?action=update`
- **Method**: POST
- **Content-Type**: application/json
- **Body**:

```json
{
  "id": 1,
  "status": "in-progress"
}
```

### Delete Quote

- **URL**: `backend/get-quotes.php?action=delete`
- **Method**: POST
- **Content-Type**: application/json
- **Body**:

```json
{
  "id": 1
}
```

## Troubleshooting

### CORS Issues

If you encounter CORS errors, make sure:

1. Both frontend and backend are on the same domain/port
2. CORS headers are set in PHP files (already included)

### Database Connection Failed

1. Check MySQL is running
2. Verify credentials in `backend/config.php`
3. Make sure database exists
4. Check user has proper permissions

### 404 Errors on API Calls

1. Check file paths in `admin-script.js`
2. Make sure the `backend` folder is in the correct location
3. Verify server is running

### Form Not Submitting

1. Open browser console (F12) to check for errors
2. Verify API endpoint URL is correct
3. Check network tab for failed requests

## Security Notes (Production)

Before deploying to production:

1. Change database credentials
2. Remove error display: `ini_set('display_errors', 0);`
3. Add proper authentication for admin panel
4. Implement rate limiting on API endpoints
5. Add CSRF protection
6. Sanitize all inputs (already implemented)
7. Use HTTPS
8. Add proper .htaccess rules

## Features

### Frontend (Contact Form)

- ✅ Form validation
- ✅ AJAX submission
- ✅ Success/Error notifications
- ✅ Responsive design

### Backend (PHP APIs)

- ✅ MySQLi database connection
- ✅ Input sanitization
- ✅ Error logging
- ✅ JSON responses
- ✅ CRUD operations

### Admin Panel

- ✅ Real-time data from database
- ✅ Statistics dashboard
- ✅ Quote list with pagination
- ✅ Search functionality
- ✅ Filter by status
- ✅ View details modal
- ✅ Update quote status
- ✅ Delete quotes
- ✅ Export to CSV
- ✅ Responsive design

## Sample Data

The database.sql file includes 5 sample quote requests for testing.
You can delete them after testing by:

1. Going to admin panel
2. Clicking the trash icon on each row
   Or run: `DELETE FROM quotes WHERE id <= 5;` in phpMyAdmin

## Support

For issues or questions:

- Check browser console for errors
- Check PHP error logs
- Verify database connection
- Ensure all files are in correct locations
