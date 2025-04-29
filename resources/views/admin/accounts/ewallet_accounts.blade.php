<x-admin-layout :title="$pageTitle">
    @push('styles')
        <script src="{{ asset('public/assets/css/select2.min.css') }}"></script>
        <style>
            tr th {
                color: white !important
            }
        </style>
    @endpush
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                <div class="card-body">
                    <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <a href="javascript:void(0)" class="btn btn-primary">
                            <div data-i18n="Accounts">Accounts 1</div>
                        </a>
                        <a href="javascript:void(0)" class="btn btn-primary">
                            <div data-i18n="Accounts">Accounts 2</div>
                        </a>
                        <a href="{{ route('admin.deposit.accounts.index') }}" class="btn btn-primary">
                            <div data-i18n="Accounts Management">Accounts Management</div>
                        </a>
                        <a href="{{ route('admin.ewallet.accounts.details') }}"
                        class="btn btn-primary {{ request()->routeIs('admin.ewallet.accounts.details') ? 'active' : '' }}">
                         <div data-i18n="Category">Category</div>
                     </a>


                    </div>



                    <div class="table-responsive">
                        <div class="card-header">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#newModal">
                                Add User
                            </button>
                        </div>
                        <table class="categories-show-table table table-hover table-striped table-bordered settable table-sm">
                            <thead class="thead-dark bg-primary">
                                <tr>
                                    <th scope="col">@lang('ID')</th>
                                    <th scope="col">@lang('Category Name')</th>
                                    <th scope="col">@lang('Status')</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $key => $item)
                                    <tr>
                                        <td>{{ $item['id'] }}</td>
                                        <td>{{ $item['name'] ?? '' }}</td>
                                        <td>
                                            <label class="switch" style="pointer-events: none;">
                                                <input type="checkbox" class="switch-input {{ $item['status'] == 1 ? 'is-valid' : 'is-invalid' }}" {{ $item['status'] == 1 ? 'checked' : '' }}>
                                                <span class="switch-toggle-slider">
                                                    <span class="switch-on"></span>
                                                    <span class="switch-off"></span>
                                                </span>
                                                <span class="switch-label">
                                                    {{ $item['status'] == 1 ? 'Active' : 'Inactive' }}
                                                </span>
                                            </label>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="icon-base ti tabler-dots-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <form action="{{ route('admin.category.delete', $item['id']) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-icon edit_button"><i class="icon-base ti tabler-trash me-1"></i> Delete</button>
                                                    </form>
                                                    <button type="button" class="btn btn-sm btn-icon edit_button" data-bs-toggle="modal" data-bs-target="#editModal{{ $item['id'] }}">
                                                        <i class="icon-base ti tabler-user me-1"></i> Edit
                                                    </button><br>
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

    @foreach ($records as $item)
        @php
            $bankEwallets = is_array($item['bank_ewallets'] ?? null) ? $item['bank_ewallets'] : json_decode($item['bank_ewallets'], true);
            $bankEwallets = $bankEwallets ?: ['', '', ''];
        @endphp
        <div id="editModal{{ $item['id'] }}" class="modal modal-top fade" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary modal-colored-header">
                        <h5 class="modal-title" style="color: white" id="modalTopTitle">@lang('Edit Record')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.category.update', $item['id']) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="pr-3">Category Name</label>
                                <input type="text" class="form-control" name="name" value="{{ $item['name'] }}" required />
                            </div>
                            <div class="form-group mt-3">
                                <label class="pr-3">Status</label>
                                <select class="form-control" name="status" required>
                                    <option value="1" {{ $item['status'] == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $item['status'] == 0 ? 'selected' : '' }}>InActive</option>
                                </select>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">@lang('Update')</button>
                                <button type="button" class="btn btn-dark" data-bs-dismiss="modal" aria-label="Close">@lang('Close')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <div class="modal modal-top fade" id="newModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="color: #7367f0" id="modalTopTitle">@lang('Add New')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.category.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="pr-3">Category Name</label>
                            <input type="text" class="form-control" name="name" required />
                        </div>
                        <div class="form-group mt-3">
                            <label class="pr-3">Status</label>
                            <select class="form-control" name="status" required>
                                <option value="1">Active</option>
                                <option value="0">InActive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">@lang('Save')</button>
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal" aria-label="Close">@lang('Close')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('js')

        <script>
            $(document).ready(function() {
                $('#image').change(function() {
                    let reader = new FileReader();
                    reader.onload = (e) => {
                        $('#image_preview_container').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(this.files[0]);
                });

                $('select').select2({ selectOnClose: true });

                $('#adjustment').change(function() {
                    var selectedValue = $(this).val();
                    if (selectedValue == 1 || selectedValue == 2) {
                        $('#amount_type1').prop('checked', true);
                        $('#amount_type2').prop('checked', false);
                    } else if (selectedValue == 3) {
                        $('#amount_type2').prop('checked', true);
                        $('#amount_type1').prop('checked', false);
                    }
                });
            });
        </script>
    @endpush
</x-admin-layout>
