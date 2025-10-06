# 📘 Udhëzues për Shtimin e OLT - FiberHome OLT Manager

## 🎯 Përmbledhje

Ky udhëzues shpjegon si të shtosh një OLT device të ri në sistem me validim SNMP dhe marrje automatike të të dhënave.

---

## ✨ Karakteristikat e Reja

### 1. **UI i Përmirësuar për Add OLT**
- ✅ Modal i plotë me të gjitha fushat e nevojshme
- ✅ Vendor selection (FiberHome, Huawei, ZTE)
- ✅ Model selection bazuar në vendor
- ✅ SNMP configuration fields
- ✅ Test Connection button
- ✅ Real-time validation

### 2. **SNMP Connection Testing**
- ✅ Test connection para se të ruash OLT
- ✅ Validim i IP address dhe SNMP credentials
- ✅ Marrje e system information nga OLT
- ✅ Visual feedback (success/error messages)
- ✅ Disable submit button nëse connection fails

### 3. **Automatic Data Collection**
- ✅ Pas shtimit të OLT, sistemi automatikisht:
  - Teston connection
  - Merr system information
  - Merr cards/boards information
  - Merr PON ports information
  - Zbullon ONUs
  - Merr performance metrics

### 4. **Real-time Monitoring**
- ✅ DataTable auto-refresh çdo 30 sekonda
- ✅ Status indicators (online/offline/error)
- ✅ Quick actions (View, Edit, Sync, Test, Delete)
- ✅ Manual refresh button

---

## 📋 Si të Shtosh një OLT

### Hapi 1: Hap OLT Management
1. Hyr në Admin Panel
2. Shko te **FiberHome OLT > OLT Management**
3. Kliko butonin **"Add OLT"** (me ikonën +)

### Hapi 2: Plotëso Informacionin Bazë
```
Name: OLT-Main-01 (emër unik për OLT)
IP Address: 192.168.1.100 (IP address i OLT)
```

### Hapi 3: Zgjidh Vendor dhe Model
```
Vendor: FiberHome / Huawei / ZTE
Model: (lista e modeleve do të shfaqet bazuar në vendor)
```

**Modelet e Mbështetura:**
- **FiberHome:** AN5516-01, AN5516-02, AN5516-04, AN5516-06, AN5516-10
- **Huawei:** MA5608T, MA5680T, MA5683T, MA5800
- **ZTE:** C300, C320, C600, C650

### Hapi 4: Konfiguro SNMP
```
SNMP Version: SNMPv2c (recommended) / SNMPv3
SNMP Community: public (ose community string i OLT)
SNMP Port: 161 (default)
```

### Hapi 5: Shto Informacion Shtesë (Opsionale)
```
Location: Data Center A, Rack 5
Description: Main OLT for building A
```

### Hapi 6: Test Connection
1. Kliko butonin **"Test Connection"**
2. Prit për rezultatin:
   - ✅ **Success:** Connection successful, system info displayed
   - ❌ **Failed:** Error message shown, check IP/SNMP settings

### Hapi 7: Ruaj OLT
1. Nëse test connection është successful, butoni "Add OLT" aktivizohet
2. Kliko **"Add OLT"**
3. Sistemi do të:
   - Ruaj OLT në database
   - Testo connection përsëri
   - Filloj automatic data collection
   - Shfaq success message

---

## 🔍 Çfarë Ndodh Pas Shtimit

### 1. **Immediate Actions**
```
✅ OLT ruhet në database
✅ Status vendoset si "online" ose "offline"
✅ Connection test ekzekutohet
```

### 2. **Background Data Collection**
```
✅ System Information
   - System Description
   - System Name
   - Uptime
   - Location
   - Contact

✅ Hardware Information
   - Cards/Boards
   - PON Ports
   - Port Status

✅ ONU Discovery
   - Automatic ONU detection
   - ONU status
   - Optical power levels

✅ Performance Metrics
   - CPU Usage
   - Memory Usage
   - Temperature
```

### 3. **Real-time Updates**
```
✅ DataTable refresh automatikisht
✅ Status indicator update
✅ ONU count update
✅ Performance data collection starts
```

---

## 🎮 Operacionet e Disponueshme

### 1. **View Details** (👁️ ikona)
- Shfaq të gjitha detajet e OLT
- System information
- Performance metrics
- ONU count
- Last sync time

### 2. **Edit** (✏️ ikona)
- Ndrysho emrin, location, description
- Update SNMP settings
- Ndrysho model

### 3. **Sync Data** (🔄 ikona)
- Merr të dhëna të reja nga OLT
- Update system information
- Refresh ONU list
- Update performance metrics

### 4. **Test Connection** (🔌 ikona)
- Testo connection me OLT
- Verifikoni nëse OLT është i arritshëm
- Check SNMP communication

### 5. **Delete** (🗑️ ikona)
- Fshi OLT nga sistemi
- Fshi të gjitha të dhënat e lidhura
- Confirmation dialog para fshirjes

---

## ⚠️ Troubleshooting

### Problem: "Connection Failed" gjatë test
**Shkaqet e mundshme:**
1. IP address është i gabuar
2. OLT nuk është i arritshëm në rrjet
3. SNMP community është i gabuar
4. SNMP nuk është i aktivizuar në OLT
5. Firewall bllokon SNMP port (161)

**Zgjidhjet:**
```bash
# Test ping
ping 192.168.1.100

# Test SNMP manually
snmpwalk -v2c -c public 192.168.1.100

# Check firewall
sudo ufw status
sudo iptables -L
```

### Problem: OLT shtohet por status është "offline"
**Shkaqet:**
1. Connection u testua me sukses por OLT u fik pas
2. SNMP settings ndryshuan në OLT
3. Network issues

**Zgjidhjet:**
1. Kliko "Test Connection" për të verifikuar
2. Kliko "Sync Data" për të rifreskuar
3. Edit OLT dhe update SNMP settings

### Problem: Të dhënat nuk po mblidhen
**Shkaqet:**
1. Queue worker nuk po ekzekutohet
2. SNMP OIDs nuk janë të sakta për modelin
3. Permissions issues

**Zgjidhjet:**
```bash
# Check queue worker
ps aux | grep queue:work

# Start queue worker
php artisan queue:work

# Check logs
tail -f storage/logs/laravel.log
```

### Problem: DataTable nuk shfaq të dhëna
**Shkaqet:**
1. JavaScript errors
2. API endpoint nuk funksionon
3. CSRF token missing

**Zgjidhjet:**
```bash
# Check browser console (F12)
# Verify API endpoint
curl -X POST http://your-domain/api/fiberhome-olt/devices/datatable

# Clear cache
php artisan cache:clear
php artisan route:clear
```

---

## 📊 Performance Monitoring

### Automatic Monitoring
Pas shtimit të OLT, sistemi automatikisht:

1. **Polling Interval:** Çdo 5 minuta
   - CPU Usage
   - Memory Usage
   - Temperature
   - ONU Status

2. **Discovery Interval:** Çdo 30 minuta
   - New ONUs
   - ONU Status Changes
   - Port Status

3. **Data Retention:** 7 ditë
   - Performance logs
   - Historical data
   - Alerts

### Manual Operations
- **Sync Data:** Manualisht refresh data
- **Test Connection:** Verifikoni status
- **View Details:** Shiko performance metrics

---

## 🔐 Security Best Practices

### SNMP Configuration
```
✅ Use SNMPv3 when possible (more secure)
✅ Change default community string from "public"
✅ Use strong community strings
✅ Limit SNMP access by IP
✅ Use read-only community for monitoring
```

### Network Security
```
✅ Place OLTs in management VLAN
✅ Use firewall rules to restrict access
✅ Enable SNMP only on management interface
✅ Use VPN for remote access
✅ Monitor SNMP traffic
```

---

## 📈 Next Steps

### After Adding OLT
1. ✅ Verify connection status
2. ✅ Check ONU discovery
3. ✅ Configure bandwidth profiles
4. ✅ Assign profiles to ONUs
5. ✅ Monitor performance
6. ✅ Setup alerts

### Regular Maintenance
1. ✅ Check OLT status daily
2. ✅ Review performance metrics
3. ✅ Monitor ONU status
4. ✅ Update firmware when needed
5. ✅ Backup configuration

---

## 📞 Support

Nëse has probleme:
1. Check `storage/logs/laravel.log`
2. Check browser console (F12)
3. Verify SNMP configuration on OLT
4. Test network connectivity
5. Contact support: support@ninjatech.ai

---

**Version:** 1.0.0  
**Last Updated:** 2025-01-15  
**Status:** ✅ Production Ready