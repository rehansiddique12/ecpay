<x-admin-layout :title="$pageTitle">
    @push('styles')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}">
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
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                <div class="card-body">
                    <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
                    <div class="col-md-5 gap-6 d-flex justify-content-between">
                        {{-- @if(adminAccessRoute(config('role.manage_staff.access.view'))) --}}
                        <div>
                            <button
                                class="btn {{ $currentRoute == 'admin.ewallet.accounts.details' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.ewallet.accounts.details') }}" class="menu-link">
                                    <div data-i18n="Accounts List">Accounts List</div>
                                </a>
                            </button>
                        </div>
                        <div>
                            <button
                                class="btn {{ $currentRoute == 'admin.account_management.add_account' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.account_management.add_account') }}" class="menu-link">
                                    <div data-i18n="Add Accounts">Add Accounts</div>
                                </a>
                            </button>
                        </div>

                        <div>
                            <button class="btn {{ $currentRoute == 'admin.account_management.add_category' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.account_management.add_category') }}" class="menu-link">
                                    <div data-i18n="Add Category">Add Category</div>
                                </a>
                            </button>
                        </div>
                        {{-- @endif --}}


                    </div>
                    <div class="row">
                        <h6>Create Account In Batch
                        </h6>
                        <form method="post" action="{{ route('admin.accounts.create') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-6 col-6">
                                    <label>{{ trans('Category Name') }}</label>
                                    <select class="form-select" name="category_id">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id ?? '' }}">{{ $category->name ?? '' }}</option>
                                        @endforeach

                                    </select>

                                    @error('category_id')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>


                                <div class="form-group col-md-4 col-4">
                                    <label>{{ trans('Select Account Name') }}</label>
                                    <select class="form-select" name="account_id">
                                        <option value="">Select Account Name</option>
                                        @foreach($methods as $account)
                                        <option value="{{ $account->id ?? '' }}">{{ $account->name ?? '' }}</option>
                                        @endforeach

                                    </select>

                                    @error('account_id')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6 col-6">
                                    <label>{{ trans('Currency') }}</label>
                                    <select class="form-select" name="currency">
                                        <option value="">Select Currency</option>
                                        <option value="INR">INR</option>
                                    </select>
                                    @error('currency')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </form>

                    </div>

                </div>
            </div>
        </div>
    </div>


    @push('js')
    <script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
    @endpush
</x-admin-layout>
