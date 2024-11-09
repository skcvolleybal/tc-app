FROM node:18-bullseye

# Install Nginx
RUN apt-get update && \
    apt-get install -y nginx

# Create app directory
WORKDIR /var/www/html


# Copy package.json and package-lock.json (if it exists) and install Node.js dependencies
COPY package*.json ./
RUN npm install

EXPOSE 80


CMD ["nginx", "-g", "daemon off;"]

