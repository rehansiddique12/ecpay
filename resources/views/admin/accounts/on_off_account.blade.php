<x-admin-layout :title="$pageTitle">
    @push('styles')
    @endpush
    @php
    $currentRoute = Route::currentRouteName();
    @endphp

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <div class="row justify-content-between">
            <div class="col-md-12">
                <div class="row ">
                    <div class="col-md-5 gap-6 d-flex justify-content-between">
                        @if(adminAccessRoute(config('role.account_management.access.view')))
                        <div>
                            <button
                                class="btn {{ $currentRoute == 'admin.ewallet.accounts.details' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.ewallet.accounts.details') }}" class="menu-link">
                                    <div data-i18n="Accounts List">Accounts List</div>
                                </a>
                            </button>
                        </div>
                        @endif
                        @if(adminAccessRoute(config('role.account_management.access.add')))
                        <div>
                            <button
                                class="btn {{ $currentRoute == 'admin.account_management.add_account' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.account_management.add_account') }}" class="menu-link">
                                    <div data-i18n="Add Accounts">Add Accounts</div>
                                </a>
                            </button>
                        </div>
                        @endif
                        @if(adminAccessRoute(config('role.e_wallet_accounts.access.edit')))
                        <div>
                            <button
                                class="btn {{ $currentRoute == 'admin.account_management.on_off_account' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.account_management.on_off_account') }}" class="menu-link">
                                    <div data-i18n="Add Accounts">On/Off Accounts</div>
                                </a>
                            </button>
                        </div>
                        @endif
                        @if(adminAccessRoute(config('role.account_group.access.view')))
                        <div>
                            <button
                                class="btn {{ $currentRoute == 'admin.account_management.account_group' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.account_management.account_group') }}" class="menu-link">
                                    <div data-i18n="Account Group">Account Group</div>
                                </a>
                            </button>
                        </div>
                        @endif
                        @if(adminAccessRoute(config('role.gateways.access.view')))
                        <div>
                            <button
                                class="btn {{ $currentRoute == 'admin.account_management.gateway' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.account_management.gateway') }}" class="menu-link">
                                    <div data-i18n="Gateway">Gateway</div>
                                </a>
                            </button>
                        </div>
                        @endif
                        @if(adminAccessRoute(config('role.categories.access.view')))
                        <div>
                            <button
                                class="btn {{ $currentRoute == 'admin.account_management.add_category' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.account_management.add_category') }}" class="menu-link">
                                    <div data-i18n="Add Category">Categories</div>
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

                </div>
                <div class="mb-3"> <button type="button" class="btn btn-primary" id="newCategoryButton">
                        Send All Status Notice
                    </button>
                    <button type="button" class="btn btn-warning" id="newCategoryButton">
                        Send Selected Notice
                    </button>
                </div>

                <div class="table-responsive">
                    <table class=" table table-hover table-striped table-bordered table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">Account Name</th>
                                <th scope="col" class="text-center"> Status</th>
                                <th scope="col" class="text-center">Deposit </th>
                                <th scope="col" class="text-center">Withdrawal </th>
                                <th scope="col" class="text-center">Sent selected</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $key => $item)
                            <td>
                                {{ $item['e_wallet_name'] }}

                            </td>
                            <!-- For Account Status -->

                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="form-check form-switch m-0">
                                        <input type="checkbox" class="form-check-input toggle-status"
                                            id="toggle_{{ $item['id'] }}" data-id="{{ $item['id'] }}" data-type="status"
                                            {{ in_array($item->status, ['1', 1, true]) ? 'checked' : '' }}>
                                    </div>
                                    <label for="toggle_{{ $item['id'] }}" class="ms-2 mb-0 fw-bold">
                                        {{ in_array($item->status, ['1', 1, true]) ? 'On' : 'Off' }}
                                    </label>
                                </div>
                            </td>

                            <!-- Deposit Toggle -->
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="form-check form-switch m-0">
                                        <input type="checkbox" class="form-check-input deposit_action_type_toggle"
                                            data-id="{{ $item->id }}" data-type="deposit"
                                            id="deposit-toggle_{{ $item->id }}" {{ in_array($item->account_type,
                                        ['Deposit', 'Both']) ? 'checked' : '' }}>
                                    </div>
                                    <label for="deposit-toggle_{{ $item->id }}" class="ms-2 mb-0 fw-bold">
                                        {{ in_array($item->account_type, ['Deposit', 'Both']) ? 'On' : 'Off' }}
                                    </label>
                                </div>
                            </td>

                            <!-- Withdrawal Toggle -->
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="form-check form-switch m-0">
                                        <input type="checkbox" class="form-check-input withdraw_action_type_toggle"
                                            data-id="{{ $item->id }}" data-type="withdrawal"
                                            id="withdrawal-toggle_{{ $item->id }}" {{ in_array($item->account_type,
                                        ['Withdrawal', 'Both']) ? 'checked' : '' }}>
                                    </div>
                                    <label for="withdrawal-toggle_{{ $item->id }}" class="ms-2 mb-0 fw-bold">
                                        {{ in_array($item->account_type, ['Withdrawal', 'Both']) ? 'On' : 'Off' }}
                                    </label>
                                </div>
                            </td>


                            <td class="text-center">
                                <input type="checkbox" name="checkbox_{{ $item->id }}" value="{{ $item->id }}">
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary btn-sm">
                                    <i class="fa fa-paper-plane me-1"></i> Send Notice
                                </button>
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


    @push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{asset('assets/DataTables/datatables.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $(".deposit_action_type_toggle, .withdraw_action_type_toggle").on("change", function () {
                let id = $(this).data("id");
                let depositChecked = $("#deposit-toggle_" + id).is(":checked");
                let withdrawalChecked = $("#withdrawal-toggle_" + id).is(":checked");
                let account_type = "";
                if (depositChecked && withdrawalChecked) {
                    account_type = "Both";
                } else if (depositChecked) {
                    account_type = "Deposit";
                } else if (withdrawalChecked) {
                    account_type = "Withdrawal";
                } else {
                    account_type = "";
                }
                $.ajax({
                    url: "{{ route('admin.wallet.updateAccountType') }}",
                    method: "POST",
                    data: { _token: "{{ csrf_token() }}", id: id, account_type: account_type },
                    success: function (response) {
                    if(response.success){
                        alert(response.message);
                    }
                    },
                    error: function (xhr) {
                        alert("Something went wrong!");
                    },
                });
            });

            $(document).on('change', '.toggle-status', function () {
                let accountId = $(this).data('id');
                let status = $(this).is(':checked') ? 1 : 0;

                $.ajax({
                    url: '{{ route("admin.ewallet-account.toggleStatus") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: accountId,
                        status: status
                    },
                    success: function (response) {
                        if (response.success) {
                            alert('Status updated successfully');
                        } else {
                            alert('Failed to update status');
                        }
                    },
                    error: function () {
                        alert('Something went wrong.');
                    }
                });
            });

        });
    </script>



    @endpush
</x-admin-layout>
