# PHP Laravel Barcode QR Code Reader
This sample demonstrates how to decode barcodes and QR codes on the server side using the PHP Laravel framework.

## Architecture
Instead of compiling a PHP extension (which is tied to a specific PHP version), this project uses a standalone **Dynamsoft Barcode Reader Service** written in Python. PHP communicates with the service via HTTP and receives JSON results. This approach works across PHP versions without recompiling anything.

```
PHP 8.x / 7.x  <--HTTP/JSON-->  Barcode Service (Python + Dynamsoft DBR)  <--SDK-->  Dynamsoft DBR/DCV
```

## Prerequisites
- [Python 3.x](https://www.python.org/downloads/)
- [PHP 7.4+](https://windows.php.net/download)
- [Composer](https://getcomposer.org/download/)
- [Laravel](https://laravel.com/)

Check the version numbers in the terminal:

```bash
python --version
php -v
PHP 7.4.30 (cli) (built: Jun  7 2022 16:24:55) ( ZTS Visual C++ 2017 x64 )
Copyright (c) The PHP Group
Zend Engine v3.4.0, Copyright (c) Zend Technologies

php artisan --version
Laravel Framework 8.83.23
```

## PHP Upload Limits
The demo accepts images up to 20 MB by default. Make sure your PHP installation allows uploads of that size. For the PHP binary at `D:\php`, edit `D:\php\php.ini`:

```ini
upload_max_filesize = 20M
post_max_size = 20M
memory_limit = 256M
max_execution_time = 60
```

After saving the file, restart the Laravel development server.

## Installation
1. Install PHP dependencies:

    ```bash
    composer install
    ```

2. Install Python service dependencies:

    ```bash
    cd service
    pip install -r requirements.txt
    ```

## Usage
1. Apply for a [30-day FREE trial license](https://www.dynamsoft.com/customer/license/trialLicense/?product=dcv&package=cross-platform), and substitute the license key in `service/app.py`.

    ```python
    LICENSE_KEY = "LICENSE-KEY"
    ```

2. Start the barcode service:

    ```bash
    cd service
    python app.py
    ```

    The service runs on `http://127.0.0.1:8080` by default. You can change the port with the `BARCODE_SERVICE_PORT` environment variable or bind to all interfaces with `--host-all`.

3. Run the web application:

    ```bash
    php artisan serve
    ```

4. Visit `http://127.0.0.1:8000/barcode_qr_reader` in a web browser.

    The web UI supports:
    - Drag and drop images onto the drop zone
    - Clicking the drop zone to select a file
    - AJAX upload (the page does not reload)
    - Auto-fitted image preview
    - Canvas overlay showing decoded barcode locations
    - A results panel listing barcode format, text, raw bytes, and localization

## Testing with PHP CLI
A command-line test script is provided at `test_service.php`. Make sure the Python service is running, then:

```bash
php test_service.php <path/to/image>
```

## Blog
[How to Read Barcode QR Code on the Server Side Using PHP Laravel](https://www.dynamsoft.com/codepool/php-laravel-barcode-qr-code-reader.html)
