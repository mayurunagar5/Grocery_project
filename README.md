# FreshMart Grocery Store

A complete grocery store web application with user authentication, product management, shopping cart, and contact form functionality.

## 🚀 Features

- **User Authentication**: Register, login, and logout functionality
- **Product Management**: Admin panel to manage products
- **Shopping Cart**: Add/remove items and manage quantities
- **Contact Form**: Customer support with database storage
- **Responsive Design**: Works on desktop and mobile devices
- **Admin Dashboard**: Manage products and view contact submissions

## 📋 Prerequisites

- XAMPP (Apache + MySQL + PHP)
- Web browser
- phpMyAdmin access

## 🛠️ Installation

1. **Clone/Download** the project to your XAMPP htdocs folder:

   ```
   C:\xampp\htdocs\project_grocery\
   ```

2. **Start XAMPP**:
   - Start Apache and MySQL services
   - Open phpMyAdmin: `http://localhost/phpmyadmin`

3. **Create Database**:
   - Create a new database named `data`
   - Import the `data.sql` file or run the setup scripts

4. **Setup Contact Form** (Optional):
   - Visit: `http://localhost/project_grocery/setup_contact_table.php`
   - This will create the contact submissions table

5. **Configure Database** (if needed):
   - Edit `config/database.php` with your database credentials
   - Default: username `root`, password `''` (empty)

## 🎯 How to Use

### For Users:

1. **Start the application**: `http://localhost/project_grocery/`
2. **Register/Login**: Create an account or login with existing credentials
3. **Browse Products**: View categories and products on the homepage
4. **Add to Cart**: Click "Add to Cart" on any product
5. **View Cart**: Click the cart icon in the header
6. **Contact Support**: Use the contact form for any questions

### For Admins:

1. **Admin Login**: Use admin credentials (check database for default)
2. **Manage Products**: Add, edit, or delete products
3. **View Contact Submissions**: Check customer messages
4. **Update Status**: Mark submissions as read/replied

## 📁 File Structure

```
project_grocery/
├── index.php              # Entry point (redirects to login/homepage)
├── login.php              # User registration and login
├── homepage.php           # Main store page (requires login)
├── cart.php              # Shopping cart (requires login)
├── contact.php           # Contact form (requires login)
├── admin.php             # Admin dashboard
├── admin_contact.php     # Contact submissions management
├── config/
│   └── database.php      # Database configuration
├── api/
│   ├── user_login.php    # User login API
│   ├── user_register.php # User registration API
│   ├── admin_login.php   # Admin login API
│   ├── contact_submit.php # Contact form submission
│   └── logout.php        # Logout functionality
├── data.sql              # Database structure
└── setup_contact_table.php # Contact table setup
```

## 🔐 Default Admin Credentials

Check the `data.sql` file or database for default admin credentials. You may need to create an admin user manually in the database.

## 🎨 Customization

- **Colors**: Edit CSS variables in the `:root` section of each file
- **Products**: Add/remove products through the admin panel
- **Categories**: Modify category options in the code
- **Styling**: Update CSS in the `<style>` sections

## 🐛 Troubleshooting

1. **Database Connection Error**: Check database credentials in `config/database.php`
2. **Page Not Found**: Ensure XAMPP is running and files are in correct location
3. **Login Issues**: Check if database tables exist and have proper structure
4. **Contact Form Not Working**: Run the setup script to create the contact table

## 📞 Support

For issues or questions, use the contact form in the application or check the admin panel for existing submissions.

## 🔄 Updates

- Contact form submissions are stored in the database
- Admin can manage product inventory
- User sessions are maintained across pages
- Responsive design for all devices

---

**Happy Shopping! 🛒**

## Local helper scripts

I added two convenience scripts for local development under the `scripts/` folder:

- `scripts/run-local.sh`: creates/imports the `data` database from `data.sql` and starts the PHP built-in server at `http://localhost:8000`.
- `scripts/check_db.php`: quick PHP script to verify database connectivity using the project's `config/database.php`.

Usage:

```
chmod +x scripts/run-local.sh
./scripts/run-local.sh

# or just check DB connectivity
php scripts/check_db.php
```

If your MySQL user/password differ, update `config/database.php` before running the scripts.
