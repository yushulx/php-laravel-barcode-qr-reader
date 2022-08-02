## PHP Barcode QR Code Extension
The project aims to build a PHP Barcode QR code extension with [Dynamsoft C++ Barcode SDK](https://www.dynamsoft.com/barcode-reader/sdk-desktop-server/).

## Download
[Dynamsoft C++ Barcode Reader v9.2](https://www.dynamsoft.com/barcode-reader/downloads)

## License Key
App for a [30-day FREE trial license](https://www.dynamsoft.com/customer/license/trialLicense/?product=dbr).

## How to Build the Extension

**Windows**
1. Download [php-sdk-binary-tools](https://github.com/php/php-sdk-binary-tools), [PHP source code and development package](https://windows.php.net/download)
2. Build the extension:

    ```bash
    phpize
    configure --enable-dbr
    nmake
    ```
3. Configure the extension in `php.ini`:

    ```bash
    extension=dbr
    ```
4. Copy the generated `php_dbr.dll` to the `php/ext/` folder, and copy `DynamicPdfx64.dll`, `DynamsoftBarcodeReaderx64.dll`, `DynamsoftLicenseClientx64.dll` and `vcomp110.dll` to the PHP root directory.

    ![]()

5. Substitute the license key in `test/reader.php`.

    ```php
    DBRInitLicense("DLS2eyJoYW5kc2hha2VDb2RlIjoiMjAwMDAxLTE2NDk4Mjk3OTI2MzUiLCJvcmdhbml6YXRpb25JRCI6IjIwMDAwMSIsInNlc3Npb25QYXNzd29yZCI6IndTcGR6Vm05WDJrcEQ5YUoifQ==");
    ```
 
6. Test the extension:

    ```bash
    cd test
    php reader.php
    ```

    ![PHP barcode QR code reader](https://www.dynamsoft.com/codepool/img/2022/08/php-barcode-qrcode-reader.png)

## References
- [Build your own PHP on Windows](https://wiki.php.net/internals/windows/stepbystepbuild_sdk_2)