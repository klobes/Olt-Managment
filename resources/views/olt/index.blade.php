@extends('core/base::layouts.master')

@section('content')
    <div class="max-width-1200">
        <div class="flexbox-annotated-section">
            <div class="flexbox-annotated-section-annotation">
                <div class="annotated-section-title pd-all-20">
                    <h4 class="title">{{ trans('plugins/fiberhome-olt-manager::olt.olt_management') }}</h4>
                </div>
            </div>
            
            <div class="flexbox-annotated-section-content">
                <div class="wrapper-content pd-all-20">
                    <!-- Add OLT Button -->
                    <div class="mb-3">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add-olt-modal">
                            <i class="fa fa-plus"></i> {{ trans('plugins/fiberhome-olt-manager::olt.add_olt') }}
                        </button>
                        <button type="button" class="btn btn-info" id="refresh-table">
                            <i class="fa fa-refresh"></i> {{ trans('plugins/fiberhome-olt-manager::olt.refresh') }}
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="olt-table">
                            <thead>
                                <tr>
                                    <th>{{ trans('plugins/fiberhome-olt-manager::olt.id') }}</th>
                                    <th>{{ trans('plugins/fiberhome-olt-manager::olt.name') }}</th>
                                    <th>{{ trans('plugins/fiberhome-olt-manager::olt.ip_address') }}</th>
                                    <th>{{ trans('plugins/fiberhome-olt-manager::olt.model') }}</th>
                                    <th>{{ trans('plugins/fiberhome-olt-manager::olt.vendor') }}</th>
                                    <th>{{ trans('plugins/fiberhome-olt-manager::olt.status') }}</th>
                                    <th>{{ trans('plugins/fiberhome-olt-manager::olt.onu_count') }}</th>
                                    <th>{{ trans('plugins/fiberhome-olt-manager::olt.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be populated by DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('plugins/fiberhome-olt-manager::olt.modals.add-olt')
    @include('plugins/fiberhome-olt-manager::olt.modals.edit-olt')
    @include('plugins/fiberhome-olt-manager::olt.modals.view-details')
@endsection

@push('footer')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#olt-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/api/fiberhome-olt/devices/datatable',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'ip_address', name: 'ip_address' },
                    { data: 'model', name: 'model' },
                    { data: 'vendor', name: 'vendor' },
                    { 
                        data: 'status', 
                        name: 'status',
                        render: function(data) {
                            var statusClass = {
                                'online': 'success',
                                'offline': 'danger',
                                'error': 'warning'
                            };
                            var className = statusClass[data] || 'secondary';
                            return '<span class="badge badge-' + className + '">' + data + '</span>';
                        }
                    },
                    { data: 'onu_count', name: 'onu_count', className: 'text-center' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                responsive: true,
                order: [[0, 'desc']],
                language: {
                    processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                }
            });
            
            // Refresh table button
            $('#refresh-table').on('click', function() {
                table.ajax.reload();
            });
            
            // Auto refresh every 30 seconds
            setInterval(function() {
                table.ajax.reload(null, false); // false = don't reset pagination
            }, 30000);
        });
    </script>
@endpush