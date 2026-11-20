FROM php:8.2-apache

# Otimizações de sistema e extensões necessárias (SQLite3, curl e mod_rewrite)
RUN apt-get update && apt-get install -y --no-install-recommends \
    sqlite3 \
    libsqlite3-dev \
    iputils-ping \
    curl \
    && docker-php-ext-install pdo pdo_sqlite \
    && a2enmod rewrite headers \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configuração otimizada do Apache e PHP
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && echo "AddDefaultCharset UTF-8" >> /etc/apache2/apache2.conf \
    && echo "expose_php = Off" > /usr/local/etc/php/conf.d/security.conf \
    && echo "display_errors = On" >> /usr/local/etc/php/conf.d/security.conf

# Diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos da aplicação
COPY src/ /var/www/html/

# Ajustar permissões para www-data (necessário para escrita no SQLite e LFI/RCE contido)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && mkdir -p /var/www/html/data \
    && chmod -R 777 /var/www/html/data

EXPOSE 80

CMD ["apache2-foreground"]
