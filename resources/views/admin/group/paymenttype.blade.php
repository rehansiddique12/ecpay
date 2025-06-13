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
                    <div class="table-responsive">
                        <div class="card-header">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#newModal">
                                {{ __('reports.add_user') }}
                            </button>
                        </div>
                        <table
                            class="categories-show-table table table-hover table-striped table-bordered settable table-sm">
                            <thead class="thead-dark bg-primary">
                                <tr>
                                    <th scope="col">{{ __('reports.id') }}</th>
                                    <th scope="col">{{ __('reports.category_name') }}</th>
                                    <th scope="col">{{ __('reports.status') }}</th>
                                    <th>{{ __('reports.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $key => $item)
                                    <tr>
                                        <td>{{ $item['id'] }}</td>
                                        <td>{{ $item['name'] ?? '' }}</td>
                                        <td>
                                            <label class="switch" style="pointer-events: none;">
                                                <input type="checkbox"
                                                    class="switch-input {{ $item['status'] == 1 ? 'is-valid' : 'is-invalid' }}"
                                                    {{ $item['status'] == 1 ? 'checked' : '' }}>

                                                <span class="switch-toggle-slider">
                                                    <span class="switch-on"></span>
                                                    <span class="switch-off"></span>
                                                </span>

                                                <span class="switch-label">
                                                    {{ $item['status'] == 1 ? __('reports.active') : __('reports.inactive') }}
                                                </span>
                                            </label>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                    data-bs-toggle="dropdown">
                                                    <i class="icon-base ti tabler-dots-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    {{-- @if (adminAccessRoute(config('role.partners.access.delete'))) --}}
                                                    <form action="{{ route('admin.groups.delete', $item['id']) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-sm btn-icon edit_button"><i
                                                                class="icon-base ti tabler-trash me-1"></i>
                                                            {{ __('reports.delete') }}</button>
                                                    </form>
                                                    {{-- @endif --}}
                                                    {{-- @if (adminAccessRoute(config('role.partners.access.edit'))) --}}
                                                    <button type="button" class="btn btn-sm btn-icon edit_button"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editModal{{ $item['id'] }}">
                                                        <i class="icon-base ti tabler-user me-1"></i>
                                                        {{ __('reports.edit_record') }}
                                                    </button><br>
                                                    {{-- @endif --}}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%">
                                            <p class="text-dark">{{ __('reports.no_data_found') }}</p>
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
        <!-- Edit Modal -->
        <div id="editModal{{ $item['id'] }}" class="modal modal-top fade" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary modal-colored-header">
                        <h5 class="modal-title" style="color: white" id="modalTopTitle">{{ __('reports.edit_record') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.type.update', $item['id']) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row justify-content-between align-items-center">
                                <!-- Input fields for editing the record -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="pr-3">{{ __('reports.category_name') }}</label>
                                        <input type="text" class="form-control" name="name"
                                            value="{{ $item['name'] }}" required />
                                    </div>
                                    <div class="row mt-3 justify-content-between">
                                        <div class="col-lg-12 col-md-12">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="pr-3">{{ __('reports.status') }}</label>
                                                    <select class="form-control" name="status" required>
                                                        <option value="1">{{ __('reports.active') }}</option>
                                                        <option value="0">{{ __('reports.inactive') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">{{ __('reports.update') }}</button>
                                <button type="button" class="btn btn-dark" data-bs-dismiss="modal"
                                    aria-label="Close">{{ __('reports.close') }}</button>
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
                    <h5 class="modal-title" style="color: #7367f0" id="modalTopTitle">{{ __('reports.add_new') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.type.add') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row justify-content-between align-items-center">



                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('reports.category_name') }}</label>
                                    <input type="text" class="form-control" name="name" required />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('reports.status') }}</label>
                                    <select class="form-control" name="status" required>
                                        <option value="1">{{ __('reports.active') }}</option>
                                        <option value="0">{{ __('reports.inactive') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">{{ __('reports.save') }}</button>
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal"
                            aria-label="Close">{{ __('reports.close') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('js')
        <script src="{{ asset('public/assets/js/select2.min.js') }}"></script>
        <script>
            "use strict";
            $(document).ready(function(e) {


                $('#image').change(function() {
                    let reader = new FileReader();
                    reader.onload = (e) => {
                        $('#image_preview_container').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(this.files[0]);
                });


            });

            $(document).ready(function() {
                $('select').select2({
                    selectOnClose: true
                });
            });
        </script>
        <script>
            function setBalanceItem(itemId) {
                // Find the input field in the modal
                var balanceInput = document.getElementById("balanceInput");

                // Set the value of the input field to the item id
                balanceInput.value = itemId;
            }

            function setParentID(parentidd, acc_idd) {
                // Find the input field in the modal
                var parentidInput = document.getElementById("parentid");
                var acc_idInput = document.getElementById("acc_id");

                // Set the value of the input field to the item id
                parentidInput.value = parentidd;
                acc_idInput.value = acc_idd;
            }
        </script>

        <script>
            $(document).ready(function() {
                // Attach change event listener to the select element
                $('#adjustment').change(function() {
                    // Get the selected value
                    var selectedValue = $(this).val();

                    // Check if selected value is 1 or 2
                    if (selectedValue == 1 || selectedValue == 2) {
                        // If selected value is 1 or 2, check amount_type1 and uncheck amount_type2
                        $('#amount_type1').prop('checked', true);
                        $('#amount_type2').prop('checked', false);
                    } else if (selectedValue == 3) {
                        // If selected value is 3, check amount_type2 and uncheck amount_type1
                        $('#amount_type2').prop('checked', true);
                        $('#amount_type1').prop('checked', false);
                    }
                });
            });
        </script>
    @endpush
</x-admin-layout>
