# 1. Imagen base oficial de PHP con Apache
FROM php:8.2-apache

# 2. Actualizar el sistema e instalar dependencias necesarias (git y unzip son requeridos por Composer)
RUN apt-get update && apt-get install -y \
    libssl-dev \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# 3. Instalar y habilitar la extensión de MongoDB para PHP a través de PECL
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# 4. Instalar Composer copiándolo desde su imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Establecer el directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

# 6. Copiar los archivos de tu proyecto (html y php) al directorio raíz del servidor web
COPY . /var/www/html/

# 7. Ejecutar Composer para instalar la librería oficial de MongoDB
# (Esto generará la carpeta 'vendor' y el 'autoload.php' que requiere tu script)
RUN composer require mongodb/mongodb

# 8. Ajustar los permisos para que Apache pueda leer y ejecutar los archivos correctamente
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# 9. Exponer el puerto 80 para acceder al servidor web
EXPOSE 80
