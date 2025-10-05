<div class="modal fade" id="add-olt-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="add-olt-form">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ trans('plugins/fiberhome-olt-manager::olt.add_olt') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="add-name">{{ trans('plugins/fiberhome-olt-manager::olt.name') }}</label>
                        <input type="text" class="form-control" id="add-name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="add-ip">{{ trans('plugins/fiberhome-olt-manager::olt.ip_address') }}</label>
                        <input type="text" class="form-control" id="add-ip" name="ip_address" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="add-model">{{ trans('plugins/fiberhome-olt-manager::olt.model') }}</label>
                        <select class="form-control" id="add-model" name="model" required>
                            <option value="">{{ trans('plugins/fiberhome-olt-manager::olt.select_model') }}</option>
                            <option value="AN5516-01">AN5516-01</option>
                            <option value="AN5516-02">AN5516-02</option>
                            <option value="AN5516-04">AN5516-04</option>
                            <option value="AN5516-06">AN5516-06</option>
                            <option value="AN5516-10">AN5516-10</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="add-snmp-community">{{ trans('plugins/fiberhome-olt-manager::olt.snmp_community') }}</label>
                        <input type="text" class="form-control" id="add-snmp-community" name="snmp_community" value="public">
                    </div>
                    
                    <div class="form-group">
                        <label for="add-snmp-version">{{ trans('plugins/fiberhome-olt-manager::olt.snmp_version') }}</label>
                        <select class="form-control" id="add-snmp-version" name="snmp_version">
                            <option value="2c">SNMPv2c</option>
                            <option value="3">SNMPv3</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="add-description">{{ trans('plugins/fiberhome-olt-manager::olt.description') }}</label>
                        <textarea class="form-control" id="add-description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        {{ trans('plugins/fiberhome-olt-manager::olt.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        {{ trans('plugins/fiberhome-olt-manager::olt.add_olt') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$('#add-olt-form').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: '{{ route("fiberhome.olt.store") }}',
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            if (response.success) {
                Botble.showSuccess(response.message);
                $('#add-olt-modal').modal('hide');
                $('#olt-table').DataTable().ajax.reload();
                $('#add-olt-form')[0].reset();
            } else {
                Botble.showError(response.message);
            }
        },
        error: function(xhr) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                let errors = xhr.responseJSON.errors;
                let errorMessage = '';
                
                Object.keys(errors).forEach(function(key) {
                    errorMessage += errors[key][0] + '<br>';
                });
                
                Botble.showError(errorMessage);
            } else {
                Botble.showError('{{ trans("plugins/fiberhome-olt-manager::olt.add_error") }}');
            }
        }
    });
});
</script>