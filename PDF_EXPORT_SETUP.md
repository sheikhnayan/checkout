# PDF Export Setup for CartVIP Reports (Ubuntu + Apache2)

## Prerequisites
- Ubuntu 18.04+ or 20.04+
- Apache2 with PHP
- Laravel 9+ project
- SSH access to your server

## Installation Steps

### Step 1: Install System Dependencies

SSH into your server and run:

```bash
sudo apt-get update
sudo apt-get install -y php-gd libfontconfig1 libxrender1 fonts-dejavu
```

### Step 2: Install dompdf via Composer

Navigate to your project directory and run:

```bash
cd /var/www/cartvip  # or your project path
composer require barryvdh/laravel-dompdf
```

### Step 3: Publish Configuration (Optional)

```bash
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

This creates `config/dompdf.php` where you can customize PDF settings.

### Step 4: Set Proper Permissions

Set write permissions for the storage directory where temporary files are created:

```bash
sudo chown -R www-data:www-data /var/www/cartvip/storage
sudo chmod -R 775 /var/www/cartvip/storage
sudo chown -R www-data:www-data /var/www/cartvip/bootstrap/cache
sudo chmod -R 775 /var/www/cartvip/bootstrap/cache
```

### Step 5: Configure Apache2

Ensure Apache has the necessary modules enabled:

```bash
sudo a2enmod rewrite
sudo a2enmod deflate
sudo systemctl restart apache2
```

### Step 6: Configure PHP for PDF Generation

Edit your PHP configuration to ensure memory limits are sufficient:

```bash
sudo nano /etc/php/8.1/apache2/php.ini
```

Make sure these values are set:

```ini
memory_limit = 256M
max_execution_time = 300
upload_max_filesize = 100M
post_max_size = 100M
```

Then restart Apache:

```bash
sudo systemctl restart apache2
```

### Step 7: Create Fonts Directory (if needed)

For better font support, you can add custom fonts:

```bash
mkdir -p /var/www/cartvip/resources/fonts
sudo chown -R www-data:www-data /var/www/cartvip/resources/fonts
```

### Step 8: Test PDF Export

1. Go to your reports page: `https://yoursite.com/admins/reports`
2. Click on any report
3. Click the PDF export button
4. The PDF should download successfully

## Troubleshooting

### Issue: "Class 'Barryvdh\DomPDF\Facade\Pdf' not found"

Solution: Clear Laravel cache and composer autoload
```bash
cd /var/www/cartvip
php artisan cache:clear
php artisan config:clear
composer dump-autoload
```

### Issue: "Failed to load image" in PDF

Solution: Ensure storage directory is accessible and has proper permissions
```bash
sudo chown -R www-data:www-data /var/www/cartvip/storage
chmod -R 755 /var/www/cartvip/storage
```

### Issue: PDF timeout or memory errors

Solution: Increase PHP limits in `/etc/php/8.1/apache2/php.ini`
```ini
memory_limit = 512M
max_execution_time = 600
```

### Issue: Fonts not rendering correctly

Solution: Install additional font packages
```bash
sudo apt-get install -y fonts-liberation fonts-opensans fonts-dejavu-extra
```

## Performance Optimization

For large reports, add this to your `config/dompdf.php`:

```php
'enable_html5_parser' => true,
'isFontSubsettingEnabled' => true,
'chroot' => base_path(),
'tempDir' => storage_path('temp'),
```

## Enable PDF Streaming for Large Files

The current implementation uses download(). For very large PDFs, you can stream them:

```php
return $pdf->stream($filename);
```

## Security Notes

1. **File Storage**: PDFs are temporarily stored. Add cleanup job:
   ```bash
   php artisan schedule:work
   ```

2. **Access Control**: Only authenticated admins can export reports (already enforced in ReportController)

3. **Rate Limiting**: Consider adding rate limiting to export endpoint:
   ```php
   Route::post('/reports/{report}/export', 'ReportController@export')->middleware('throttle:10,1');
   ```

## Support

For dompdf documentation: https://github.com/barryvdh/laravel-dompdf
For Laravel documentation: https://laravel.com/docs
