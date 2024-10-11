# Stage 1: Build Node.js Application
FROM node:16-alpine as builder

# Set the working directory
WORKDIR /app

# Copy package.json and package-lock.json (if it exists) and install Node.js dependencies
COPY package*.json ./
RUN npm install

# Copy the rest of the application code
COPY . .

# Build your application if needed (adjust if you have a specific build command)
# RUN npm run build  # Uncomment if you have a build command

# Stage 2: PHP Setup and Dependencies
FROM php:8.1-apache

# Install necessary PHP extensions
RUN apt-get update && apt-get install -y unzip git

# Install Composer for PHP dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory for PHP
WORKDIR /var/www/html/tc-app  # Assuming this is the final working directory in the container

# Copy the composer.json and composer.lock (if it exists) directly into the container
COPY --from=builder /app/composer.json ./
COPY --from=builder /app/composer.lock ./

# Install PHP dependencies
RUN composer install --ignore-platform-reqs 

# Copy the PHP source files from the previous stage (if you have a 'php' folder or similar)
COPY --from=builder /app/php ./php  
# Adjust this if your PHP files are in a different location

# Set the correct permissions for the Apache server
RUN chown -R www-data:www-data /var/www/html

# 
