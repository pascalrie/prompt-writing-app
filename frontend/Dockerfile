# Use an official Node.js image as a base
FROM node:23-alpine

# Set working directory inside the container
WORKDIR /usr/src/app

# Copy package.json and yarn.lock for dependency installation
COPY package.json yarn.lock ./

# Install dependencies using Yarn
RUN yarn install

# Copy the rest of the application files into the container
COPY . .

# Expose port 3000 for the React development server
EXPOSE 3000

# Default command to start the React development server
CMD ["yarn", "start"]