# Live Deployment Instructions

## Overview

This document provides step-by-step instructions for deploying Edulink SmartFees to a live platform.

## Platform Options

### Option 1: Render.com (Recommended for Free Tier)

1. **Create Account**
   - Go to https://render.com
   - Sign up with GitHub account

2. **Create New Web Service**
   - Click "New +" → "Web Service"
   - Connect your GitHub repository
   - Select branch (main/develop)

3. **Configure Build Settings**
   ```
   Build Command: composer install --optimize-autoloader --no-dev && npm install && npm run build
   Start Command: php artisan serve --host=0.0.0.0 --port=$PORT
   ```

4. **Add Environment Variables**
   - APP_ENV=production
   - APP_DEBUG=false
   - APP_URL=https://your-app-name.onrender.com
   - DB_HOST=your_db_host
   - DB_DATABASE=your_database
   - DB_USERNAME=your_username
   - DB_PASSWORD=your_password
   - (Add all other required .env variables)

5. **Create Database**
   - Create PostgreSQL database on Render
   - Update DB_* environment variables

6. **Deploy**
   - Click "Create Web Service"
   - Wait for deployment
   - Run migrations: Use Render Shell or add to build command

### Option 2: Fly.io

1. **Install Fly CLI**
   ```bash
   curl -L https://fly.io/install.sh | sh
   ```

2. **Login**
   ```bash
   fly auth login
   ```

3. **Initialize App**
   ```bash
   fly launch
   ```

4. **Configure fly.toml**
   ```toml
   [build]
     builder = "paketobuildpacks/builder:base"
   
   [env]
     APP_ENV = "production"
   
   [[services]]
     internal_port = 8000
     protocol = "tcp"
   ```

5. **Deploy**
   ```bash
   fly deploy
   ```

### Option 3: Heroku

1. **Install Heroku CLI**
   ```bash
   # macOS
   brew tap heroku/brew && brew install heroku
   ```

2. **Login**
   ```bash
   heroku login
   ```

3. **Create App**
   ```bash
   heroku create edulink-smartfees
   ```

4. **Add Buildpacks**
   ```bash
   heroku buildpacks:add heroku/php
   heroku buildpacks:add heroku/nodejs
   ```

5. **Configure Environment**
   ```bash
   heroku config:set APP_ENV=production
   heroku config:set APP_DEBUG=false
   # Add all other environment variables
   ```

6. **Add Database**
   ```bash
   heroku addons:create cleardb:ignite
   ```

7. **Deploy**
   ```bash
   git push heroku main
   heroku run php artisan migrate
   ```

## Post-Deployment Steps

1. **Run Migrations**
   ```bash
   php artisan migrate --force
   ```

2. **Seed Database** (Optional)
   ```bash
   php artisan db:seed
   ```

3. **Create Admin User**
   ```bash
   php artisan admin:create
   ```

4. **Optimize**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

5. **Test Application**
   - Visit deployment URL
   - Test student login
   - Test admin login
   - Test payment flow (use sandbox credentials)

## Update README

After deployment, update README.md with:
- Live deployment URL
- QR code for easy access
- Deployment platform information

## Notes

- Use sandbox/test credentials for payment gateways initially
- Monitor logs for errors
- Set up automated backups
- Configure SSL/HTTPS
- Set up monitoring and alerts
