# OLT Management Consolidation Todo

## Phase 1: Preparation & Backup
- [x] Create backup branch
- [x] Create working branch
- [x] Document current state

## Phase 2: Model Consolidation
- [x] Update OLT model with all required fields
- [ ] Delete OltDevice.php model
- [x] Verify OLT model completeness

## Phase 3: Controller Consolidation
- [x] Review and merge OltDeviceController methods into OLTController
- [x] Rename OLTController to OltController (Laravel convention)
- [ ] Delete OltDeviceController.php and old OLTController.php
- [x] Verify all CRUD methods exist

## Phase 4: Service Updates
- [x] Update OltConfigurationService.php imports
- [x] Update OltDataCollector.php imports
- [x] Update SnmpManager.php imports
- [x] Update VendorService.php imports
- [x] Update VendorDriverInterface.php imports
- [x] Update AbstractVendorDriver.php imports
- [x] Update FiberhomeDriver.php imports
- [x] Update HuaweiDriver.php imports
- [x] Update ZteDriver.php imports
- [x] Update PollOltJob.php imports
- [x] Update DiscoverOnuJob.php imports
- [x] Update SendAlertJob.php imports
- [x] Clean up DiscoveryService.php (remove dual usage)
- [x] Clean up OLTService.php (remove dual usage)

## Phase 5: Route Updates
- [x] Update web.php routes to use OltController
- [x] Update api.php routes to use OltController
- [x] Verify all route references

## Phase 6: Database Cleanup
- [x] Delete create_olt_devices_table.php migration
- [x] Update foreign keys in create_olt_cards_table.php (already correct)
- [x] Update foreign keys in create_olt_pon_ports_table.php (already correct)
- [x] Update foreign keys in create_olt_performance_logs_table.php (already correct)

## Phase 7: View & Frontend Updates
- [x] Update view route references if needed
- [x] Update JavaScript API endpoints if needed
- [x] Update model relationships (BandwidthProfile, OltCard, OltPerformanceLog, OltPonPort, Onu)
- [x] Update TopologyController references
- [x] Update migration comments
- [ ] Verify frontend functionality

## Phase 8: Table & DataTable Updates
- [x] Delete OltDeviceTable.php
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
- [x] Update CHANGELOG.md
- [x] Create consolidation summary document
- [x] Commit all changes
- [x] Push to GitHub
- [x] Create pull request (PR #2)