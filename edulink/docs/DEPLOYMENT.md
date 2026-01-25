# Deployment Guide

## Overview

This guide covers deployment of Edulink SmartFees to various platforms including Docker, traditional servers, and cloud platforms.

## Prerequisites

- PHP 8.2 or higher
- Composer 2.x
- MySQL 8.0+
- Node.js 18+ and NPM
- Git

## Docker Deployment

### Local Development with Docker

1. **Clone the repository**
   ```bash
   git clone https://github.com/osumba404/IBL1-SmartFees.git
   cd IBL1-SmartFees/edulink
   ```

2. **Copy environment file**
   ```bash
   cp .env.example .env
   ```

3. **Update environment variables**
   ```env
   DB_HOST=db
   DB_DATABASE=edulink_smartfees
   DB_USERNAME=edulink_user
   DB_PASSWORD=edulink_password
   REDIS_HOST=redis
   ```

4. **Start containers**
   ```bash
   docker-compose up -d
   ```

5. **Install dependencies**
   ```bash
   docker-compose exec app composer install
   docker-compose exec app npm install
   ```

6. **Generate application key**
   ```bash
   docker-compose exec app php artisan key:generate
   ```

7. **Run migrations**
   ```bash
   docker-compose exec app php artisan migrate
   docker-compose exec app php artisan db:seed
   ```

8. **Build assets**
   ```bash
   docker-compose exec app npm run build
   ```

9. **Access the application**
   - Application: http://localhost:8000
   - phpMyAdmin: http://localhost:8080

### Production Docker Deployment

1. **Build production image**
   ```bash
   docker build --target production -t edulink-smartfees:latest .
   ```

2. **Run production container**
   ```bash
   docker run -d \
     --name edulink-app \
     -p 80:80 \
     -e APP_ENV=production \
     -e DB_HOST=your_db_host \
     -e DB_DATABASE=edulink_smartfees \
     -e DB_USERNAME=your_username \
     -e DB_PASSWORD=your_password \
     edulink-smartfees:latest
   ```

## Traditional Server Deployment

### Server Requirements

- Ubuntu 20.04+ or CentOS 8+
- Nginx or Apache
- PHP 8.2+ with required extensions
- MySQL 8.0+
- SSL certificate (Let's Encrypt recommended)

### Installation Steps

1. **Install PHP and extensions**
   ```bash
   sudo apt update
   sudo apt install php8.2-fpm php8.2-mysql php8.2-mbstring \
     php8.2-xml php8.2-bcmath php8.2-gd php8.2-zip php8.2-curl
   ```

2. **Install Composer**
   ```bash
   curl -sS https://getcomposer.org/installer | php
   sudo mv composer.phar /usr/local/bin/composer
   ```

3. **Clone and setup application**
   ```bash
   cd /var/www
   sudo git clone https://github.com/osumba404/IBL1-SmartFees.git
   cd IBL1-SmartFees/edulink
   sudo composer install --optimize-autoloader --no-dev
   sudo npm install
   sudo npm run build
   ```

4. **Configure environment**
   ```bash
   sudo cp .env.example .env
   sudo php artisan key:generate
   # Edit .env with production settings
   ```

5. **Set permissions**
   ```bash
   sudo chown -R www-data:www-data /var/www/IBL1-SmartFees
   sudo chmod -R 755 /var/www/IBL1-SmartFees/edulink/storage
   sudo chmod -R 755 /var/www/IBL1-SmartFees/edulink/bootstrap/cache
   ```

6. **Run migrations**
   ```bash
   sudo php artisan migrate --force
   ```

7. **Configure Nginx**
   ```nginx
   server {
       listen 80;
       server_name your-domain.com;
       root /var/www/IBL1-SmartFees/edulink/public;

       add_header X-Frame-Options "SAMEORIGIN";
       add_header X-Content-Type-Options "nosniff";

       index index.php;

       charset utf-8;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location = /favicon.ico { access_log off; log_not_found off; }
       location = /robots.txt  { access_log off; log_not_found off; }

       error_page 404 /index.php;

       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
           include fastcgi_params;
       }

       location ~ /\.(?!well-known).* {
           deny all;
       }
   }
   ```

8. **Setup SSL with Let's Encrypt**
   ```bash
   sudo apt install certbot python3-certbot-nginx
   sudo certbot --nginx -d your-domain.com
   ```

9. **Optimize for production**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## Cloud Platform Deployment

### Render.com

1. **Create new Web Service**
   - Connect GitHub repository
   - Select branch (main/develop)

2. **Configure build settings**
   ```bash
   Build Command: composer install --optimize-autoloader --no-dev && npm install && npm run build
   Start Command: php artisan serve --host=0.0.0.0 --port=$PORT
   ```

3. **Environment variables**
   - Add all required .env variables
   - Set APP_ENV=production
   - Set APP_DEBUG=false

4. **Database setup**
   - Create PostgreSQL or MySQL database
   - Update DB_* environment variables

5. **Run migrations**
   - Use Render shell or add to build command

### Fly.io

1. **Install Fly CLI**
   ```bash
   curl -L https://fly.io/install.sh | sh
   ```

2. **Initialize Fly app**
   ```bash
   fly launch
   ```

3. **Configure fly.toml**
   ```toml
   [build]
     builder = "paketobuildpacks/builder:base"

   [env]
     APP_ENV = "production"

   [[services]]
     internal_port = 8000
     protocol = "tcp"
   ```

4. **Deploy**
   ```bash
   fly deploy
   ```

### Heroku

1. **Install Heroku CLI**
   ```bash
   # macOS
   brew tap heroku/brew && brew install heroku
   ```

2. **Login and create app**
   ```bash
   heroku login
   heroku create edulink-smartfees
   ```

3. **Add buildpacks**
   ```bash
   heroku buildpacks:add heroku/php
   heroku buildpacks:add heroku/nodejs
   ```

4. **Configure environment**
   ```bash
   heroku config:set APP_ENV=production
   heroku config:set APP_DEBUG=false
   # Add other environment variables
   ```

5. **Add database**
   ```bash
   heroku addons:create cleardb:ignite
   ```

6. **Deploy**
   ```bash
   git push heroku main
   heroku run php artisan migrate
   ```

## Post-Deployment Checklist

- [ ] Environment variables configured
- [ ] Database migrations run
- [ ] Application key generated
- [ ] Storage permissions set
- [ ] SSL certificate installed
- [ ] Configuration cached
- [ ] Routes cached
- [ ] Views cached
- [ ] Queue worker configured
- [ ] Scheduled tasks configured
- [ ] Error logging configured
- [ ] Backup strategy in place
- [ ] Monitoring setup
- [ ] Performance optimization

## Performance Optimization

### Caching

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Cache events
php artisan event:cache
```

### Database Optimization

- Enable query caching
- Add database indexes
- Optimize slow queries
- Use database connection pooling

### Asset Optimization

```bash
# Production build
npm run build

# Minify assets
npm run production
```

## Monitoring

### Application Monitoring

- Set up error tracking (Sentry, Bugsnag)
- Monitor application logs
- Track performance metrics
- Set up uptime monitoring

### Database Monitoring

- Monitor query performance
- Track slow queries
- Monitor connection pool
- Set up database backups

## Backup Strategy

### Database Backups

```bash
# Automated daily backup
mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql
```

### File Backups

- Backup storage directory
- Backup uploaded files
- Backup configuration files

## Troubleshooting

### Common Issues

1. **500 Internal Server Error**
   - Check file permissions
   - Review error logs
   - Verify .env configuration

2. **Database Connection Error**
   - Verify database credentials
   - Check database server status
   - Verify network connectivity

3. **Asset Loading Issues**
   - Run `npm run build`
   - Check public/build directory
   - Verify asset URLs

## Security Considerations

- Use HTTPS only
- Set secure environment variables
- Enable firewall rules
- Regular security updates
- Implement rate limiting
- Monitor for vulnerabilities

## Support

For deployment issues, contact:
- Email: support@edulink.ac.ke
- GitHub Issues: https://github.com/osumba404/IBL1-SmartFees/issues
