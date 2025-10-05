@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ trans('plugins/fiberhome-olt-manager::fiberhome-olt.topology.title') }}</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5>{{ trans('plugins/fiberhome-olt-manager::fiberhome-olt.topology.olt_devices') }}</h5>
                                    <h2>{{ $oltDevices->count() }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5>{{ trans('plugins/fiberhome-olt-manager::fiberhome-olt.topology.fiber_cables') }}</h5>
                                    <h2>{{ $fiberCables->count() }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5>{{ trans('plugins/fiberhome-olt-manager::fiberhome-olt.topology.junction_boxes') }}</h5>
                                    <h2>{{ $junctionBoxes->count() }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5>{{ trans('plugins/fiberhome-olt-manager::fiberhome-olt.topology.splitters') }}</h5>
                                    <h2>{{ $splitters->count() }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div id="topology-map" style="height: 500px; border: 1px solid #ddd; border-radius: 5px;">
                                <p class="text-center p-5">
                                    <i class="fas fa-map-marked-alt fa-3x text-muted"></i><br>
                                    <strong>Interactive Topology Map</strong><br>
                                    <small>Topology visualization will be implemented in v2.0.0</small>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5>{{ trans('plugins/fiberhome-olt-manager::fiberhome-olt.topology.equipment_list') }}</h5>
                            
                            <div class="accordion" id="topologyAccordion">
                                <div class="card">
                                    <div class="card-header" id="oltHeading">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#oltCollapse">
                                                <i class="fas fa-server"></i> OLT Devices ({{ $oltDevices->count() }})
                                            </button>
                                        </h2>
                                    </div>
                                    <div id="oltCollapse" class="collapse show" data-parent="#topologyAccordion">
                                        <div class="card-body">
                                            @if($oltDevices->isEmpty())
                                                <p class="text-muted">No OLT devices found</p>
                                            @else
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>IP Address</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($oltDevices as $olt)
                                                        <tr>
                                                            <td>{{ $olt->name }}</td>
                                                            <td>{{ $olt->ip_address }}</td>
                                                            <td>
                                                                <span class="badge badge-{{ $olt->status === 'online' ? 'success' : 'secondary' }}">
                                                                    {{ $olt->status }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header" id="cablesHeading">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#cablesCollapse">
                                                <i class="fas fa-network-wired"></i> Fiber Cables ({{ $fiberCables->count() }})
                                            </button>
                                        </h2>
                                    </div>
                                    <div id="cablesCollapse" class="collapse" data-parent="#topologyAccordion">
                                        <div class="card-body">
                                            @if($fiberCables->isEmpty())
                                                <p class="text-muted">No fiber cables found</p>
                                            @else
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Code</th>
                                                            <th>Type</th>
                                                            <th>Fibers</th>
                                                            <th>Length</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($fiberCables as $cable)
                                                        <tr>
                                                            <td>{{ $cable->cable_code }}</td>
                                                            <td>{{ $cable->cable_type }}</td>
                                                            <td>{{ $cable->fiber_count }}</td>
                                                            <td>{{ $cable->length }}m</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header" id="boxesHeading">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#boxesCollapse">
                                                <i class="fas fa-box"></i> Junction Boxes ({{ $junctionBoxes->count() }})
                                            </button>
                                        </h2>
                                    </div>
                                    <div id="boxesCollapse" class="collapse" data-parent="#topologyAccordion">
                                        <div class="card-body">
                                            @if($junctionBoxes->isEmpty())
                                                <p class="text-muted">No junction boxes found</p>
                                            @else
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Code</th>
                                                            <th>Type</th>
                                                            <th>Capacity</th>
                                                            <th>Location</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($junctionBoxes as $box)
                                                        <tr>
                                                            <td>{{ $box->box_code }}</td>
                                                            <td>{{ $box->box_type }}</td>
                                                            <td>{{ $box->capacity }}</td>
                                                            <td>
                                                                @if($box->latitude && $box->longitude)
                                                                    <small>{{ $box->latitude }}, {{ $box->longitude }}</small>
                                                                @else
                                                                    <span class="text-muted">No GPS</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header" id="splittersHeading">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#splittersCollapse">
                                                <i class="fas fa-project-diagram"></i> Splitters ({{ $splitters->count() }})
                                            </button>
                                        </h2>
                                    </div>
                                    <div id="splittersCollapse" class="collapse" data-parent="#topologyAccordion">
                                        <div class="card-body">
                                            @if($splitters->isEmpty())
                                                <p class="text-muted">No splitters found</p>
                                            @else
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Code</th>
                                                            <th>Type</th>
                                                            <th>Ports</th>
                                                            <th>Location</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($splitters as $splitter)
                                                        <tr>
                                                            <td>{{ $splitter->splitter_code }}</td>
                                                            <td>{{ $splitter->splitter_type }}</td>
                                                            <td>{{ $splitter->output_ports }}</td>
                                                            <td>
                                                                @if($splitter->latitude && $splitter->longitude)
                                                                    <small>{{ $splitter->latitude }}, {{ $splitter->longitude }}</small>
                                                                @else
                                                                    <span class="text-muted">No GPS</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5>{{ trans('plugins/fiberhome-olt-manager::fiberhome-olt.topology.quick_actions') }}</h5>
                            
                            <div class="list-group">
                                <a href="#" class="list-group-item list-group-item-action" onclick="tracePath()">
                                    <i class="fas fa-route"></i> Trace Fiber Path
                                </a>
                                <a href="#" class="list-group-item list-group-item-action" onclick="findAvailableEquipment()">
                                    <i class="fas fa-search"></i> Find Available Equipment
                                </a>
                                <a href="#" class="list-group-item list-group-item-action" onclick="calculateOpticalBudget()">
                                    <i class="fas fa-calculator"></i> Calculate Optical Budget
                                </a>
                                <a href="#" class="list-group-item list-group-item-action" onclick="validateTopology()">
                                    <i class="fas fa-check-circle"></i> Validate Topology
                                </a>
                            </div>

                            <div class="mt-3">
                                <h6>{{ trans('plugins/fiberhome-olt-manager::fiberhome-olt.topology.recommendations') }}</h6>
                                <div class="alert alert-info">
                                    <i class="fas fa-lightbulb"></i>
                                    <strong>Recommendation:</strong> Consider implementing topology visualization in v2.0.0
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function tracePath() {
            // Implementation for fiber path tracing
            alert('Fiber path tracing will be implemented in v2.0.0');
        }

        function findAvailableEquipment() {
            // Implementation for finding available equipment
            alert('Equipment finder will be implemented in v2.0.0');
        }

        function calculateOpticalBudget() {
            // Implementation for optical budget calculation
            alert('Optical budget calculator will be implemented in v2.0.0');
        }

        function validateTopology() {
            // Implementation for topology validation
            alert('Topology validation will be implemented in v2.0.0');
        }
    </script>
@endsection