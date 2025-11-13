# --- 1. Base Image ---
# Start from the official PHP 8.3 image with Apache pre-installed.
FROM php:8.3-apache

# --- 2. Install System & PHP Dependencies ---
# Update the package list and install libraries needed for PHP extensions
# We need 'mysqli' (for MySQL) and 'gd' (for image processing).
# We also add 'zip', 'unzip', and 'git' for good measure (e.g., for Composer)
RUN apt-get update && apt-get install -y \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        zip \
        unzip \
        git \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mysqli

# --- 3. Enable Apache Rewrite Module ---
# This is for "pretty URLs" (e.g., /post/my-first-post)
# Your app doesn't use it yet, but most do.
RUN a2enmod rewrite

# --- 4. Copy Application Code ---
# Copy all your project files (login.php, adminhome.php, etc.)
# into the Apache web root directory inside the container.
COPY . /var/www/html/

# --- 5. Set Permissions for Uploads ---
# Create the 'uploads' directory and give the Apache user (www-data)
# permission to write files to it.
# In production, this directory should be a persistent 'volume'.
RUN mkdir -p /var/www/html/uploads && \
    chown -R www-data:www-data /var/www/html/uploads && \
    chmod -R 755 /var/www/html/uploads

# --- 6. Set Working Directory ---
WORKDIR /var/www/html

# --- 7. Expose Port ---
# The Apache server inside the container listens on port 80.
EXPOSE 80
