<x-admin-layout :title="$pageTitle">
    <style>
        .fa-ellipsis-v:before {
            content: "\f142";
        }
    </style>
    @php
        $currentRoute = Route::currentRouteName();
    @endphp
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <div class="row justify-content-between">
            <div class="col-md-12">
                <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
                <div class="row ">
                    <div class="col-md-5 gap-6 d-flex justify-content-between">
                        <div>
                            <button class="btn {{ $currentRoute == 'admin.users' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.users') }}" class="menu-link">
                                    <div data-i18n="Manual Gateway">Manual Gateway</div>
                                </a>
                            </button>
                        </div>
                        <div>
                            <button class="btn {{ $currentRoute == 'admin.location' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.location') }}" class="menu-link">
                                    <div data-i18n="Location">Location</div>
                                </a>
                            </button>
                        </div>
                        <div>
                            <button
                                class="btn {{ $currentRoute == 'admin.roles_and_permission' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.roles_and_permission') }}" class="menu-link">
                                    <div data-i18n="Roles and Permission">Roles and Permission</div>
                                </a>
                            </button>
                        </div>
                        <div>
                            <button class="btn {{ $currentRoute == 'admin.rolescategory' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.rolescategory') }}" class="menu-link">
                                    <div data-i18n="Roles Category">Roles Category</div>
                                </a>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                    <div class="card-body">
                        <div class="card-header">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newModal">
                                Add New Location
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="categories-show-table table table-hover table-striped table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">@lang('No.')</th>
                                        <th scope="col">@lang('Location')</th>
                                        <th scope="col">@lang('Status')</th>
                                        <th scope="col">@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($userLocations as $location)
                                        <tr>
                                            <td data-label="@lang('No.')">{{ loopIndex($userLocations) + $loop->index }}</td>
                                            <td data-label="@lang('Location')">{{ $location->location }}</td>
                                            <td data-label="@lang('Status')">
                                                <label class="switch" style="pointer-events: none;">
                                                    <input type="checkbox" class="switch-input {{ $location->status ? 'is-valid' : 'is-invalid' }}"
                                                        {{ $location->status ? 'checked' : '' }}>
                                                    <span class="switch-toggle-slider">
                                                        <span class="switch-on"></span>
                                                        <span class="switch-off"></span>
                                                    </span>
                                                    <span class="switch-label">
                                                        {{ $location->status ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </label>
                                            </td>
                                            <td data-label="@lang('Action')">
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="icon-base ti tabler-dots-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                        <a class="dropdown-item edit-location" href="#" data-bs-toggle="modal"
                                                           data-bs-target="#editModal"
                                                           data-id="{{ $location->id }}"
                                                           data-location="{{ $location->location }}"
                                                           data-status="{{ $location->status }}">
                                                            <i class="fa fa-edit text-warning pr-2" aria-hidden="true"></i>
                                                            @lang('Edit')
                                                        </a>
                                                        <form action="{{ route('admin.users.location.delete', $location->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item" onclick="return confirm('Are you sure you want to delete this location?')">
                                                                <i class="fa fa-trash text-danger pr-2" aria-hidden="true"></i>
                                                                @lang('Delete')
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center text-danger" colspan="4">@lang('No Location Data')</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $userLocations->appends(@$search)->links('partials.pagination') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Location Modal -->
    <div class="modal fade" id="newModal" tabindex="-1" aria-labelledby="newModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newModalLabel">Add Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addLocationForm" action="{{ route('admin.users.location.add') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="location" class="form-label">Location:</label>
                            <input type="text" class="form-control" id="location" name="location" required placeholder="Enter location">
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status:</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addLocationForm" class="btn btn-primary">Save Location</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Location Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editLocationForm" action="{{ route('admin.location.update', '') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-3">
                            <label for="edit_location" class="form-label">Location:</label>
                            <input type="text" class="form-control" id="edit_location" name="location" required placeholder="Enter location">
                        </div>

                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status:</label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="editLocationForm" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            jQuery(document).ready(function() {
    jQuery(document).on('click', '.edit-location', function(e) {
        e.preventDefault();
        try {
            var id = jQuery(this).data('id');
            var location = jQuery(this).data('location');
            var status = jQuery(this).data('status');

            // Validate data
            if (!id || !location) {
                console.error('Missing required data');
                return;
            }

            // Populate form fields
            jQuery('#edit_id').val(id);
            jQuery('#edit_location').val(location).trigger('change');
            jQuery('#edit_status').val(status).trigger('change');

            // Update form action URL
            var updateUrl = "{{ route('admin.location.update', '') }}/" + id;
            jQuery('#editLocationForm').attr('action', updateUrl);

            // Debugging logs
            console.log('Edit form populated:', {
                id: id,
                location: location,
                status: status
            });
        } catch (error) {
            console.error('Error populating edit form:', error);
        }
    });
});


            // $(document).ready(function() {
                // Initialize Select2

                // Handle edit button click


                // Reset form when modal is closed
                $('#editModal').on('hidden.bs.modal', function() {
                    $('#editLocationForm')[0].reset();
                    $('select').trigger('change');
                });
            // });
        </script>
    @endpush
</x-admin-layout>
