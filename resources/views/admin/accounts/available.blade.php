<x-admin-layout :title="$pageTitle">
   
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

            @foreach($result as $walletName => $accounts)
        <div class="card mb-5 shadow-sm">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0 text-capitalize">{{ $walletName }} Accounts</h4>
            </div>
            <div class="card-body">
                @if(count($accounts) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Account No</th>
                                    <th>Active</th>
                                    <th>Available Limit</th>
                                    <th>Time Slot</th>
                                    <th>Final Active</th>
                                    <th>Daily Remaining</th>
                                    <th>Per-Minute Remaining</th>
                                    <th>Last Used</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($accounts as $account)
                                    <tr>
                                        <td>{{ $account['account_no'] ?? '-' }}</td>
                                        <td>
                                            @if($account['active_status'] === 'yes')
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-danger">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($account['available_limit_accounts'] === 'yes')
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-danger">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($account['time_slot_accounts'] === 'yes')
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-danger">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($account['final_active_accounts'] === 'yes')
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-danger">No</span>
                                            @endif
                                        </td>
                                        <td>{{ $account['daily_remaining'] ?? '-' }}</td>
                                        <td>{{ $account['per_minute_remaining'] ?? '-' }}</td>
                                        <td>{{ $account['last_used'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No accounts found for {{ ucfirst($walletName) }}.</p>
                @endif
            </div>
        </div>
    @endforeach


        </div>
    </div>


  


    @push('js')
        
    @endpush
</x-admin-layout>
