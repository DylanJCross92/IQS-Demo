FROM debian:jessie
MAINTAINER Tom Gornick <thomas.gornick@icg360.com>

RUN apt-get -y update && \
    apt-get -y install \
    locales-all \
    build-essential \
    apache2 \
    php5-dev \
    libssl-dev \
    libapache2-mod-php5 \
    php-pear \
    php5-common \
    php5-odbc \
    php5-xsl \
    php5-json \
    php5-curl \
    php5-mcrypt \
    php5-gd \
    php5-gd \
    swftools \
    vim \
    curl

RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/* \
    /tmp/* \
    /var/tmp/*

# Configure PHP
RUN pear install XML_Serializer-0.20.2
RUN php5enmod xsl json curl mcrypt gd odbc

# Configure Apache
RUN a2dissite 000-default
RUN a2enmod php5 proxy proxy_http speling status rewrite
COPY files/apache_status.conf /etc/apache2/mods-available/status.conf

# Install UnixODBC
ADD files/unixODBC-2.3.2.tar.gz /tmp/
RUN cd /tmp/unixODBC-2.3.2 && \
    /tmp/unixODBC-2.3.2/configure \
      --enable-gui=no --enable-drivers=no --enable-iconv \
      --with-iconv-char-enc=UTF8 --with-iconv-ucode-enc=UTF16LE \
      --libdir=/usr/lib/x86_64-linux-gnu --prefix=/usr \
      --sysconfdir=/etc --enable-stats=no && \
    make && \
    make install

# Install MSODBC driver
ADD files/msodbcsql-11.0.2270.tar.gz /tmp/
RUN cd /tmp/msodbcsql-11.0.2270.0 && \
    /bin/bash /tmp/msodbcsql-11.0.2270.0/install.sh install --accept-license --force

# Symlink libraries
RUN ln -sf /usr/lib/x86_64-linux-gnu/libcrypto.so.1.0.0 \
      /usr/lib/x86_64-linux-gnu/libcrypto.so.10
RUN ln -sf /usr/lib/x86_64-linux-gnu/libssl.so.1.0.0 \
      /usr/lib/x86_64-linux-gnu/libssl.so.10
RUN ln -sf /usr/lib/x86_64-linux-gnu/libodbcinst.so.2.0.0 \
      /usr/lib/x86_64-linux-gnu/libodbcinst.so.1

# Install Sempl
RUN curl -o /usr/local/bin/sempl \
    https://raw.githubusercontent.com/nextrevision/sempl/master/sempl
RUN chmod +x /usr/local/bin/sempl

# Configure IQS
RUN mkdir -p /opt/apps/iqs /etc/iqs /var/log/iqs
ADD deploy/iqs_current.tar.gz /opt/apps/iqs/
RUN cp -R /opt/apps/iqs/Iqs/. /var/www/Iqs/
RUN cp -R /opt/apps/iqs/iqs-admin/. /var/www/iqs-admin/
COPY files/iqsConf.ini.tmpl /etc/iqs/iqsConf.ini.tmpl
COPY files/iqs.conf.tmpl /etc/apache2/sites-available/iqs.conf.tmpl
COPY files/filePaths.ini /var/www/Iqs/Conf/filePaths.ini
COPY files/init.php.tmpl /var/www/iqs-admin/app/init.php.tmpl
COPY files/apiKey.properties.tmpl /var/www/iqs-admin/app/vendor/stormpath/apiKey.properties.tmpl
RUN chown -R www-data: /var/www/Iqs

