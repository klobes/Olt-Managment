# Udhëzues Instalimi - Fiberhome OLT Manager

## Hapat e Instalimit

### 1. Përgatitja e Sistemit

#### Instalimi i PHP SNMP Extension

**Ubuntu/Debian:**
```bash
# Instalimi i paketave të nevojshme
sudo apt-get update
sudo apt-get install -y php-snmp snmp snmpd libsnmp-dev

# Restart i PHP-FPM
sudo systemctl restart php8.1-fpm  # Ndrysho version sipas nevojës

# Verifikimi
php -m | grep snmp
```

**CentOS/RHEL:**
```bash
# Instalimi i paketave
sudo yum install -y php-snmp net-snmp net-snmp-utils

# Restart i PHP-FPM
sudo systemctl restart php-fpm

# Verifikimi
php -m | grep snmp
```

**macOS (Homebrew):**
```bash
brew install net-snmp
pecl install snmp
```

### 2. Instalimi i Plugin-it

#### Metoda 1: Manual

1. Kopjoni direktorinë `fiberhome-olt-manager` në `platform/plugins/`:
```bash
cp -r fiberhome-olt-manager /path/to/botble/platform/plugins/
```

2. Vendosni permissions:
```bash
cd /path/to/botble
chmod -R 755 platform/plugins/fiberhome-olt-manager
chown -R www-data:www-data platform/plugins/fiberhome-olt-manager
```

#### Metoda 2: Composer (nëse është në repository)

```bash
composer require botble/fiberhome-olt-manager
```

### 3. Konfigurimi

1. Kopjoni `.env.example` në `.env` të projektit:
```bash
cat platform/plugins/fiberhome-olt-manager/.env.example >> .env
```

2. Modifikoni vlerat sipas nevojës:
```env
OLT_SNMP_VERSION=2c
OLT_SNMP_COMMUNITY=public
OLT_SNMP_TIMEOUT=1000000
OLT_SNMP_RETRIES=3
```

### 4. Aktivizimi i Plugin-it

#### Nga Admin Panel:

1. Hyni në Admin Panel
2. Shkoni në **Plugins**
3. Gjeni **Fiberhome OLT Manager**
4. Klikoni **Activate**

#### Nga Command Line:

```bash
php artisan plugin:activate fiberhome-olt-manager
```

### 5. Ekzekutimi i Migrations

```bash
php artisan migrate
```

### 6. Publikimi i Assets (opsionale)

```bash
php artisan vendor:publish --tag=fiberhome-olt-assets
```

### 7. Clear Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Verifikimi i Instalimit

### 1. Kontrolloni SNMP Extension

```bash
php -r "if (extension_loaded('snmp')) { echo 'SNMP extension is loaded'; } else { echo 'SNMP extension is NOT loaded'; }"
```

### 2. Testoni SNMP Connection

```bash
# Testoni me një OLT të vërtetë
snmpwalk -v2c -c public <OLT_IP> 1.3.6.1.2.1.1.1.0
```

### 3. Kontrolloni Database Tables

```bash
php artisan tinker
```

```php
// Në tinker
use Botble\FiberhomeOltManager\Models\OltDevice;
OltDevice::count();
```

### 4. Aksesoni Dashboard

Shkoni në: `https://your-domain.com/admin/fiberhome-olt`

## Konfigurimi i Cron Jobs (Opsionale)

Për sinkronizim automatik të të dhënave, shtoni në crontab:

```bash
# Hapni crontab
crontab -e

# Shtoni këto rreshta
# Sync të dhënat çdo 5 minuta
*/5 * * * * cd /path/to/botble && php artisan fiberhome-olt:sync-all >> /dev/null 2>&1

# Pastro logs të vjetër çdo ditë
0 2 * * * cd /path/to/botble && php artisan fiberhome-olt:clean-logs >> /dev/null 2>&1
```

## Konfigurimi i Queue Workers (Opsionale)

Për performance më të mirë, përdorni queue workers:

1. Konfiguroni queue driver në `.env`:
```env
QUEUE_CONNECTION=redis
```

2. Startoni queue worker:
```bash
php artisan queue:work --queue=fiberhome-olt
```

3. Ose përdorni Supervisor:

```ini
[program:fiberhome-olt-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/botble/artisan queue:work --queue=fiberhome-olt --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/botble/storage/logs/worker.log
```

## Troubleshooting

### Problem: SNMP Extension Not Found

**Zgjidhja:**
```bash
# Kontrolloni nëse është instaluar
dpkg -l | grep php-snmp

# Nëse nuk është, instalojeni
sudo apt-get install php-snmp

# Restart PHP-FPM
sudo systemctl restart php8.1-fpm
```

### Problem: Permission Denied

**Zgjidhja:**
```bash
# Vendosni permissions
sudo chown -R www-data:www-data platform/plugins/fiberhome-olt-manager
sudo chmod -R 755 platform/plugins/fiberhome-olt-manager
```

### Problem: Migration Failed

**Zgjidhja:**
```bash
# Rollback dhe re-run
php artisan migrate:rollback --path=platform/plugins/fiberhome-olt-manager/database/migrations
php artisan migrate --path=platform/plugins/fiberhome-olt-manager/database/migrations
```

### Problem: SNMP Timeout

**Zgjidhja:**
1. Rritni timeout në `.env`:
```env
OLT_SNMP_TIMEOUT=5000000
OLT_SNMP_RETRIES=5
```

2. Kontrolloni firewall:
```bash
# Hapni port 161 (SNMP)
sudo ufw allow 161/udp
```

### Problem: Cache Issues

**Zgjidhja:**
```bash
# Clear all cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Restart services
sudo systemctl restart php8.1-fpm
sudo systemctl restart nginx  # ose apache2
```

## Konfigurimi i Firewall

### UFW (Ubuntu)

```bash
# Lejo SNMP
sudo ufw allow 161/udp

# Lejo nga IP specifike
sudo ufw allow from <OLT_IP> to any port 161 proto udp
```

### iptables

```bash
# Lejo SNMP
sudo iptables -A INPUT -p udp --dport 161 -j ACCEPT

# Save rules
sudo iptables-save > /etc/iptables/rules.v4
```

## Optimizimi

### 1. Database Indexing

Migrations tashmë përmbajnë indexes, por mund të shtoni më shumë:

```sql
CREATE INDEX idx_olt_devices_status ON olt_devices(status);
CREATE INDEX idx_onus_status ON onus(status);
CREATE INDEX idx_performance_logs_recorded_at ON olt_performance_logs(recorded_at);
```

### 2. Redis Cache

```bash
# Instaloni Redis
sudo apt-get install redis-server

# Konfiguroni në .env
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 3. Database Connection Pooling

Në `config/database.php`:

```php
'mysql' => [
    // ...
    'options' => [
        PDO::ATTR_PERSISTENT => true,
    ],
],
```

## Backup dhe Restore

### Backup

```bash
# Backup database
php artisan backup:run --only-db

# Backup files
tar -czf fiberhome-olt-backup.tar.gz platform/plugins/fiberhome-olt-manager
```

### Restore

```bash
# Restore database
mysql -u username -p database_name < backup.sql

# Restore files
tar -xzf fiberhome-olt-backup.tar.gz -C /path/to/botble/
```

## Përditësimi

```bash
# Pull latest changes
cd platform/plugins/fiberhome-olt-manager
git pull origin main

# Run migrations
php artisan migrate

# Clear cache
php artisan cache:clear
php artisan config:clear
```

## Mbështetje

Për probleme të tjera, kontaktoni:
- Email: support@example.com
- Documentation: https://docs.example.com
- GitHub Issues: https://github.com/example/fiberhome-olt-manager/issues