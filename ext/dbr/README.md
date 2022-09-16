## PHP Barcode QR Code Extension
The project aims to build a PHP Barcode QR code extension with [Dynamsoft C++ Barcode SDK](https://www.dynamsoft.com/barcode-reader/sdk-desktop-server/).

## Download
- [Dynamsoft C++ Barcode Reader v9.2](https://www.dynamsoft.com/barcode-reader/downloads)
- PHP 7.4
    - Windows
        - [php-sdk-binary-tools](https://github.com/php/php-sdk-binary-tools)
        - [PHP 7.4 source code](https://windows.php.net/downloads/releases/php-7.4.30-src.zip)
        - [PHP 7.4](https://windows.php.net/downloads/releases/php-7.4.30-nts-Win32-vc15-x64.zip)
        - [Development package (SDK to develop PHP extensions)](https://windows.php.net/downloads/releases/php-devel-pack-7.4.30-nts-Win32-vc15-x64.zip)
    - Linux
        ```bash
        sudo apt install php7.4-dev php7.4 libxml2-dev
        ```

## How to Build the Extension on Windows and Linux

**Windows**
1. Download [php-sdk-binary-tools](https://github.com/php/php-sdk-binary-tools), [PHP source code and development package](https://windows.php.net/download)
2. Use `phpize` to build the extension:

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

    ![install PHP barcode Qr code extension in Windows](https://www.dynamsoft.com/codepool/img/2022/08/php-install-barcode-extension.png)

**Linux**
1. In Ubuntu 20.04, install:

    ```bash
    sudo apt install php7.4-dev php7.4 libxml2-dev
    ```

2. Copy `libDynamicPdf.so`, `libDynamsoftBarcodeReader.so`, `libDynamsoftLicenseClient.so` to `/usr/bin`:

    ```bash
    sudo cp bin/linux/*.so /usr/lib
    ```

3. Use `phpize` to build the extension:

    ```bash
    phpize
    ./configure --enable-dbr
    make
    ```

4. Install the extension:

    ```bash
    make install
    ```

    Then add `extension=dbr` to `/etc/php/7.4/cli/php.ini`.


## Test the Extension
1. Apply for a [30-day FREE trial license](https://www.dynamsoft.com/customer/license/trialLicense/?product=dbr), and substitute the license key in `test.php`.

    ```php
    DBRInitLicense("DLS2eyJoYW5kc2hha2VDb2RlIjoiMjAwMDAxLTE2NDk4Mjk3OTI2MzUiLCJvcmdhbml6YXRpb25JRCI6IjIwMDAwMSIsInNlc3Npb25QYXNzd29yZCI6IndTcGR6Vm05WDJrcEQ5YUoifQ==");
    ```
 
2. Test the extension:

    ```bash
    php test.php
    ```

    ![PHP barcode QR code reader](https://www.dynamsoft.com/codepool/img/2022/08/php-barcode-qrcode-reader.png)

## Docker
- Run the `test.php` in Docker:

    ```bash
    docker build -t dynamsoft-php-barcode-reader .
    docker run -it --rm  dynamsoft-php-barcode-reader
    ```
- Read barcodes from an image located in the local file system:

    ```bash
    docker run -it --rm -v <image-folder>:/app dynamsoft-php-barcode-reader php /usr/src/myapp/test.php /app/<image-file>
    ```
    
## Docker Hub
[https://hub.docker.com/repository/docker/yushulx/dynamsoft-php-barcode-reader](https://hub.docker.com/repository/docker/yushulx/dynamsoft-php-barcode-reader)

```bash
docker run yushulx/dynamsoft-php-barcode-reader
```

## References
- [Build your own PHP on Windows](https://wiki.php.net/internals/windows/stepbystepbuild_sdk_2)


