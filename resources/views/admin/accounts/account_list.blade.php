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
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addGatewayModal" id="newCategoryButton">
                        Add New Gateway
                    </button>
                </div>

                <table class="categories-show-table table table-hover table-striped table-bordered settable table-sm">
                    <thead class="thead-dark bg-primary">
                        <tr>
                            <th scope="col">@lang('ID')</th>
                            <th scope="col">@lang('Name')</th>
                            <th scope="col">@lang('Status')</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <div id="tableLoader" class="loading-overlay d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Processing...</span>
                    </div>
                </div>
            </div>


        </div>
    </div>







    @push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{asset('assets/DataTables/datatables.min.js')}}"></script>
    @endpush
</x-admin-layout>
