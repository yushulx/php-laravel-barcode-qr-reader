# PHP Laravel Barcode Qr Code Reader
This sample demonstrates how to decode barcode and Qr code on the server side using the PHP Laravel framework.

## Installation
- [Composer](https://getcomposer.org/download/)
    - Windows
        Run [Composer-Setup.exe](https://getcomposer.org/Composer-Setup.exe)
    - Linux
        ```bash
        php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
        php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
        php composer-setup.php
        php -r "unlink('composer-setup.php');"
        sudo mv composer.phar /usr/local/bin/composer
        ```
- [PHP 7.4](https://windows.php.net/download)
    - Windows
        
        [php-7.4.30-nts-Win32-vc15-x64.zip](https://windows.php.net/downloads/releases/php-7.4.30-nts-Win32-vc15-x64.zip)
    - Linux
        ```bash
        sudo apt install php7.4
        ```
- Laravel:

    ```bash
    composer global require laravel/installer
    ```

Check the version number in the terminal:

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

## Blog
[How to Read Barcode QR Code on the Server Side Using PHP Laravel](https://www.dynamsoft.com/codepool/php-laravel-barcode-qr-code-reader.html)
