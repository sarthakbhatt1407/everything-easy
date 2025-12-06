# 🚀 Quick Start Guide - Everything Easy

## Step-by-Step Setup (5 Minutes)

### 1. Install XAMPP (if not already installed)

- Download from: https://www.apachefriends.org/
- Install and start **Apache** and **MySQL** services

### 2. Move Project to XAMPP

- Copy the entire `everything-easy` folder to:
  ```
  C:\xampp\htdocs\everything-easy\
  ```

### 3. Import Database

1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Click "Import" tab
3. Choose file: `backend/database.sql`
4. Click "Go" button
5. Database `everything_easy` will be created with sample data

### 4. Test Database Connection

- Open: http://localhost/everything-easy/backend/test-connection.php
- You should see "✓ Connection Successful!"
- Check the statistics to verify data is loaded

### 5. Access the Application

#### Contact Form (Frontend)

```
http://localhost/everything-easy/contact.html
```

- Fill out and submit a quote request
- Check for success message

#### Admin Panel (Backend)

```
http://localhost/everything-easy/admin/
```

**Login Credentials:**

- **Username:** `everythingeasy`
- **Password:** `everythingeasy@9412`

After login:

- View all quote requests
- Manage status, delete, export data
- Logout when done

---

## ⚡ Quick Commands

### Start XAMPP (PowerShell)

```powershell
cd C:\xampp
.\xampp-control.exe
```

### Test PHP Version

```powershell
php -v
```

### Start PHP Built-in Server (Alternative)

```powershell
cd "C:\Users\bheem chand\Desktop\everythingeasy\everything-easy"
php -S localhost:8000
```

Then access: http://localhost:8000/

---

## 📁 Important Files

| File                       | Purpose                       |
| -------------------------- | ----------------------------- |
| `backend/database.sql`     | Database schema & sample data |
| `backend/config.php`       | Database credentials          |
| `backend/submit-quote.php` | Form submission API           |
| `backend/get-quotes.php`   | Admin panel data API          |
| `contact.html`             | Contact form page             |
| `admin/index.php`          | Admin dashboard               |

---

## 🧪 Testing Checklist

- [ ] XAMPP Apache & MySQL running
- [ ] Database imported successfully
- [ ] Test connection page shows success
- [ ] Submit test quote from contact form
- [ ] View quote in admin panel
- [ ] Update quote status
- [ ] Delete quote
- [ ] Export to CSV

---

## 🔧 Default Configuration

```php
Database Host: localhost
Database Name: everything_easy
Database User: root
Database Password: (empty)
```

**To change:** Edit `backend/config.php`

---

## 🎯 Features Overview

### Contact Form

✅ Responsive design  
✅ Form validation  
✅ AJAX submission  
✅ Email, phone, company fields  
✅ Service selection dropdown  
✅ Budget & timeline options  
✅ Project details textarea

### Admin Panel

✅ Dashboard statistics  
✅ Real-time data from MySQL  
✅ Pagination (10 per page)  
✅ Search functionality  
✅ Filter by status  
✅ View details modal  
✅ Update status  
✅ Delete quotes  
✅ Export to CSV

---

## 🐛 Troubleshooting

### Issue: Can't connect to database

**Solution:**

1. Start MySQL in XAMPP Control Panel
2. Check credentials in `backend/config.php`
3. Run test-connection.php

### Issue: 404 on admin panel

**Solution:**

- Make sure path is: `http://localhost/everything-easy/admin/`
- Check Apache is running

### Issue: Form not submitting

**Solution:**

1. Open browser console (F12)
2. Check Network tab for errors
3. Verify backend path in contact.html

### Issue: No data showing in admin

**Solution:**

1. Import database.sql file
2. Submit test quote from contact form
3. Refresh admin panel

---

## 📞 Need Help?

1. Check `backend/test-connection.php` first
2. Look at browser console for errors (F12)
3. Check PHP error logs in `backend/logs/error.log`
4. Verify all files are in correct locations

---

## 🎉 You're All Set!

Your quote management system is ready to use!

- **Frontend**: Users submit quotes via contact form
- **Backend**: Data saved to MySQL database
- **Admin**: Manage all quotes in dashboard

Enjoy! 🚀
