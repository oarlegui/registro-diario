# INSTALLATION.md

## Quick Start Guide for cPanel Installation

This guide will walk you through deploying Registro-Diario on a cPanel hosting environment.

## Prerequisites

- cPanel hosting account
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache with mod_rewrite

## Step-by-Step Installation

### 1. Download the Application

Download or clone this repository to your local machine.

### 2. Upload Files to cPanel

1. Log into your cPanel account
2. Open **File Manager**
3. Navigate to `public_html` (or your desired installation directory)
4. Upload all files from the repository
5. If you uploaded as a ZIP file, extract it

### 3. Create MySQL Database

1. In cPanel, go to **MySQL® Databases**
2. Create a new database:
   - Database Name: `registro_diario` (or your preferred name)
3. Create a MySQL user:
   - Username: Choose a secure username
   - Password: Generate a strong password
4. Add the user to the database with **ALL PRIVILEGES**
5. Write down your database details:
   - Database name
   - Username
   - Password
   - Host (usually `localhost`)

### 4. Import Database Schema

1. In cPanel, go to **phpMyAdmin**
2. Select your newly created database from the left sidebar
3. Click on the **Import** tab
4. Click **Choose File** and select `database/schema.sql`
5. Click **Go** at the bottom of the page
6. Wait for the import to complete

You should see a success message indicating that 2 tables were created:
- `users`
- `records`

### 5. Configure Database Connection

1. In File Manager, navigate to the `config` directory
2. Open `database.php` for editing
3. Update the database credentials:

```php
define('DB_HOST', 'localhost');              // Usually 'localhost'
define('DB_NAME', 'your_database_name');     // Your database name
define('DB_USER', 'your_mysql_username');    // Your MySQL username
define('DB_PASS', 'your_mysql_password');    // Your MySQL password
```

4. Save the file

### 6. Set File Permissions

Ensure proper file permissions:

1. In File Manager, select the following directories:
   - `public/` - set to 755
   - `app/` - set to 755
   - `config/` - set to 755

2. For `config/database.php` - set to 644

To change permissions in cPanel:
- Right-click on file/folder → Change Permissions
- Or use the Permissions button in the toolbar

### 7. Verify .htaccess Files

Make sure you can see hidden files:
1. Click **Settings** in File Manager
2. Check **Show Hidden Files (dotfiles)**
3. Verify these files exist:
   - `.htaccess` in the root directory
   - `.htaccess` in the `config/` directory

### 8. Test the Installation

1. Open your web browser
2. Navigate to: `http://yourdomain.com/public/login.php`
3. You should see the login page

**Default credentials** (change immediately after first login):
- Email: `admin@registro-diario.local`
- Password: `admin123`

### 9. First Login and Setup

1. Log in with the default credentials
2. **IMPORTANT**: Change the default admin password immediately

To change the password:
1. Go to phpMyAdmin in cPanel
2. Select your database
3. Click on the `users` table
4. Edit the admin user row
5. Generate a new password hash using this PHP code:

```php
<?php
echo password_hash('your_new_password', PASSWORD_BCRYPT);
?>
```

6. Update the `password` field with the new hash

### 10. Create Additional Users (Optional)

To create additional users:

1. Go to phpMyAdmin
2. Select your database
3. Click on the `users` table
4. Click **Insert** at the top
5. Fill in the details:
   - `email`: User's email address
   - `password`: Generate a hash using `password_hash()` (see above)
   - `role`: `user` or `admin`
6. Click **Go**

## Troubleshooting

### Cannot Connect to Database

**Error**: "Could not connect to database"

**Solution**:
1. Verify database credentials in `config/database.php`
2. Make sure the database user has proper privileges
3. Check that the database host is correct (usually `localhost`)
4. Contact your hosting provider if issues persist

### 500 Internal Server Error

**Solution**:
1. Check file permissions (directories: 755, files: 644)
2. Verify `.htaccess` syntax
3. Check PHP error logs in cPanel under **Errors**
4. Make sure PHP version is 7.4 or higher

### Login Page Shows Blank Screen

**Solution**:
1. Check PHP error logs
2. Verify all required PHP extensions are installed:
   - PDO
   - pdo_mysql
   - session
3. Check file permissions

### .htaccess Not Working

**Solution**:
1. Verify mod_rewrite is enabled (contact hosting provider)
2. Check that `AllowOverride` is enabled
3. Verify `.htaccess` file syntax

### Session Not Persisting

**Solution**:
1. Check that PHP sessions are enabled
2. Verify session directory permissions
3. Clear browser cookies and try again

## Security Recommendations

### After Installation

1. **Change default credentials immediately**
2. **Remove or secure the database schema file**: Consider deleting `database/schema.sql` after installation
3. **Regular backups**: Set up automated database backups in cPanel
4. **Keep PHP updated**: Use the latest stable PHP version available
5. **Monitor logs**: Regularly check error logs for suspicious activity

### For Production Use

1. Disable error display in PHP:
   ```php
   ini_set('display_errors', 0);
   error_reporting(E_ALL);
   ```

2. Use HTTPS: Install an SSL certificate (free with Let's Encrypt)

3. Implement rate limiting for login attempts (consider adding fail2ban or similar)

4. Regular security updates: Keep your system updated

## Getting Help

If you encounter issues:

1. Check the **Troubleshooting** section above
2. Review PHP error logs in cPanel
3. Verify all prerequisites are met
4. Check file and directory permissions
5. Contact your hosting provider for server-specific issues

## Backup Instructions

### Manual Backup

1. **Database Backup**:
   - Go to phpMyAdmin
   - Select your database
   - Click **Export**
   - Choose **Quick** method
   - Click **Go**
   - Save the downloaded SQL file

2. **File Backup**:
   - In File Manager, compress the application folder
   - Download the compressed file

### Automated Backups

Set up automated backups in cPanel:
1. Go to **Backup** in cPanel
2. Configure automatic backups
3. Store backups in a secure location

## Updating the Application

To update to a new version:

1. **Backup** your current installation (database and files)
2. Download the new version
3. **Do not replace** `config/database.php`
4. Replace all other files
5. Check the changelog for database changes
6. Run any new SQL migrations if required
7. Test thoroughly

## Performance Optimization

For better performance:

1. **Enable PHP OPcache** (usually available in cPanel)
2. **Enable gzip compression** (already configured in `.htaccess`)
3. **Use MySQL query cache** (if available)
4. **Regular database optimization**: In phpMyAdmin, select tables and click "Optimize table"

## Support

For additional help, refer to:
- README.md for detailed features and usage
- Your hosting provider's support documentation
- cPanel documentation

---

**Note**: This application is designed specifically for cPanel environments but can be adapted for other hosting platforms with similar PHP/MySQL support.
