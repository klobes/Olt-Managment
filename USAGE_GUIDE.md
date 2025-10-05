# Udhëzues Përdorimi - Fiberhome OLT Manager

## Përmbajtja

1. [Hyrje](#hyrje)
2. [Dashboard](#dashboard)
3. [Menaxhimi i OLT](#menaxhimi-i-olt)
4. [Menaxhimi i ONU](#menaxhimi-i-onu)
5. [Bandwidth Profiles](#bandwidth-profiles)
6. [Monitorimi](#monitorimi)
7. [Shembuj Praktikë](#shembuj-praktikë)

## Hyrje

Fiberhome OLT Manager është një plugin i plotë për menaxhimin e pajisjeve Fiberhome OLT përmes protokollit SNMP. Plugin-i mundëson monitorimin, konfigurimin dhe menaxhimin e OLT, ONU, dhe shërbimeve të rrjetit.

## Dashboard

Dashboard-i ofron një pamje të përgjithshme të sistemit:

### Statistika Kryesore

- **Total OLTs**: Numri total i pajisjeve OLT
- **Online OLTs**: Numri i OLT që janë online
- **Total ONUs**: Numri total i ONU
- **Online ONUs**: Numri i ONU që janë online
- **Total PON Ports**: Numri total i portave PON
- **Active PON Ports**: Numri i portave PON aktive

### Performance Alerts

Dashboard-i shfaq alerts për:
- CPU utilization të lartë (> 80%)
- Memory utilization të lartë (> 85%)
- Temperature të lartë (> 70°C)
- Optical power jashtë range-it normal

### Lista e Fundit

- **Recent OLTs**: 5 OLT-të e fundit të shtuar
- **Offline ONUs**: 10 ONU-të e fundit që kanë shkuar offline

## Menaxhimi i OLT

### Shtimi i një OLT të Ri

1. **Navigoni në OLT Devices**
   - Klikoni në menunë: **OLT Manager > OLT Devices**
   - Klikoni butonin **Add OLT Device**

2. **Plotësoni Formën**
   ```
   Device Name: OLT-TIRANE-01
   IP Address: 192.168.1.100
   SNMP Community: public
   SNMP Version: 2c
   SNMP Port: 161
   Location: Tiranë, Shqipëri
   Description: OLT kryesor për zonën e Tiranës
   ```

3. **Ruani dhe Testoni**
   - Klikoni **Save**
   - Plugin-i automatikisht do të testojë lidhjen
   - Nëse lidhja është e suksesshme, do të fillojë mbledhja e të dhënave

### Shikimi i Detajeve të OLT

1. Klikoni në emrin e OLT nga lista
2. Do të shihni:
   - **System Information**: Informacion i përgjithshëm
   - **Cards**: Lista e kartave të instaluara
   - **PON Ports**: Lista e portave PON
   - **ONUs**: Lista e ONU të lidhura
   - **Performance**: Grafikë të performance

### Sinkronizimi i të Dhënave

Për të sinkronizuar të dhënat manualisht:

1. Hapni detajet e OLT
2. Klikoni butonin **Sync Data**
3. Prisni derisa procesi të përfundojë

**Shënim**: Sinkronizimi automatik ndodh çdo 5 minuta (mund të konfigurohet).

### Testimi i Lidhjes

Për të testuar lidhjen SNMP:

1. Hapni detajet e OLT
2. Klikoni butonin **Test Connection**
3. Do të shihni një mesazh sukses ose gabim

### Modifikimi i OLT

1. Hapni detajet e OLT
2. Klikoni butonin **Edit**
3. Modifikoni të dhënat e nevojshme
4. Klikoni **Save**

### Fshirja e OLT

1. Hapni detajet e OLT
2. Klikoni butonin **Delete**
3. Konfirmoni fshirjen

**Kujdes**: Fshirja e OLT do të fshijë edhe të gjitha të dhënat e lidhura (cards, PON ports, ONUs).

## Menaxhimi i ONU

### Shtimi i një ONU të Ri

1. **Navigoni në ONUs**
   - Klikoni në menunë: **OLT Manager > ONUs**
   - Klikoni butonin **Add ONU**

2. **Plotësoni Formën**
   ```
   OLT Device: OLT-TIRANE-01
   PON Port: PON 1/1/1
   ONU Name: ONU-CLIENT-001
   MAC Address: 00:11:22:33:44:55
   Serial Number: FHTT12345678
   Password: (opsionale)
   Auth Type: mac
   Description: ONU për klientin ABC
   ```

3. **Ruani**
   - Klikoni **Save**
   - ONU do të shtohet në whitelist të OLT

### Shikimi i Detajeve të ONU

1. Klikoni në emrin e ONU nga lista
2. Do të shihni:
   - **Basic Information**: Informacion bazë
   - **Optical Power**: Matje të fuqisë optike (RX/TX)
   - **Distance**: Distanca nga OLT
   - **Ports**: Lista e portave të ONU
   - **Status History**: Historia e statusit

### Enable/Disable ONU

#### Enable ONU

1. Nga lista e ONUs, gjeni ONU që dëshironi
2. Klikoni butonin **Enable**
3. Konfirmoni veprimin

Ose:

1. Hapni detajet e ONU
2. Klikoni butonin **Enable** në krye
3. Konfirmoni veprimin

#### Disable ONU

1. Nga lista e ONUs, gjeni ONU që dëshironi
2. Klikoni butonin **Disable**
3. Konfirmoni veprimin

**Shënim**: Disable do të çaktivizojë të gjitha portat e ONU.

### Reboot ONU

1. Hapni detajet e ONU
2. Klikoni butonin **Reboot**
3. Konfirmoni veprimin
4. Prisni 1-2 minuta derisa ONU të rifillojë

### Filtrimi dhe Kërkimi

#### Filtrim sipas OLT

1. Nga lista e ONUs
2. Zgjidhni OLT nga dropdown menu
3. Klikoni **Filter**

#### Filtrim sipas Status

1. Nga lista e ONUs
2. Zgjidhni status (Online, Offline, LOS, Dying Gasp)
3. Klikoni **Filter**

#### Kërkimi

1. Shkruani në search box:
   - Emrin e ONU
   - MAC address
   - Serial number
2. Klikoni **Search**

### Interpretimi i Optical Power

#### RX Power (Receive Power)

- **Normal**: -28 dBm deri -8 dBm
- **Low**: < -28 dBm (problem me fibër ose splitter)
- **High**: > -8 dBm (shumë afër OLT)

#### TX Power (Transmit Power)

- **Normal**: +2 dBm deri +7 dBm
- **Low**: < +2 dBm (problem me ONU)
- **High**: > +7 dBm (jo normale)

### Status të ONU

- **Online**: ONU është i lidhur dhe funksional
- **Offline**: ONU nuk është i lidhur
- **LOS (Loss of Signal)**: Humbje e sinjalit optik
- **Dying Gasp**: ONU ka humbur energjinë elektrike

## Bandwidth Profiles

### Krijimi i një Profili të Ri

1. **Navigoni në Bandwidth Profiles**
   - Klikoni në menunë: **OLT Manager > Bandwidth Profiles**
   - Klikoni butonin **Add Profile**

2. **Plotësoni Formën**
   ```
   OLT Device: OLT-TIRANE-01
   Profile Name: PROFILE-100M
   Upstream Min Rate: 10240 kbps (10 Mbps)
   Upstream Max Rate: 102400 kbps (100 Mbps)
   Downstream Min Rate: 10240 kbps (10 Mbps)
   Downstream Max Rate: 102400 kbps (100 Mbps)
   Fixed Rate: (opsionale)
   ```

3. **Ruani**
   - Klikoni **Save**
   - Profili do të krijohet në OLT

### Shembuj të Profilëve

#### Profil 50 Mbps

```
Profile Name: PROFILE-50M
Upstream Min Rate: 5120 kbps
Upstream Max Rate: 51200 kbps
Downstream Min Rate: 5120 kbps
Downstream Max Rate: 51200 kbps
```

#### Profil 100 Mbps

```
Profile Name: PROFILE-100M
Upstream Min Rate: 10240 kbps
Upstream Max Rate: 102400 kbps
Downstream Min Rate: 10240 kbps
Downstream Max Rate: 102400 kbps
```

#### Profil 200 Mbps

```
Profile Name: PROFILE-200M
Upstream Min Rate: 20480 kbps
Upstream Max Rate: 204800 kbps
Downstream Min Rate: 20480 kbps
Downstream Max Rate: 204800 kbps
```

### Aplikimi i Profilit në ONU

(Kjo funksionalitet do të implementohet në version të ardhshëm)

## Monitorimi

### Performance Monitoring

#### CPU Utilization

- Shfaqet në dashboard dhe detajet e OLT
- Alert nëse > 80%
- Grafikë për 24 orët e fundit

#### Memory Utilization

- Shfaqet në dashboard dhe detajet e OLT
- Alert nëse > 85%
- Grafikë për 24 orët e fundit

#### Temperature

- Shfaqet në dashboard dhe detajet e OLT
- Alert nëse > 70°C
- Grafikë për 24 orët e fundit

### Optical Power Monitoring

#### Për OLT PON Ports

- TX Power: Fuqia e transmetimit
- Voltage: Voltazhi
- Current: Rrymë
- Temperature: Temperatura

#### Për ONUs

- RX Power: Fuqia e marrjes
- TX Power: Fuqia e transmetimit
- Voltage: Voltazhi
- Current: Rrymë
- Temperature: Temperatura

### Alerts

Sistemi gjeneron alerts automatikisht për:

1. **CPU të lartë**: Kur CPU utilization > 80%
2. **Memory të lartë**: Kur memory utilization > 85%
3. **Temperature të lartë**: Kur temperature > 70°C
4. **Optical power të ulët**: Kur RX power < -28 dBm
5. **Optical power të lartë**: Kur RX power > -8 dBm

## Shembuj Praktikë

### Skenar 1: Shtimi i një Klienti të Ri

1. **Hapi 1**: Identifikoni OLT dhe PON port
   - Shkoni në **OLT Devices**
   - Gjeni OLT më të afërt me klientin
   - Kontrolloni PON ports për disponueshmëri

2. **Hapi 2**: Shtoni ONU
   - Shkoni në **ONUs > Add ONU**
   - Plotësoni të dhënat e ONU
   - Ruani

3. **Hapi 3**: Konfiguroni Bandwidth
   - Shkoni në **Bandwidth Profiles**
   - Zgjidhni profilin e duhur (p.sh., PROFILE-100M)
   - Aplikoni në ONU (nëse disponueshme)

4. **Hapi 4**: Verifikoni
   - Kontrolloni statusin e ONU (duhet të jetë Online)
   - Kontrolloni optical power (duhet të jetë në range normal)
   - Testoni lidhjen

### Skenar 2: Troubleshooting ONU Offline

1. **Hapi 1**: Identifikoni problemin
   - Shkoni në **ONUs**
   - Gjeni ONU që është offline
   - Hapni detajet

2. **Hapi 2**: Kontrolloni optical power
   - Shikoni RX power
   - Nëse është shumë i ulët (< -28 dBm):
     * Problem me fibër
     * Problem me splitter
     * Fibra e prerë

3. **Hapi 3**: Kontrolloni konfigurimin
   - Verifikoni që ONU është në whitelist
   - Verifikoni MAC address
   - Verifikoni PON port

4. **Hapi 4**: Provoni reboot
   - Klikoni **Reboot**
   - Prisni 1-2 minuta
   - Kontrolloni statusin

### Skenar 3: Monitorimi i Performance

1. **Hapi 1**: Hapni Dashboard
   - Shikoni statistikat e përgjithshme
   - Kontrolloni alerts

2. **Hapi 2**: Kontrolloni OLT specifike
   - Shkoni në **OLT Devices**
   - Hapni detajet e OLT
   - Shikoni grafikët e performance

3. **Hapi 3**: Identifikoni probleme
   - CPU i lartë: Shumë trafik ose problem me hardware
   - Memory i lartë: Leak ose shumë ONUs
   - Temperature i lartë: Problem me cooling

4. **Hapi 4**: Merrni masa
   - Nëse CPU/Memory i lartë: Konsideroni upgrade
   - Nëse temperature i lartë: Kontrolloni cooling system
   - Nëse optical power problematik: Kontrolloni fibrat

## Best Practices

### 1. Naming Convention

Përdorni një naming convention konsistente:

```
OLT: OLT-[QYTET]-[NUMER]
Shembull: OLT-TIRANE-01

ONU: ONU-[KLIENT]-[NUMER]
Shembull: ONU-ABC-001

Profile: PROFILE-[SPEED]
Shembull: PROFILE-100M
```

### 2. Documentation

Dokumentoni gjithmonë:
- Location të OLT
- Klientët e lidhur me çdo ONU
- Ndryshimet e konfigurimit
- Problemet dhe zgjidhjet

### 3. Monitoring

- Kontrolloni dashboard çdo ditë
- Reagoni shpejt ndaj alerts
- Monitoroni optical power rregullisht
- Mbani logs të performance

### 4. Maintenance

- Sinkronizoni të dhënat rregullisht
- Pastroni logs të vjetër
- Backup të databazës
- Update të plugin-it

### 5. Security

- Ndryshoni SNMP community nga "public"
- Përdorni SNMP v3 nëse është e mundur
- Kufizoni aksesin në OLT
- Monitoroni unauthorized access

## Pyetje të Shpeshta (FAQ)

### Si mund të ndryshoj SNMP community?

1. Shkoni në **OLT Devices**
2. Klikoni **Edit** për OLT
3. Ndryshoni **SNMP Community**
4. Ruani

### Pse ONU nuk shfaqet online?

Kontrollo:
1. ONU është në whitelist
2. MAC address është korrekt
3. Optical power është në range normal
4. PON port është enabled
5. Fibra është e lidhur

### Si mund të shoh historinë e performance?

1. Shkoni në **OLT Devices**
2. Hapni detajet e OLT
3. Scroll down në seksionin **Performance**
4. Shiko grafikët për 24 orët e fundit

### Si mund të eksportoj të dhënat?

(Kjo funksionalitet do të implementohet në version të ardhshëm)

## Mbështetje

Për pyetje dhe mbështetje:
- Email: support@example.com
- Documentation: https://docs.example.com
- GitHub: https://github.com/example/fiberhome-olt-manager