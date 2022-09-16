FROM php:7.4-cli
COPY . /usr/src/myapp
WORKDIR /usr/src/myapp
RUN cp bin/linux/*.so /usr/lib
RUN phpize; ./configure --enable-dbr; make; make install
RUN mv "./php.ini" "$PHP_INI_DIR/php.ini"
CMD [ "php", "./test.php" ]