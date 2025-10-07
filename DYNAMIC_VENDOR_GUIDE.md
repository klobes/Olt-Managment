# 🎯 Dynamic Vendor-Based OLT Registration

## Overview
Sistemi i ri i regjistrimit të OLT-ve është plotësisht dinamik dhe bazuar në vendor. Përdoruesi zgjedh vendor-in dhe automatikisht ngarkohen modelet specifike për atë vendor.

---

## 🏭 Vendors të Mbështetur

### 1. FiberHome
**Modele:**
- **AN5516-01** - GPON OLT with 16 PON ports (1024 ONUs)
- **AN5516-04** - GPON OLT with 4 PON ports (256 ONUs)
- **AN5516-06** - GPON OLT with 6 PON ports (384 ONUs)
- **AN6000-17** - High-capacity GPON OLT (2048 ONUs)
- **AN5506-04** - Compact GPON OLT with 4 PON ports (256 ONUs)

### 2. Huawei
**Modele:**
- **MA5608T** - Optical Access OLT (512 ONUs, GPON/EPON)
- **MA5680T** - High-density OLT (2048 ONUs, GPON/EPON/10G-GPON)
- **MA5800-X7** - Next-generation OLT (4096 ONUs, GPON/10G-GPON/XG-PON)
- **MA5800-X15** - Ultra high-capacity OLT (8192 ONUs, GPON/10G-GPON/XG-PON)

### 3. ZTE
**Modele:**
- **C300** - Compact OLT (512 ONUs, GPON/EPON)
- **C320** - Medium-capacity OLT (2048 ONUs, GPON/EPON/10G-GPON)
- **C600** - High-capacity OLT (4096 ONUs, GPON/10G-GPON/XG-PON)
- **C650** - Ultra high-capacity OLT (8192 ONUs, GPON/10G-GPON/XG-PON/XGS-PON)

---

## 🎨 User Interface

### Add OLT Modal Structure

#### 1. Basic Information Card
- **Name** - Unique identifier for the OLT
- **IP Address** - Network address
- **Location** - Physical location
- **Description** - Optional notes

#### 2. Device Configuration Card
- **Vendor Selection** - Dropdown with FiberHome, Huawei, ZTE
- **Model Selection** - Dynamically loaded based on vendor
- **Model Details** - Auto-displayed after model selection:
  - Description
  - Max Ports
  - Max ONUs
  - Supported Technology (GPON, EPON, 10G-GPON, etc.)

#### 3. SNMP Configuration Card
- **SNMP Community** - Default: public
- **SNMP Version** - v1, v2c, v3
- **SNMP Port** - Default: 161
- **Test Connection** - Button to verify connectivity

---

## 🔄 Dynamic Workflow

### Step 1: Vendor Selection
```javascript
User selects vendor → AJAX call to /api/fiberhome-olt/vendors/{vendor}/models
                    → Models loaded dynamically
                    → Model dropdown enabled
```

### Step 2: Model Selection
```javascript
User selects model → Model info displayed from cached data
                   → Shows: description, max_ports, max_onus, technology
                   → Model details card appears
```

### Step 3: SNMP Configuration
```javascript
User fills SNMP details → Clicks "Test Connection"
                        → AJAX call to test connectivity
                        → Shows success/failure with device info
```

### Step 4: Save
```javascript
User clicks Save → Form data sent to /api/fiberhome-olt/devices
                 → Model specs auto-populated (max_ports, max_onus, technology)
                 → Device created with full configuration
                 → DataTable refreshed
```

---

## 📡 API Endpoints

### 1. Get All Vendors
```http
GET /api/fiberhome-olt/vendors
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "value": "fiberhome",
      "text": "FiberHome",
      "models_count": 5
    },
    {
      "value": "huawei",
      "text": "Huawei",
      "models_count": 4
    },
    {
      "value": "zte",
      "text": "ZTE",
      "models_count": 4
    }
  ]
}
```

### 2. Get Models for Vendor
```http
GET /api/fiberhome-olt/vendors/{vendor}/models
```

**Example:** `/api/fiberhome-olt/vendors/fiberhome/models`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "value": "AN5516-01",
      "text": "AN5516-01",
      "description": "GPON OLT with 16 PON ports",
      "max_ports": 16,
      "max_onus": 1024,
      "technology": ["GPON"]
    },
    ...
  ]
}
```

### 3. Get Model Details
```http
GET /api/fiberhome-olt/vendors/{vendor}/models/{model}
```

**Example:** `/api/fiberhome-olt/vendors/fiberhome/models/AN5516-01`

**Response:**
```json
{
  "success": true,
  "data": {
    "vendor": "fiberhome",
    "vendor_name": "FiberHome",
    "model": "AN5516-01",
    "name": "AN5516-01",
    "description": "GPON OLT with 16 PON ports",
    "max_ports": 16,
    "max_onus": 1024,
    "technology": ["GPON"]
  }
}
```

---

## ⚙️ Configuration

### Adding New Vendors/Models

Edit `config/olt-vendors.php`:

```php
'vendors' => [
    'new_vendor' => [
        'name' => 'New Vendor Name',
        'models' => [
            'MODEL-123' => [
                'name' => 'MODEL-123',
                'description' => 'Description here',
                'max_ports' => 16,
                'max_onus' => 1024,
                'technology' => ['GPON', 'EPON'],
            ],
        ],
    ],
],
```

### Model Properties

| Property | Type | Required | Description |
|----------|------|----------|-------------|
| `name` | string | Yes | Display name |
| `description` | string | Yes | Model description |
| `max_ports` | integer | Yes | Maximum PON ports |
| `max_onus` | integer | Yes | Maximum ONUs supported |
| `technology` | array | Yes | Supported technologies |

---

## 🎯 Features

### ✅ Dynamic Loading
- Models load automatically when vendor is selected
- No page refresh needed
- Fast AJAX-based updates

### ✅ Real-time Information
- Model specifications displayed immediately
- Helps users make informed decisions
- Reduces configuration errors

### ✅ Validation
- Required fields enforced
- IP address validation
- SNMP configuration validation
- Connection testing before save

### ✅ User Experience
- Clean, card-based UI
- Loading states for all operations
- Clear error messages
- Success confirmations

### ✅ Data Consistency
- Model specs auto-populated from config
- No manual entry of technical specs
- Consistent data across system

---

## 🔧 Technical Details

### Frontend (Blade + jQuery)
- **File:** `resources/views/olt/modals/add-olt.blade.php`
- **Features:**
  - Event-driven model loading
  - Dynamic form updates
  - AJAX form submission
  - Real-time validation

### Backend (Laravel)
- **Controller:** `src/Http/Controllers/VendorController.php`
- **Config:** `config/olt-vendors.php`
- **Routes:** `routes/api.php`

### Data Flow
```
User Action → Frontend JS → API Call → Controller → Config → Response → UI Update
```

---

## 📝 Usage Example

### 1. Open Add OLT Modal
Click "Add OLT" button on OLT management page

### 2. Fill Basic Info
- Name: `OLT-Main-01`
- IP: `192.168.1.100`
- Location: `Main Office`

### 3. Select Vendor
Choose `FiberHome` from dropdown
→ Models load automatically

### 4. Select Model
Choose `AN5516-01` from dropdown
→ Model details appear:
- Description: GPON OLT with 16 PON ports
- Max Ports: 16
- Max ONUs: 1024
- Technology: GPON

### 5. Configure SNMP
- Community: `public`
- Version: `v2c`
- Port: `161`

### 6. Test Connection
Click "Test Connection"
→ System verifies connectivity
→ Shows device information if successful

### 7. Save
Click "Save"
→ OLT created with all specifications
→ DataTable refreshes automatically

---

## 🎉 Benefits

1. **Faster Registration** - No need to manually enter technical specs
2. **Fewer Errors** - Specs come from validated config
3. **Better UX** - Clear, guided process
4. **Scalable** - Easy to add new vendors/models
5. **Consistent** - All OLTs have proper specifications
6. **Professional** - Clean, modern interface

---

## 🚀 Future Enhancements

Possible additions:
- [ ] Vendor logos in dropdown
- [ ] Model comparison feature
- [ ] Bulk OLT import
- [ ] Custom model definitions
- [ ] Firmware version tracking
- [ ] Model-specific configuration templates

---

**Status:** ✅ IMPLEMENTED  
**Version:** 1.0.0  
**Date:** 2025-10-06