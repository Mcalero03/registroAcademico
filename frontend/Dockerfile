# Base image
FROM node:14-alpine

# Set working directory
WORKDIR /app

# Copy package.json and package-lock.json
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy all the files to the container
COPY . .

# Build the app
RUN npm run build

# Set environment variable
ENV NODE_ENV=production

# Expose the port
EXPOSE 8080

# Start the app
CMD ["npm", "run", "start"]