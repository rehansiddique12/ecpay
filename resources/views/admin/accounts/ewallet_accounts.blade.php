<x-admin-layout :title="$pageTitle">
    @push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
    <style>
        tr th {
            color: white !important
        }

        .categories-show-table {
            display: none;
            /* Initially hidden */
        }

        h3 {
            color: #7367f0 !important
        }

        .dropzone-container {
            width: 100%;
        }

        .dropzone {
            border: 1px dashed #ccc;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            position: relative;
            transition: all 0.3s ease;
        }

        .dropzone:hover {
            border-color: #999;
            background-color: #f9f9f9;
        }

        .upload-icon {
            background-color: #f0f0f0;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .upload-svg {
            color: #666;
        }

        .dropzone-title {
            font-size: 1.125rem;
            color: #333;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .dropzone-description {
            font-size: 0.875rem;
            color: #666;
            margin: 0;
        }

        .hidden-input {
            position: absolute;
            width: 0;
            height: 0;
            opacity: 0;
        }

        .preview-image {
            max-width: 100%;
            margin-top: 1rem;
            border-radius: 4px;
            display: none;
        }

        #image_preview_container:not([src="/placeholder.svg"]) {
            display: block;
        }

        label {
            margin-bottom: 5px;
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

        {{-- <div class="row">
            <form action="{{ route('admin.ewallet.accounts.details')}}" method="get">
                <div class="row align-items-center">

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-select" name="status" data-placeholder="Status">
                                <option value="" {{ request()->status === null ? 'selected' : '' }}>All</option>
                                <option value="1" {{ request()->status === '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ request()->status === '0' ? 'selected' : '' }}>In-Active</option>
                            </select>
                        </div>
                    </div>


                    <div class="col-md-2">
                        <div class="form-group">
                            <br>
                            <button type="submit" class="btn waves-effect waves-light btn-primary"><i
                                    class="icon-base ti tabler-search me-1"></i> {{ __('reports.search') }}</button>
                        </div>
                    </div>

                </div>
            </form>
        </div> --}}
    </div>

    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">

            <div id="listaccountsSection">
                @include('admin.payout.accounts')

            </div>

        </div>
    </div>



    @push('js')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    @endpush
</x-admin-layout>
