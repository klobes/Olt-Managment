<div class="btn-group btn-group-sm" role="group">
    <button type="button" class="btn btn-info view-olt" data-id="{{ $device->id }}" title="View Details">
        <i class="fa fa-eye"></i>
    </button>
    <button type="button" class="btn btn-warning edit-olt" data-id="{{ $device->id }}" title="Edit">
        <i class="fa fa-edit"></i>
    </button>
    <button type="button" class="btn btn-success sync-olt" data-id="{{ $device->id }}" title="Sync Data">
        <i class="fa fa-refresh"></i>
    </button>
    <button type="button" class="btn btn-primary test-connection" data-id="{{ $device->id }}" title="Test Connection">
        <i class="fa fa-plug"></i>
    </button>
    <button type="button" class="btn btn-danger delete-olt" data-id="{{ $device->id }}" title="Delete">
        <i class="fa fa-trash"></i>
    </button>
</div>