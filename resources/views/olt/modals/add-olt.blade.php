<div class="modal fade" id="add-olt-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="add-olt-form">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-plus"></i> {{ trans('plugins/fiberhome-olt-manager::olt.add_olt') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Connection Test Status -->
                    <div id="connection-status" class="alert" style="display: none;">
                        <i class="fa fa-info-circle"></i>
                        <span id="connection-message"></span>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add-name">{{ trans('plugins/fiberhome-olt-manager::olt.name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add-name" name="name" required placeholder="e.g., OLT-Main-01">
                                <small class="form-text text-muted">Unique name for this OLT device</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add-ip">{{ trans('plugins/fiberhome-olt-manager::olt.ip_address') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add-ip" name="ip_address" required placeholder="192.168.1.100">
                                <small class="form-text text-muted">IP address of the OLT device</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add-vendor">{{ trans('plugins/fiberhome-olt-manager::olt.vendor') }} <span class="text-danger">*</span></label>
                                <select class="form-control" id="add-vendor" name="vendor" required>
                                    <option value="">{{ trans('plugins/fiberhome-olt-manager::olt.select_vendor') }}</option>
                                    <option value="fiberhome">FiberHome</option>
                                    <option value="huawei">Huawei</option>
                                    <option value="zte">ZTE</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add-model">{{ trans('plugins/fiberhome-olt-manager::olt.model') }} <span class="text-danger">*</span></label>
                                <select class="form-control" id="add-model" name="model" required>
                                    <option value="">{{ trans('plugins/fiberhome-olt-manager::olt.select_model') }}</option>
                                    <!-- Options will be populated based on vendor selection -->
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="add-snmp-version">{{ trans('plugins/fiberhome-olt-manager::olt.snmp_version') }} <span class="text-danger">*</span></label>
                                <select class="form-control" id="add-snmp-version" name="snmp_version" required>
                                    <option value="2c" selected>SNMPv2c</option>
                                    <option value="3">SNMPv3</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="add-snmp-community">{{ trans('plugins/fiberhome-olt-manager::olt.snmp_community') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add-snmp-community" name="snmp_community" value="public" required>
                                <small class="form-text text-muted">SNMP community string</small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="add-snmp-port">{{ trans('plugins/fiberhome-olt-manager::olt.snmp_port') }}</label>
                                <input type="number" class="form-control" id="add-snmp-port" name="snmp_port" value="161" min="1" max="65535">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add-location">{{ trans('plugins/fiberhome-olt-manager::olt.location') }}</label>
                                <input type="text" class="form-control" id="add-location" name="location" placeholder="e.g., Data Center A, Rack 5">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add-description">{{ trans('plugins/fiberhome-olt-manager::olt.description') }}</label>
                                <textarea class="form-control" id="add-description" name="description" rows="2" placeholder="Optional description"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Test Connection Button -->
                    <div class="form-group">
                        <button type="button" class="btn btn-info btn-block" id="test-connection-btn">
                            <i class="fa fa-plug"></i> {{ trans('plugins/fiberhome-olt-manager::olt.test_connection') }}
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i> {{ trans('plugins/fiberhome-olt-manager::olt.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary" id="submit-olt-btn">
                        <i class="fa fa-save"></i> {{ trans('plugins/fiberhome-olt-manager::olt.add_olt') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Model options for each vendor
    var modelOptions = {
        'fiberhome': [
            { value: 'AN5516-01', text: 'AN5516-01' },
            { value: 'AN5516-02', text: 'AN5516-02' },
            { value: 'AN5516-04', text: 'AN5516-04' },
            { value: 'AN5516-06', text: 'AN5516-06' },
            { value: 'AN5516-10', text: 'AN5516-10' }
        ],
        'huawei': [
            { value: 'MA5608T', text: 'MA5608T' },
            { value: 'MA5680T', text: 'MA5680T' },
            { value: 'MA5683T', text: 'MA5683T' },
            { value: 'MA5800', text: 'MA5800' }
        ],
        'zte': [
            { value: 'C300', text: 'C300' },
            { value: 'C320', text: 'C320' },
            { value: 'C600', text: 'C600' },
            { value: 'C650', text: 'C650' }
        ]
    };
    
    // Update model options when vendor changes
    $('#add-vendor').on('change', function() {
        var vendor = $(this).val();
        var $modelSelect = $('#add-model');
        
        $modelSelect.empty();
        $modelSelect.append('<option value="">{{ trans("plugins/fiberhome-olt-manager::olt.select_model") }}</option>');
        
        if (vendor && modelOptions[vendor]) {
            $.each(modelOptions[vendor], function(index, model) {
                $modelSelect.append('<option value="' + model.value + '">' + model.text + '</option>');
            });
        }
    });
    
    // Test Connection
    $('#test-connection-btn').on('click', function() {
        var $btn = $(this);
        var $status = $('#connection-status');
        var $message = $('#connection-message');
        
        // Validate required fields
        var ipAddress = $('#add-ip').val();
        var snmpCommunity = $('#add-snmp-community').val();
        var snmpVersion = $('#add-snmp-version').val();
        var snmpPort = $('#add-snmp-port').val();
        
        if (!ipAddress || !snmpCommunity) {
            $status.removeClass('alert-success alert-danger alert-info').addClass('alert-warning').show();
            $message.text('{{ trans("plugins/fiberhome-olt-manager::olt.fill_required_fields") }}');
            return;
        }
        
        // Show loading
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> {{ trans("plugins/fiberhome-olt-manager::olt.testing") }}');
        $status.removeClass('alert-success alert-danger alert-warning').addClass('alert-info').show();
        $message.text('{{ trans("plugins/fiberhome-olt-manager::olt.testing_connection") }}');
        
        // Test connection
        $.ajax({
            url: '/api/fiberhome-olt/devices/test-connection',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                ip_address: ipAddress,
                snmp_community: snmpCommunity,
                snmp_version: snmpVersion,
                snmp_port: snmpPort
            },
            success: function(response) {
                if (response.success) {
                    $status.removeClass('alert-info alert-danger').addClass('alert-success');
                    $message.html('<strong>{{ trans("plugins/fiberhome-olt-manager::olt.connection_successful") }}</strong><br>' + 
                                 (response.data ? response.data.system_description || '' : ''));
                    $('#submit-olt-btn').prop('disabled', false);
                } else {
                    $status.removeClass('alert-info alert-success').addClass('alert-danger');
                    $message.html('<strong>{{ trans("plugins/fiberhome-olt-manager::olt.connection_failed") }}</strong><br>' + 
                                 (response.message || ''));
                    $('#submit-olt-btn').prop('disabled', true);
                }
            },
            error: function(xhr) {
                $status.removeClass('alert-info alert-success').addClass('alert-danger');
                var errorMsg = '{{ trans("plugins/fiberhome-olt-manager::olt.connection_error") }}';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg += '<br>' + xhr.responseJSON.message;
                }
                $message.html('<strong>{{ trans("plugins/fiberhome-olt-manager::olt.connection_failed") }}</strong><br>' + errorMsg);
                $('#submit-olt-btn').prop('disabled', true);
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fa fa-plug"></i> {{ trans("plugins/fiberhome-olt-manager::olt.test_connection") }}');
            }
        });
    });
    
    // Form submission
    $('#add-olt-form').on('submit', function(e) {
        e.preventDefault();
        
        var $submitBtn = $('#submit-olt-btn');
        $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> {{ trans("plugins/fiberhome-olt-manager::olt.adding") }}');
        
        $.ajax({
            url: '/api/fiberhome-olt/devices',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    Botble.showSuccess(response.message || '{{ trans("plugins/fiberhome-olt-manager::olt.add_success") }}');
                    $('#add-olt-modal').modal('hide');
                    $('#olt-table').DataTable().ajax.reload();
                    $('#add-olt-form')[0].reset();
                    $('#connection-status').hide();
                    $('#add-model').empty().append('<option value="">{{ trans("plugins/fiberhome-olt-manager::olt.select_model") }}</option>');
                } else {
                    Botble.showError(response.message || '{{ trans("plugins/fiberhome-olt-manager::olt.add_error") }}');
                }
            },
            error: function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessage = '';
                    
                    Object.keys(errors).forEach(function(key) {
                        errorMessage += errors[key][0] + '<br>';
                    });
                    
                    Botble.showError(errorMessage);
                } else {
                    Botble.showError(xhr.responseJSON?.message || '{{ trans("plugins/fiberhome-olt-manager::olt.add_error") }}');
                }
            },
            complete: function() {
                $submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> {{ trans("plugins/fiberhome-olt-manager::olt.add_olt") }}');
            }
        });
    });
    
    // Reset form when modal is closed
    $('#add-olt-modal').on('hidden.bs.modal', function() {
        $('#add-olt-form')[0].reset();
        $('#connection-status').hide();
        $('#submit-olt-btn').prop('disabled', false);
        $('#add-model').empty().append('<option value="">{{ trans("plugins/fiberhome-olt-manager::olt.select_model") }}</option>');
    });
});
</script>