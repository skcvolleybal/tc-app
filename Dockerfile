FROM node:18-bullseye

# Install Nginx
RUN apt-get update && \
    apt-get install -y nginx

# Create app directory
WORKDIR /usr/src/app


# Copy package.json and package-lock.json (if it exists) and install Node.js dependencies
COPY package*.json ./
RUN npm install

# Copy the rest of the application code
COPY . .


EXPOSE 80


CMD ["nginx", "-g", "daemon off;"]

