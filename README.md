# PHP Laravel Barcode Qr Code Reader
This sample demonstrates how to decode barcode and Qr code on the server side using the PHP Laravel framework.

## Installation
- [Composer](https://getcomposer.org/download/)
- [PHP 7.4](https://windows.php.net/download)
- Laravel:

    ```bash
    composer global require laravel/installer
    ```

Check the version number in terminal:

```bash
php -v
PHP 7.4.30 (cli) (built: Jun  7 2022 16:24:55) ( ZTS Visual C++ 2017 x64 )
Copyright (c) The PHP Group
Zend Engine v3.4.0, Copyright (c) Zend Technologies

php artisan --version
Laravel Framework 8.83.23
```

## Usage
1. Build and install the [PHP barcode Qr code extension](./ext/dbr).
2. Apply for a [30-day FREE trial license](https://www.dynamsoft.com/customer/license/trialLicense/?product=dbr), and substitute the license key in `app/Http/Controllers/ImageUploadController.php`.

    ```php
    DBRInitLicense("DLS2eyJoYW5kc2hha2VDb2RlIjoiMjAwMDAxLTE2NDk4Mjk3OTI2MzUiLCJvcmdhbml6YXRpb25JRCI6IjIwMDAwMSIsInNlc3Npb25QYXNzd29yZCI6IndTcGR6Vm05WDJrcEQ5YUoifQ==");
    ```

3. Run the web application:

    ```bash
    composer update
    composer install
    php artisan serve
    ```
4. Visit `http://127.0.0.1:8000/barcode_qr_reader`.

    ![PHP laravel barcode Qr code reader](https://www.dynamsoft.com/codepool/img/2022/08/php-laravel-barcode-qr-reader.gif)