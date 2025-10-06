# OLT Management Consolidation Todo

## Phase 1: Preparation & Backup
- [ ] Create backup branch
- [ ] Create working branch
- [ ] Document current state

## Phase 2: Model Consolidation
- [ ] Update OLT model with all required fields
- [ ] Delete OltDevice.php model
- [ ] Verify OLT model completeness

## Phase 3: Controller Consolidation
- [ ] Review and merge OltDeviceController methods into OLTController
- [ ] Rename OLTController to OltController (Laravel convention)
- [ ] Delete OltDeviceController.php
- [ ] Verify all CRUD methods exist

## Phase 4: Service Updates
- [ ] Update OltConfigurationService.php imports
- [ ] Update OltDataCollector.php imports
- [ ] Update SnmpManager.php imports
- [ ] Update VendorService.php imports
- [ ] Update VendorDriverInterface.php imports
- [ ] Update AbstractVendorDriver.php imports
- [ ] Update FiberhomeDriver.php imports
- [ ] Update HuaweiDriver.php imports
- [ ] Update ZteDriver.php imports
- [ ] Update PollOltJob.php imports
- [ ] Update DiscoverOnuJob.php imports
- [ ] Update SendAlertJob.php imports
- [ ] Clean up DiscoveryService.php (remove dual usage)
- [ ] Clean up OLTService.php (remove dual usage)

## Phase 5: Route Updates
- [ ] Update web.php routes to use OltController
- [ ] Update api.php routes to use OltController
- [ ] Verify all route references

## Phase 6: Database Cleanup
- [ ] Delete create_olt_devices_table.php migration
- [ ] Update foreign keys in create_olt_cards_table.php
- [ ] Update foreign keys in create_olt_pon_ports_table.php
- [ ] Update foreign keys in create_olt_performance_logs_table.php

## Phase 7: View & Frontend Updates
- [ ] Update view route references if needed
- [ ] Update JavaScript API endpoints if needed
- [ ] Verify frontend functionality

## Phase 8: Table & DataTable Updates
- [ ] Delete OltDeviceTable.php
- [ ] Verify DataTable functionality

## Phase 9: Testing
- [ ] Test OLT CRUD operations
- [ ] Test SNMP connection
- [ ] Test data collection
- [ ] Test background jobs
- [ ] Test API endpoints
- [ ] Test DataTables
- [ ] Test dashboard

## Phase 10: Documentation & Commit
- [ ] Update CHANGELOG.md
- [ ] Create consolidation summary document
- [ ] Commit all changes
- [ ] Push to GitHub
- [ ] Update pull request