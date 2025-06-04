<x-admin-layout :title="$pageTitle">
    @push('styles')
    @endpush
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                <div class="card-body">
                    <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
                    {{-- @if (adminAccessRoute(config('role.partners.access.add'))) --}}
                    {{-- <a href="javascript:void(0)" class="btn btn-sm btn-primary mr-2" data-target="#newModal" data-toggle="modal">
                        <span><i class="fa fa-plus-circle"></i> @lang('Add New')</span>
                    </a> --}}
                    {{-- @endif --}}

                    <div class="table-responsive">
                        <table
                            class="categories-show-table table table-hover table-striped table-bordered settable table-sm">
                            <thead class="thead-dark bg-primary">
                                <tr>
                                    <th scope="col">{{ __('accounts.id') }}</th>
                                    <th scope="col">{{ __('accounts.partner') }}</th>
                                    <th scope="col">{{ __('accounts.group_name') }}</th>
                                    <th scope="col">{{ __('accounts.group_id') }}</th>
                                    <th scope="col">{{ __('accounts.status') }}</th>
                                    <th>{{ __('accounts.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $key => $item)
                                    <tr>
                                        <td>{{ $item['id'] }}</td>
                                        <td>{{ $partners[$item['api_id']] ?? '' }}</td>
                                        <td>{{ $item['group_name'] }}</td>
                                        <td>{{ $item['group_username'] }}</td>

                                        <td class="text-lg-center text-right">
                                            <form class="toggle-status-form d-inline" data-id="{{ $item->id }}"
                                                data-url="{{ route('admin.groups.toggleStatus', $item->id) }}">
                                                @csrf
                                                <button type="button"
                                                    class="btn btn-sm toggle-status-btn {{ $item->status ? 'btn-success' : 'btn-danger' }}">
                                                    <i class="fa fa-circle me-1"></i>
                                                    {{ $item->status ? __('Active') : __('Inactive') }}
                                                </button>
                                            </form>
                                        </td>

                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                    data-bs-toggle="dropdown">
                                                    <i class="icon-base ti tabler-dots-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    @if (adminAccessRoute(config('role.partners.access.delete')))
                                                        <form action="{{ route('admin.groups.delete', $item['id']) }}"
                                                            method="POST" class="delete-form"
                                                            data-id="{{ $item['id'] }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="btn btn-sm btn-icon edit_button">
                                                                <i class="icon-base ti tabler-trash me-1"></i>
                                                                {{ __('accounts.delete') }}
                                                            </button>
                                                        </form>
                                                    @endif
                                                    {{-- @if (adminAccessRoute(config('role.partners.access.view'))) --}}


                                                    {{-- @endif --}}
                                                    @if (adminAccessRoute(config('role.partners.access.edit')))
                                                        <button type="button" class="btn btn-sm btn-icon edit_button"
                                                            data-id="{{ $item['id'] }}"
                                                            data-api_id="{{ $item['api_id'] }}"
                                                            data-group_name="{{ $item['group_name'] }}"
                                                            data-group_username="{{ $item['group_username'] }}"
                                                            data-status="{{ $item['status'] }}"
                                                            data-route="{{ route('admin.groups.update', $item['id']) }}"
                                                            data-bs-toggle="modal" data-bs-target="#editModal">
                                                            <i class="icon-base ti tabler-user me-1"></i>
                                                            {{ __('accounts.edit') }}
                                                        </button>
                                                    @endif

                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%">
                                            <p class="text-dark">@lang('No Data Found')</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        {{ $records->appends($_GET)->links('partials.pagination') }}
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- Single Edit Modal -->
    <div id="editModal" class="modal modal-top fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header modal-colored-header bg-primary">
                    <h5 class="modal-title">{{ __('accounts.edit_record') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="id" id="edit_id">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{ __('accounts.select_partner') }}</label>
                                    <select class="form-control" name="api_id" id="edit_api_id" required>
                                        <option value="">{{ __('accounts.select_partner') }}</option>
                                        @foreach ($partners as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>{{ __('accounts.group_name') }}</label>
                                    <input type="text" class="form-control" name="group_name" id="edit_group_name"
                                        required />
                                </div>

                                <div class="form-group">
                                    <label>{{ __('accounts.group_id') }}</label>
                                    <input type="text" class="form-control" name="group_username"
                                        id="edit_group_username" required />
                                </div>

                                <div class="form-group">
                                    <label>{{ __('accounts.status') }}</label>
                                    <select class="form-control" name="status" id="edit_status" required>
                                        <option value="1">{{ __('accounts.active') }}</option>
                                        <option value="0">{{ __('accounts.inactive') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">{{ __('accounts.update') }}</button>
                        <button type="button" class="btn btn-dark"
                            data-bs-dismiss="modal">{{ __('accounts.close') }}</button>
                    </div>
                </form>

            </div>
        </div>
    </div>


    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const deleteForms = document.querySelectorAll('.delete-form');
                deleteForms.forEach(form => {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault(); // stop form

                        const itemId = form.getAttribute('data-id');

                        Swal.fire({
                            title: 'Are you sure?',
                            text: `This will permanently delete item ID: ${itemId}`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Yes, delete it!',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit(); // proceed to submit
                            }
                        });
                    });
                });
            });

            document.addEventListener('DOMContentLoaded', function() {
                const editButtons = document.querySelectorAll('.edit_button');

                editButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const api_id = this.getAttribute('data-api_id');
                        const group_name = this.getAttribute('data-group_name');
                        const group_username = this.getAttribute('data-group_username');
                        const status = this.getAttribute('data-status');
                        const route = this.getAttribute('data-route');

                        // Set the form action
                        document.getElementById('editForm').setAttribute('action', route);

                        // Fill form values
                        document.getElementById('edit_api_id').value = api_id;
                        document.getElementById('edit_group_name').value = group_name;
                        document.getElementById('edit_group_username').value = group_username;
                        document.getElementById('edit_status').value = status;
                    });
                });
            });

            document.addEventListener('DOMContentLoaded', function() {
                const forms = document.querySelectorAll('.toggle-status-form');

                forms.forEach(form => {
                    const button = form.querySelector('.toggle-status-btn');
                    button.addEventListener('click', function() {
                        const url = form.getAttribute('data-url');
                        const token = form.querySelector('input[name="_token"]').value;

                        fetch(url, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': token,
                                    'Accept': 'application/json',
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    location.reload(); // Refresh page
                                } else {
                                    alert('Failed to update status.');
                                }
                            })
                            .catch(() => alert('Something went wrong.'));
                    });
                });
            });
        </script>
    @endpush
</x-admin-layout>
