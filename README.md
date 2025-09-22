# OLT Management System

A comprehensive Laravel-based management system for OLT (Optical Line Terminal) equipment supporting multiple vendors including Fiberhome, Huawei, and ZTE.

## Features

### Core Functionality
- **Multi-Vendor Support**: Fiberhome, Huawei, ZTE with extensible adapter pattern
- **Device Management**: Complete CRUD operations for OLT devices
- **Port Management**: Monitor and configure GPON/EPON ports
- **ONT Management**: Add, remove, and configure ONT devices
- **Real-time Monitoring**: System status, alarms, and performance metrics
- **SNMP Integration**: Automated data collection and synchronization

### Architecture Highlights
- **Vendor Abstraction Layer**: Clean separation between vendor-specific implementations
- **Service-Oriented Design**: Dedicated services for device operations
- **Comprehensive Logging**: Detailed logging for troubleshooting
- **RESTful API**: Complete API for integration with external systems
- **Database Optimization**: Proper indexing and relationships

## Installation

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL/PostgreSQL
- SNMP tools (for device communication)

### Setup Steps

1. **Install Dependencies**
   ```bash
   composer install
   ```

2. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database Setup**
   ```bash
   php artisan migrate
   ```

4. **Start Development Server**
   ```bash
   php artisan serve
   ```

## API Documentation

### Device Management

#### List Devices
```http
GET /api/devices
```

Query Parameters:
- `vendor`: Filter by vendor (fiberhome, huawei, zte)
- `status`: Filter by status (true/false)
- `per_page`: Items per page (default: 15)

#### Create Device
```http
POST /api/devices
```

```json
{
  "name": "OLT-001",
  "vendor": "fiberhome",
  "model": "AN5516-01",
  "ip_address": "192.168.1.100",
  "snmp_community": "public",
  "location": "Central Office",
  "port_count": 16
}
```

#### Sync Device
```http
POST /api/devices/{id}/sync
```

#### Add ONT
```http
POST /api/devices/{id}/add-ont
```

```json
{
  "port_number": 1,
  "serial_number": "FHTT12345678",
  "config": {
    "service_profile": "default",
    "bandwidth_profile": "100M"
  }
}
```

## Vendor Adapters

### Supported Vendors

#### Fiberhome
- Models: AN5516-01, AN5516-04, AN5516-06
- Protocol: SNMP v2c, Telnet
- Features: Full ONT management, port configuration

#### Huawei
- Models: MA5608T, MA5680T, MA5683T
- Protocol: SNMP v2c, SSH
- Features: Basic device info (expandable)

#### ZTE
- Models: C320, C300, C220
- Protocol: SNMP v2c, Telnet
- Features: Basic device info (expandable)

### Adding New Vendors

1. Create adapter class implementing `VendorAdapterInterface`
2. Extend `BaseAdapter` for common functionality
3. Add vendor to device model enum
4. Update device factory method

```php
<?php

namespace App\Services\Vendors;

class CustomVendorAdapter extends BaseAdapter
{
    public function connect(): bool
    {
        // Implement vendor-specific connection logic
    }
    
    // Implement other required methods...
}
```

## Database Schema

### Devices Table
- Device information and credentials
- Vendor-specific configuration
- Status and monitoring data

### Ports Table
- Port configuration and status
- Performance metrics
- ONT capacity tracking

### ONTs Table
- ONT device information
- Service configuration
- Customer data

### Monitoring Logs Table
- Historical performance data
- Alerts and thresholds
- System metrics

## Development Guidelines

### Code Organization
- Models: Database entities and relationships
- Services: Business logic and vendor communication
- Controllers: API endpoints and request handling
- Adapters: Vendor-specific implementations

### Testing Strategy
- Unit tests for adapters and services
- Integration tests for API endpoints
- Mock vendor responses for testing

### Security Considerations
- Encrypted credential storage
- Role-based access control
- API rate limiting
- Input validation and sanitization

## Monitoring and Alerts

### Metrics Collected
- Device temperature and power
- Port utilization and status
- ONT signal quality
- System performance

### Alert Levels
- **Info**: Normal operational events
- **Warning**: Threshold exceeded, attention needed
- **Critical**: Service affecting issues

## Contributing

1. Fork the repository
2. Create feature branch
3. Implement changes with tests
4. Submit pull request

## License

This project is licensed under the MIT License.