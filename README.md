# FiberHome OLT Manager Plugin për Botble CMS

## 📋 Përmbledhje

Plugin i plotë për menaxhimin e pajisjeve FiberHome OLT përmes SNMP në platformën Botble CMS. Ofron një ndërfaqe të përdorshme për administrimin e OLT-ve, ONU-ve dhe profileve të bandwidth-it.

## ✨ Karakteristikat Kryesore

### 🎯 Versioni 1.0.0 - Gati për Përdorim
- **Dashboard Interaktiv** me statistika në kohë reale
- **Menaxhim i plotë i OLT-ve** përmes SNMP
- **Menaxhim i ONU-ve** me mbështetje për profile të ndryshme
- **Profile Bandwidth-i** me konfigurim të lehtë
- **Network Topology** për vizualizimin e rrjetit
- **Sistem Alerts** për problemet dhe performancën
- **DataTables** për kërkim dhe filtrim të avancuar
- **API Endpoints** për integrime të jashtme

### 🔧 Karakteristika Teknike
- **SNMP Protocol**: Komunikim i sigurt me pajisjet
- **Auto Discovery**: Zbulim automatik i ONU-ve të reja
- **Performance Monitoring**: Monitorim i CPU, Memory, Temperature
- **Bandwidth Management**: Konfigurim i lehtë i profileve
- **Multi-language**: Mbështetje për gjuhë të ndryshme
- **Responsive Design**: Punon në të gjitha pajisjet

## 📦 Instalimi

### Hapi 1: Ekstraktoni Plugin-in
```bash
cd platform/plugins/
unzip fiberhome-olt-manager-plugin-v2.zip
```

### Hapi 2: Instaloni Dependencies
```bash
cd platform/
composer require php-snmp/snmp
php artisan migrate
```

### Hapi 3: Aktivizoni Plugin-in
1. Shkoni te **Admin Panel > Plugins**
2. Gjeni **FiberHome OLT Manager**
3. Klikoni **Activate**

### Hapi 4: Konfiguroni Settings
1. Shkoni te **FiberHome OLT > Settings**
2. Konfiguroni parametrat SNMP:
   - SNMP Timeout: 3000ms
   - SNMP Retries: 3
   - Polling Interval: 300s
3. Ruajeni konfigurimin

## 🚀 Përdorimi

### 1. Shtoni OLT të Ri
1. Shkoni te **FiberHome OLT > OLT Management**
2. Klikoni **Add OLT**
3. Plotësoni informacionin:
   - **Name**: Emri i OLT-së
   - **IP Address**: Adresa IP e OLT-së
   - **Model**: Modeli i OLT-së (AN5516-01, AN5516-02, etj)
   - **SNMP Community**: Komuniteti SNMP (default: public)
4. Klikoni **Save**

### 2. Menaxhoni ONU-t
1. Shkoni te **FiberHome OLT > ONU Management**
2. Shikoni listën e ONU-ve të zbuluara
3. Klikoni **Configure** për të konfiguruar një ONU
4. Caktoni:
   - Bandwidth Profile
   - VLAN ID
   - Service Type
   - Customer Information

### 3. Krijoni Profile Bandwidth
1. Shkoni te **FiberHome OLT > Bandwidth Profiles**
2. Klikoni **Create Profile**
3. Definoni:
   - **Name**: Emri i profilit
   - **Download/Upload Speed**: Shpejtësia në Mbps
   - **Priority**: Prioriteti (Low, Medium, High, Premium)
   - **Guaranteed Rate**: Përqindja e garantuar

### 4. Monitoroni Performancën
1. Shkoni te **Dashboard**
2. Shikoni grafikët e performancës:
   - CPU Usage
   - Memory Usage
   - Temperature
   - Online/Offline Status
3. Kontrolloni **Alerts** për probleme

### 5. Network Topology
1. Shkoni te **Network Topology**
2. Shikoni hartën vizuale të rrjetit
3. Shikoni lidhjet mes OLT-ve dhe ONU-ve
4. Identifikoni problemet në topologji

## 🔧 Konfigurimi i SNMP

### Për OLT FiberHome
```bash
# Në OLT, aktivizoni SNMP:
enable
configure terminal
snmp-agent community read public
snmp-agent community write private
snmp-agent sys-info version v2c
snmp-agent target-host inform address 192.168.1.100 params securityname public
```

### OID-të e përdorura
```php
// System OIDs
'1.3.6.1.2.1.1.1.0' => 'sysDescr'
'1.3.6.1.2.1.1.3.0' => 'sysUpTime'
'1.3.6.1.2.1.1.5.0' => 'sysName'

// Performance OIDs
'1.3.6.1.4.1.34592.1.3.2.1.1.1.1' => 'cpuUsage'
'1.3.6.1.4.1.34592.1.3.2.1.1.1.2' => 'memoryUsage'
'1.3.6.1.4.1.34592.1.3.2.1.1.1.3' => 'temperature'

// ONU OIDs
'1.3.6.1.4.1.34592.1.3.4.1.1.1.1' => 'onuSerialNumber'
'1.3.6.1.4.1.34592.1.3.4.1.1.1.2' => 'onuStatus'
'1.3.6.1.4.1.34592.1.3.4.1.1.1.3' => 'onuRxPower'
'1.3.6.1.4.1.34592.1.3.4.1.1.1.4' => 'onuTxPower'
```

## 🛠️ Troubleshooting

### Problem: OLT nuk lidhet
**Zgjidhja:**
1. Kontrolloni IP adresën
2. Verifikoni SNMP community
3. Kontrolloni firewall
4. Testoni lidhjen me: `snmpwalk -v2c -c public OLT_IP`

### Problem: ONU nuk shfaqet
**Zgjidhja:**
1. Kontrolloni nëse ONU është e lidhur
2. Verifikoni konfigurimin e OLT
3. Rifreskoni listën e ONU-ve
4. Kontrolloni logët e sistemit

### Problem: Performance data nuk shfaqet
**Zgjidhja:**
1. Verifikoni OID-të e SNMP
2. Kontrolloni versionin e firmware të OLT
3. Rifreskoni të dhënat manualisht
4. Kontrolloni timeout-in e SNMP

## 📊 API Endpoints

### OLT Management
```
GET    /api/fiberhome/olt              # List all OLTs
POST   /api/fiberhome/olt              # Create new OLT
GET    /api/fiberhome/olt/{id}         # Get OLT details
PUT    /api/fiberhome/olt/{id}         # Update OLT
DELETE /api/fiberhome/olt/{id}         # Delete OLT
POST   /api/fiberhome/olt/{id}/poll    # Poll OLT data
```

### ONU Management
```
GET    /api/fiberhome/onu              # List all ONUs
GET    /api/fiberhome/onu/{id}         # Get ONU details
PUT    /api/fiberhome/onu/{id}         # Update ONU
POST   /api/fiberhome/onu/{id}/configure # Configure ONU
POST   /api/fiberhome/onu/{id}/reboot  # Reboot ONU
```

### Bandwidth Profiles
```
GET    /api/fiberhome/bandwidth        # List profiles
POST   /api/fiberhome/bandwidth        # Create profile
PUT    /api/fiberhome/bandwidth/{id}   # Update profile
POST   /api/fiberhome/bandwidth/{id}/assign # Assign to ONU
```

## 🔄 Cron Jobs

### Për auto-polling
```bash
# Shtoni në crontab
*/5 * * * * cd /path/to/platform && php artisan fiberhome:poll >> /dev/null 2>&1
```

### Për auto-discovery
```bash
# Çdo 30 minuta
*/30 * * * * cd /path/to/platform && php artisan fiberhome:discover >> /dev/null 2>&1
```

## 📈 Performance Optimization

### Për performancë më të mirë:
1. **Përdorni caching** për të dhënat e SNMP
2. **Rregulloni polling interval** sipas nevojës
3. **Përdorni CDN** për asset-et
4. **Optimizoni databazën** me indexing
5. **Përdorni Redis** për sessione dhe cache

## 🔒 Siguria

### Masa të sigurisë:
1. **SNMP v3** për komunikim të sigurt
2. **Firewall rules** për akses të kufizuar
3. **SSL/TLS** për komunikimin web
4. **Rate limiting** për API endpoints
5. **Input validation** për të gjitha të dhënat

## 🆘 Mbështetje

### Nëse keni probleme:
1. Kontrolloni **log files** në `storage/logs/`
2. Verifikoni **konfigurimin** e SNMP
3. Kontrolloni **permissions** e file system
4. Rifreskoni **cache** me `php artisan cache:clear`

### Kontaktoni:
- **Email**: support@ninjatech.ai
- **Documentation**: [Dokumentacioni i Plotë](docs/)
- **Issues**: [Report Issues](https://github.com/your-repo/issues)

## 📄 Licenca

Ky plugin është i licencuar sipas **MIT License**. Mund ta përdorni për qëllime komerciale dhe private.

---

## 🎉 Faleminderit për përdorimin e FiberHome OLT Manager!

**Suksese në menaxhimin e rrjetit tuaj të fibrave!** 🚀

*Zhvilluar me ❤️ nga NinjaTech AI*