<x-admin-layout :title="$pageTitle">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
        <style>
            /* Fix for Select2 inside Bootstrap modal */
            .select2-container {
                z-index: 99999 !important;
            }
        </style>
    @endpush
    @php
        $currentRoute = Route::currentRouteName();
    @endphp

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <div class="row justify-content-between">
            <div class="col-md-12">
                <div class="row ">
                    <div class="col-md-5 gap-6 d-flex justify-content-between">
                        @if (adminAccessRoute(config('role.account_management.access.view')))
                            <div>
                                <button
                                    class="btn {{ $currentRoute == 'admin.ewallet.accounts.details' ? 'btn-primary' : '' }}">
                                    <a href="{{ route('admin.ewallet.accounts.details') }}" class="menu-link">
                                        <div data-i18n="Accounts List">{{ __('accounts.accounts_list') }}</div>
                                    </a>
                                </button>
                            </div>
                        @endif
                        @if (adminAccessRoute(config('role.account_management.access.add')))
                            <div>
                                <button
                                    class="btn {{ $currentRoute == 'admin.account_management.add_account' ? 'btn-primary' : '' }}">
                                    <a href="{{ route('admin.account_management.add_account') }}" class="menu-link">
                                        <div data-i18n="Add Accounts">{{ __('accounts.add_account') }}</div>
                                    </a>
                                </button>
                            </div>
                        @endif
                        @if (adminAccessRoute(config('role.e_wallet_accounts.access.edit')))
                            <div>
                                <button
                                    class="btn {{ $currentRoute == 'admin.account_management.on_off_account' ? 'btn-primary' : '' }}">
                                    <a href="{{ route('admin.account_management.on_off_account') }}" class="menu-link">
                                        <div data-i18n="Add Accounts">{{ __('accounts.on_off_account') }}</div>
                                    </a>
                                </button>
                            </div>
                        @endif
                        @if (adminAccessRoute(config('role.account_group.access.view')))
                            <div>
                                <button
                                    class="btn {{ $currentRoute == 'admin.account_management.account_group' ? 'btn-primary' : '' }}">
                                    <a href="{{ route('admin.account_management.account_group') }}" class="menu-link">
                                        <div data-i18n="Account Group">{{ __('accounts.account_group') }}</div>
                                    </a>
                                </button>
                            </div>
                        @endif
                        @if (adminAccessRoute(config('role.gateways.access.view')))
                            <div>
                                <button
                                    class="btn {{ $currentRoute == 'admin.account_management.gateway' ? 'btn-primary' : '' }}">
                                    <a href="{{ route('admin.account_management.gateway') }}" class="menu-link">
                                        <div data-i18n="Gateway">{{ __('accounts.gateway') }}</div>
                                    </a>
                                </button>
                            </div>
                        @endif
                        @if (adminAccessRoute(config('role.categories.access.view')))
                            <div>
                                <button
                                    class="btn {{ $currentRoute == 'admin.account_management.add_category' ? 'btn-primary' : '' }}">
                                    <a href="{{ route('admin.account_management.add_category') }}" class="menu-link">
                                        <div data-i18n="Add Category">{{ __('accounts.categories') }}</div>
                                    </a>
                                </button>
                            </div>
                        @endif
                        @if(adminAccessRoute(config('role.account_management.access.view')))
                        <div>
                            <button
                                class="btn {{ $currentRoute == 'admin.ewallet.accounts.available' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.ewallet.accounts.available') }}" class="menu-link">
                                    <div data-i18n="Accounts List">Available Accounts</div>
                                </a>
                            </button>
                        </div>
                        @endif


                    </div>
                </div>

            </div>
        </div>
    </div>


    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">

            <div class="">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
                    @if (adminAccessRoute(config('role.account_group.access.add')))
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#groupModal" id="newCategoryButton">
                            {{ __('accounts.add_account_group') }}
                        </button>
                    @endif
                </div>


                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">{{ __('accounts.group_name') }}</th>
                                <th scope="col">{{ __('accounts.accounts') }}</th>
                                <th scope="col">{{ __('accounts.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groups as $group)
                                <tr>
                                    <td>{{ $group->name }}</td>
                                    <td>
                                        @if ($group->accounts->isNotEmpty())
                                            @foreach ($group->accounts as $account)
                                                <span class="badge bg-primary me-1">{{ $account->account_no }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">{{ __('accounts.no_accounts') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if (adminAccessRoute(config('role.account_group.access.edit')))
                                            <button class="btn btn-sm btn-warning editGroupBtn"
                                                data-id="{{ $group->id }}" data-name="{{ $group->name }}"
                                                data-accounts="{{ $group->accounts->pluck('id')->implode(',') }}">
                                                {{ __('accounts.edit') }}

                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>



                </div>
            </div>


        </div>
    </div>


    <!-- Group Modal -->
    <div class="modal fade" id="groupModal" tabindex="-1" aria-labelledby="groupModalLabel" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="groupModalLabel">{{ __('accounts.add_group') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="groupForm" action="{{ route('admin.accounts.addpairs') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="groupName" class="form-label">{{ __('accounts.group_name') }}</label>
                            <input type="text" name="group_name" class="form-control" id="groupName"
                                placeholder="{{ __('accounts.enter_group_name') }}">
                        </div>

                        <div class="mb-3">
                            <label for="paris" class="form-label">{{ __('accounts.select_pairs') }}</label>
                            <select id="paris" name="pairs[]" class="form-select select2" z-index="99999" multiple>
                                @foreach ($records as $accounts)
                                    <option value="{{ $accounts->id }}"> {{ $accounts->account_no }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ __('accounts.close') }}</button>
                            <button type="submit" class="btn btn-primary"
                                id="submitGroupBtn">{{ __('accounts.save_group') }}</button>
                        </div>
                    </form>

                </div>


            </div>
        </div>
    </div>

    <!-- Edit Group Modal -->
    <div class="modal fade" id="editGroupModal" tabindex="-1" aria-labelledby="editGroupModalLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editGroupForm" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="editGroupId">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editGroupModalLabel">{{ __('accounts.edit_group') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editGroupName" class="form-label">{{ __('accounts.group_name') }}</label>
                            <input type="text" name="edit_group_name" class="form-control" id="editGroupName"
                                placeholder="{{ __('accounts.enter_group_name') }}">
                            <small class="text-danger error-text group_name_error"></small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('accounts.select_pairs') }}</label>
                            <select name="edit_pairs[]" id="editGroupAccounts" class="form-select select2" multiple>
                                @foreach ($records as $accounts)
                                    <option value="{{ $accounts->id }}">{{ $accounts->account_no }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger error-text pairs_error"></small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('accounts.close') }}</button>
                        <button type="submit" class="btn btn-primary"
                            id="updateGroupBtn">{{ __('accounts.update_group') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="{{ asset('assets/DataTables/datatables.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('groupForm');
                const submitBtn = document.getElementById('submitGroupBtn');
                const modal = new bootstrap.Modal(document.getElementById('groupModal'));

                form.addEventListener('submit', function(e) {
                    // Disable the button to prevent multiple clicks
                    submitBtn.disabled = true;
                    submitBtn.innerText = 'Saving...';

                    // Optionally hide the modal immediately
                    modal.hide();
                });
            });

            $(document).ready(function() {
                // Initialize Add Group Modal select2
                $('#groupModal').on('shown.bs.modal', function() {
                    let $select = $(this).find('.select2');

                    // Prevent re-initialization
                    if (!$select.hasClass('select2-hidden-accessible')) {
                        $select.select2({
                            dropdownParent: $('#groupModal'),
                            allowClear: true
                        });

                        $select.on('select2:unselecting', function(e) {
                            $(this).data('unselecting', true);
                        });

                        $select.on('select2:opening', function(e) {
                            if ($(this).data('unselecting')) {
                                $(this).removeData('unselecting');
                                e.preventDefault();
                            }
                        });
                    }
                });

                // Initialize Edit Group Modal select2
                $('#editGroupModal').on('shown.bs.modal', function() {
                    let $editSelect = $(this).find('.select2');
                    if (!$editSelect.hasClass('select2-hidden-accessible')) {
                        $editSelect.select2({
                            dropdownParent: $('#editGroupModal'),
                            allowClear: true
                        });

                        $editSelect.on('select2:unselecting', function(e) {
                            $(this).data('unselecting', true);
                        });

                        $editSelect.on('select2:opening', function(e) {
                            if ($(this).data('unselecting')) {
                                $(this).removeData('unselecting');
                                e.preventDefault();
                            }
                        });
                    }
                });

                // Open edit modal
                $(document).on('click', '.editGroupBtn', function() {
                    const groupId = $(this).data('id');
                    const groupName = $(this).data('name');
                    const accounts = $(this).data('accounts')?.toString().split(',') || [];

                    // Set form values
                    $('#editGroupId').val(groupId);
                    $('#editGroupName').val(groupName);

                    const $select = $('#editGroupAccounts');

                    // Reset and set selected options
                    $select.val(null).trigger('change'); // Clear previous selections
                    $select.val(accounts).trigger('change');

                    // Set form action dynamically
                    $('#editGroupForm').attr('action', '/admin/accounts/update-group/' + groupId);

                    // Show modal
                    const editModal = new bootstrap.Modal(document.getElementById('editGroupModal'));
                    editModal.show();
                });

                // Submit form via AJAX
                $('#editGroupForm').on('submit', function(e) {
                    e.preventDefault();

                    let formData = new FormData(this);
                    clearErrors();

                    $.ajax({
                        type: "POST",
                        url: "{{ route('admin.accounts.updateGroup') }}",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function() {
                            $('#editGroupModal').modal('hide');
                            location.reload();
                        },
                        error: function(xhr) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('.' + key + '_error').text(value[0]);
                            });
                        }
                    });
                });

                function clearErrors() {
                    $('.error-text').text('');
                }
            });
        </script>
    @endpush
</x-admin-layout>
