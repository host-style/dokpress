# Dokploy - WordPress Stack

Docker stack for WordPress with Nginx, PHP-FPM, MariaDB and Redis, optimized for Dokploy and Traefik.

## Technologies

- **Nginx 1.29.3** - Web server with security configurations
- **PHP 8.4-FPM** - With complete WordPress extensions
- **MariaDB 10.11** - Optimized database
- **Redis 7** - Object cache
- **Composer 2.9.2** - PHP dependency manager
- **WP-CLI** - WordPress command line interface
- **Node.js** - For asset building

## Features

### Performance
- Redis for object caching
- Configured OPcache
- MariaDB optimized for WordPress
- Static file compression and caching

### Security
- Rate limiting against DDoS
- Security headers (XSS, CSRF, Clickjacking)
- Malicious bot blocking
- Dangerous PHP functions disabled
- Sensitive file blocking

### Development
- Integrated WP-CLI
- Centralized logs
- Debug mode configurable via .env

### Operations
- Automated installation scripts
- Database backup and restore
- Complete health check
- Docker compose profiles

## Quick Setup

### 1. Clone and configure

```bash
# Copy configuration example
cp .env.example .env

# Edit with your credentials
nano .env
```

### 2. Configure .env

```env
APP_NAME=mysite
APP_DOMAIN=mysite.com
APP_ENVIRONMENT=production  # or development (APP_ENV variable also accepted)

MYSQL_DATABASE=mysite
MYSQL_USER=your_user
MYSQL_PASSWORD=secure_password_here
MYSQL_ROOT_PASSWORD=secure_root_password
```

### 3. Start containers

```bash
# Build
docker-compose build

# Start
docker-compose up -d
```

### 4. Install and Configure

```bash
# Install dependencies via Composer
docker compose exec cli composer install

# The command above automatically runs the WordPress setup
```

**Important:** Generate salt keys at https://api.wordpress.org/secret-key/1.1/salt/

### 5. Access

- **WordPress:** http://your-domain.com (or http://localhost)

## Project Structure

```
dokploy/
|-- .docker/
|   |-- nginx/              # Nginx configuration
|   |-- php/                # Dockerfile + php.ini
|   |-- php-cli/            # CLI Dockerfile for development
|   |-- mariadb/            # MySQL configuration
|-- app/                    # Project PHP code
|   |-- Command/            # Symfony CLI commands
|   |-- Config/             # Application configurations
|   |-- Security/           # Headers and safe login
|   |-- Service/            # Services (Environment, Cache)
|-- public/                 # WordPress
|   |-- wp-core/            # WordPress core
|   |-- wp-content/         # Plugins, themes, uploads
|-- vendor/                 # Composer dependencies
|-- .env                    # Configuration (not versioned)
|-- .env.example            # Configuration template
|-- composer.json           # Project dependencies
|-- console                 # Project CLI
|-- docker-compose.yml
|-- README.md
```

## Available Commands

### Project Console
```bash
# Complete setup (copy configs + install WordPress + configure language)
docker compose exec cli php console dokpress:setup

# Copy configuration files
docker compose exec cli php console dokpress:copy-config-files

# Update salt keys in .env
docker compose exec cli php console dokpress:update-salts

# Deploy WordPress (install core, languages, plugins)
docker compose exec cli php console dokpress:wordpress-deploy
```

### Docker Compose
```bash
# View logs
docker-compose logs -f

# Service-specific logs
docker-compose logs -f php-fpm

# Rebuild
docker-compose build --no-cache
docker-compose up -d

# Stop all
docker-compose down
```

### WP-CLI
```bash
# Access container
docker exec -it php-fpm bash

# WP-CLI commands
wp core version
wp plugin list
wp cache flush
```

### Composer and Node
```bash
docker exec -it php-fpm bash
composer install
npm install
yarn build
```

## Security

### Implemented Configurations

**Nginx:**
- Rate limiting (10 req/s general, 5 req/min login)
- Complete security headers
- Malicious bot blocking
- Sensitive file blocking (.sql, .git, etc)

**PHP:**
- Dangerous functions disabled
- `expose_php = Off`
- Secure sessions
- Error logging enabled

**MariaDB:**
- Credentials via environment variables
- `local_infile = 0`
- Charset utf8mb4

**WordPress (app/wp.php):**
- `DISALLOW_FILE_EDIT = true`
- Automatic HTTPS detection via proxy
- Limited revisions
- Optimized auto-save

### Pre-Production Checklist

- [ ] Change all passwords in `.env`
- [ ] Generate unique salt keys
- [ ] Set `APP_ENV=production`
- [ ] Disable debug in wp-config
- [ ] Configure HTTPS via Traefik
- [ ] Enable `FORCE_SSL_ADMIN`
- [ ] Install security plugin
- [ ] Configure automatic backups

## Traefik and HTTPS

This stack is configured to work with **Traefik** (managed by Dokploy).

Traefik handles:
- Automatic SSL/TLS certificates (Let's Encrypt)
- HTTP to HTTPS redirection
- Load balancing
- HTTPS detection via `X-Forwarded-Proto`

## Monitoring

### Automatic Health Checks
- **Nginx:** `/health` endpoint
- **Redis:** `redis-cli ping`
- **MariaDB:** mysqladmin ping
- **PHP-FPM:** Docker healthcheck

### Logs
```bash
# All logs
docker-compose logs -f

# Specific logs
docker-compose logs -f nginx
docker-compose logs -f php-fpm
docker-compose logs -f mariadb
docker-compose logs -f redis
```

## Performance

### Configured Cache
- **Redis:** 256MB, LRU policy
- **OPcache:** 128MB, 10k files
- **MariaDB:** Query cache 64MB
- **Nginx:** Static cache with expiration

### MariaDB Optimizations
- Buffer pool: 512MB
- Query cache: 64MB
- Max connections: 100
- Charset: utf8mb4

## Development Environment

```bash
# Use php.dev.ini for development
# In docker-compose.yml, change:
- ./.docker/php/php.dev.ini:/usr/local/etc/php/conf.d/custom.ini
```

**php.dev.ini includes:**
- `display_errors = On`
- `opcache.validate_timestamps = 1`
- Constant revalidation

## Installed PHP Extensions

- gd (images: PNG, JPEG, WebP, XPM)
- mysqli, pdo_mysql (database)
- opcache (performance)
- curl (HTTP requests)
- zip (compression)
- exif (image metadata)
- intl (internationalization)
- bcmath (precise calculations)
- soap (web services)
- sockets (connections)
- mbstring (multibyte strings)
- xml (XML processing)
- redis (cache via PECL)

## Troubleshooting

### WordPress not loading
```bash
# Check logs
docker-compose logs nginx php-fpm

# Check files
ls -la public/

# Check permissions
docker exec -u root php-fpm chown -R www-data:www-data /var/www/html
```

### Database not connecting
```bash
# Check if MariaDB is running
docker ps | grep mariadb

# Test connection
docker exec mariadb mysql -uroot -p${MYSQL_ROOT_PASSWORD} -e "SHOW DATABASES;"

# Check credentials in wp-config.php
```

### Redis not working
```bash
# Check status
docker exec redis redis-cli ping

# View information
docker exec redis redis-cli INFO
```

### Clean and restart
```bash
docker-compose down -v
docker-compose build --no-cache
docker-compose up -d
```

## Additional Documentation

- [WordPress Documentation](https://wordpress.org/documentation/)
- [WP-CLI](https://wp-cli.org/)
- [Docker Compose](https://docs.docker.com/compose/)

## License

This project is open source.
