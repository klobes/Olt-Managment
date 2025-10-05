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
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="olt-table">
                            <thead>
                                <tr>
                                    <th>{{ trans('plugins/fiberhome-olt-manager::olt.id') }}</th>
                                    <th>{{ trans('plugins/fiberhome-olt-manager::olt.name') }}</th>
                                    <th>{{ trans('plugins/fiberhome-olt-manager::olt.ip_address') }}</th>
                                    <th>{{ trans('plugins/fiberhome-olt-manager::olt.model') }}</th>
                                    <th>{{ trans('plugins/fiberhome-olt-manager::olt.status') }}</th>
                                    <th>{{ trans('plugins/fiberhome-olt-manager::olt.cpu_usage') }}</th>
                                    <th>{{ trans('plugins/fiberhome-olt-manager::olt.memory_usage') }}</th>
                                    <th>{{ trans('plugins/fiberhome-olt-manager::olt.temperature') }}</th>
                                    <th>{{ trans('plugins/fiberhome-olt-manager::olt.last_polled') }}</th>
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

    @include('plugins/fiberhome-olt-manager::olt.modals.view-details')
    @include('plugins/fiberhome-olt-manager::olt.modals.edit-olt')
    @include('plugins/fiberhome-olt-manager::olt.modals.add-olt')
@endsection

@push('footer')
    <script>
        $(document).ready(function() {
            $('#olt-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("fiberhome.olt.datatable") }}',
                    type: 'GET'
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'ip_address', name: 'ip_address' },
                    { data: 'model', name: 'model' },
                    { data: 'status', name: 'status' },
                    { data: 'cpu_usage', name: 'cpu_usage' },
                    { data: 'memory_usage', name: 'memory_usage' },
                    { data: 'temperature', name: 'temperature' },
                    { data: 'last_polled', name: 'last_polled' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                responsive: true,
                order: [[0, 'desc']]
            });
        });
    </script>
@endpush