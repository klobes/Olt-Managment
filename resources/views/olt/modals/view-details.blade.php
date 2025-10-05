<div class="modal fade" id="view-olt-details-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('plugins/fiberhome-olt-manager::olt.view_details') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><strong>{{ trans('plugins/fiberhome-olt-manager::olt.basic_info') }}</strong></h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>{{ trans('plugins/fiberhome-olt-manager::olt.name') }}:</strong></td>
                                <td id="detail-name"></td>
                            </tr>
                            <tr>
                                <td><strong>{{ trans('plugins/fiberhome-olt-manager::olt.ip_address') }}:</strong></td>
                                <td id="detail-ip"></td>
                            </tr>
                            <tr>
                                <td><strong>{{ trans('plugins/fiberhome-olt-manager::olt.model') }}:</strong></td>
                                <td id="detail-model"></td>
                            </tr>
                            <tr>
                                <td><strong>{{ trans('plugins/fiberhome-olt-manager::olt.firmware') }}:</strong></td>
                                <td id="detail-firmware"></td>
                            </tr>
                            <tr>
                                <td><strong>{{ trans('plugins/fiberhome-olt-manager::olt.uptime') }}:</strong></td>
                                <td id="detail-uptime"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6><strong>{{ trans('plugins/fiberhome-olt-manager::olt.performance_metrics') }}</strong></h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>{{ trans('plugins/fiberhome-olt-manager::olt.cpu_usage') }}:</strong></td>
                                <td id="detail-cpu"></td>
                            </tr>
                            <tr>
                                <td><strong>{{ trans('plugins/fiberhome-olt-manager::olt.memory_usage') }}:</strong></td>
                                <td id="detail-memory"></td>
                            </tr>
                            <tr>
                                <td><strong>{{ trans('plugins/fiberhome-olt-manager::olt.temperature') }}:</strong></td>
                                <td id="detail-temperature"></td>
                            </tr>
                            <tr>
                                <td><strong>{{ trans('plugins/fiberhome-olt-manager::olt.status') }}:</strong></td>
                                <td id="detail-status"></td>
                            </tr>
                            <tr>
                                <td><strong>{{ trans('plugins/fiberhome-olt-manager::olt.last_polled') }}:</strong></td>
                                <td id="detail-polled"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h6><strong>{{ trans('plugins/fiberhome-olt-manager::olt.port_statistics') }}</strong></h6>
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>{{ trans('plugins/fiberhome-olt-manager::olt.slot') }}</th>
                                    <th>{{ trans('plugins/fiberhome-olt-manager::olt.port') }}</th>
                                    <th>{{ trans('plugins/fiberhome-olt-manager::olt.status') }}</th>
                                    <th>{{ trans('plugins/fiberhome-olt-manager::olt.onu_count') }}</th>
                                    <th>{{ trans('plugins/fiberhome-olt-manager::olt.bandwidth_usage') }}</th>
                                </tr>
                            </thead>
                            <tbody id="port-statistics">
                                <!-- Port statistics will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    {{ trans('plugins/fiberhome-olt-manager::olt.close') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function viewOLTDetails(oltId) {
    $.ajax({
        url: '{{ url("admin/fiberhome/olt") }}/' + oltId,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const data = response.data;
                
                // Populate basic info
                $('#detail-name').text(data.name);
                $('#detail-ip').text(data.ip_address);
                $('#detail-model').text(data.model);
                $('#detail-firmware').text(data.firmware_version || 'N/A');
                $('#detail-uptime').text(data.uptime || 'N/A');
                
                // Populate performance metrics
                $('#detail-cpu').text(data.cpu_usage + '%');
                $('#detail-memory').text(data.memory_usage + '%');
                $('#detail-temperature').text(data.temperature + '°C');
                $('#detail-status').html(getStatusBadge(data.status));
                $('#detail-polled').text(data.last_polled ? new Date(data.last_polled).toLocaleString() : 'N/A');
                
                // Load port statistics
                loadPortStatistics(oltId);
                
                $('#view-olt-details-modal').modal('show');
            }
        },
        error: function() {
            Botble.showError('{{ trans("plugins/fiberhome-olt-manager::olt.load_error") }}');
        }
    });
}

function loadPortStatistics(oltId) {
    $.ajax({
        url: '{{ url("admin/fiberhome/olt") }}/' + oltId + '/ports',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const ports = response.data;
                let html = '';
                
                ports.forEach(function(port) {
                    html += '<tr>';
                    html += '<td>' + port.slot + '</td>';
                    html += '<td>' + port.port + '</td>';
                    html += '<td>' + getStatusBadge(port.status) + '</td>';
                    html += '<td>' + port.onu_count + '</td>';
                    html += '<td>' + (port.bandwidth_usage || '0') + '%</td>';
                    html += '</tr>';
                });
                
                $('#port-statistics').html(html);
            }
        }
    });
}

function getStatusBadge(status) {
    const statusClass = status === 'online' ? 'success' : 'danger';
    const statusText = status === 'online' ? '{{ trans("plugins/fiberhome-olt-manager::olt.online") }}' : '{{ trans("plugins/fiberhome-olt-manager::olt.offline") }}';
    
    return '<span class="badge badge-' + statusClass + '">' + statusText + '</span>';
}
</script>