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

                </div>
                <button type="button" class="btn btn-primary" id="newCategoryButton">
                    {{ __('accounts.send_all_status_notice') }}
                </button>
                <button type="button" class="btn btn-warning" id="newCategoryButton">
                    {{ __('accounts.send_selected_notice') }}
                </button>

                <div class="table-responsive">
                    <table class=" table table-hover table-striped table-bordered table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">{{ __('accounts.account_name') }}</th>
                                <th scope="col" class="text-center">{{ __('accounts.status') }}</th>
                                <th scope="col" class="text-center">{{ __('accounts.deposit') }}</th>
                                <th scope="col" class="text-center">{{ __('accounts.withdrawal') }}</th>
                                <th scope="col" class="text-center">{{ __('accounts.sent_selected') }}</th>
                                <th>{{ __('accounts.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $gateway)
                                <td>
                                    {{ $gateway->name }}

                                </td>
                                <!-- For Account Status -->

                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <div class="form-check form-switch m-0">
                                            <input type="checkbox" class="form-check-input toggle-status"
                                                id="toggle_{{ $gateway->id }}" data-id="{{ $gateway->id }}"
                                                data-type="status"
                                                {{ in_array($gateway->status, ['1', 1, true]) ? 'checked' : '' }}>
                                        </div>
                                        <label for="toggle_{{ $gateway->id }}" class="ms-2 mb-0 fw-bold">
                                            {{ in_array($gateway->status, ['1', 1, true]) ? __('accounts.on') : __('accounts.off') }}
                                        </label>
                                    </div>
                                </td>

                                <!-- Deposit Toggle -->
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <div class="form-check form-switch m-0">
                                            <input type="checkbox" class="form-check-input deposit_action_type_toggle"
                                                data-id="{{ $gateway->id }}" data-type="deposit_on"
                                                id="deposit-toggle_{{ $gateway->id }}"
                                                {{ in_array($gateway->deposit_on, ['1', 1, true]) ? 'checked' : '' }}>
                                        </div>
                                        <label for="deposit-toggle_{{ $gateway->id }}" class="ms-2 mb-0 fw-bold">
                                            {{ in_array($gateway->deposit_on, ['1', 1, true]) ? __('accounts.on') : __('accounts.off') }}
                                        </label>
                                    </div>
                                </td>

                                <!-- Withdrawal Toggle -->
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <div class="form-check form-switch m-0">
                                            <input type="checkbox" class="form-check-input withdraw_action_type_toggle"
                                                data-id="{{ $gateway->id }}" data-type="withdrawal_on"
                                                id="withdrawal-toggle_{{ $gateway->id }}"
                                                {{ in_array($gateway->withdrawal_on, ['1', 1, true]) ? 'checked' : '' }}>
                                        </div>
                                        <label for="withdrawal-toggle_{{ $gateway->id }}" class="ms-2 mb-0 fw-bold">
                                            {{ in_array($gateway->withdrawal_on, ['1', 1, true]) ? __('accounts.on') : __('accounts.off') }}
                                        </label>
                                    </div>
                                </td>


                                <td class="text-center">
                                    <input type="checkbox" name="checkbox_{{ $gateway->id }}"
                                        value="{{ $gateway->id }}">
                                </td>
                                <td>
                                    <button data-id="{{ $gateway->id}}" type="button" class="btn btn-primary btn-sm send_notice_buttons">
                                        <i class="fa fa-paper-plane me-1"></i> {{ __('accounts.send_notice') }}
                                    </button>
                                </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100%">
                                        <p class="text-dark">{{ __('accounts.no_data_found') }}</p>
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
        <script src="{{ asset('assets/DataTables/datatables.min.js') }}"></script>
        <script>
            $(document).ready(function() {
                $(".deposit_action_type_toggle").on("change", function() {
                    let id = $(this).data("id");
                    let status = $(this).is(':checked') ? 1 : 0;

                    $.ajax({
                        url: "{{ route('admin.wallet.updateGatewayDeposit') }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id,
                            status: status
                        },
                        success: function(response) {
                            if (response.success) {
                                // alert(response.message);
                                alert("{{ __('accounts.gateway_deposit_updated') }}");
                            }
                        },
                        error: function(xhr) {
                            alert("{{ __('accounts.something_went_wrong') }}");
                        },
                    });
                });

                $(".withdraw_action_type_toggle").on("change", function() {
                    let id = $(this).data("id");
                    let status = $(this).is(':checked') ? 1 : 0;

                    $.ajax({
                        url: "{{ route('admin.wallet.updateGatewayWithdrawal') }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id,
                            status: status
                        },
                        success: function(response) {
                            if (response.success) {
                                // alert(response.message);
                                alert("{{ __('accounts.gateway_withdrawal_updated') }}");
                            }
                        },
                        error: function(xhr) {
                            alert("{{ __('accounts.something_went_wrong') }}");
                        },
                    });
                });

                $(document).on('change', '.toggle-status', function() {
                    let accountId = $(this).data('id');
                    let status = $(this).is(':checked') ? 1 : 0;

                    $.ajax({
                        url: "{{ route('admin.ewallet-account.toggleStatus') }}",
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: accountId,
                            status: status
                        },
                        success: function(response) {
                            if (response.success) {
                                alert("{{ __('accounts.status_updated_successfully') }}");
                            } else {
                                alert("{{ __('accounts.failed_to_update_status') }}");
                            }
                        },
                        error: function() {
                            alert("{{ __('accounts.something_went_wrong') }}");
                        }
                    });
                });

                 $('.send_notice_buttons').on('click', function () {
                    let gatewayId = $(this).data('id');

                    // Disable all buttons
                    $('.send_notice_buttons').prop('disabled', true);

                    $.ajax({
                        url: '{{ route("admin.gateway.send_notice") }}', // update with your route name
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: gatewayId
                        },
                        success: function (response) {
                            alert('Notice sent successfully!');
                            // Handle success message or UI update here
                        },
                        error: function (xhr) {
                            alert('Error sending notice.');
                            // Optionally show more error info here
                        },
                        complete: function () {
                            // Re-enable all buttons after request completes
                            $('.send_notice_buttons').prop('disabled', false);
                        }
                    });
                });

            });
        </script>
    @endpush
</x-admin-layout>
