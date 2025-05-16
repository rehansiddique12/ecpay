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
    </style>
    <style>
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
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                <div class="card-body">
                    <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <a href="javascript:void(0)" class="btn btn-primary" id="listaccountsTab">
                            <div>Accounts List </div>
                        </a>
                        <a href="javascript:void(0)" class="btn btn-primary" id="addaccountsTab">
                            <div>Add Accounts </div>
                        </a>
                        <a href="javascript:void(0)" class="btn btn-primary" id="inoffTab">
                            <div>In/off Account </div>
                        </a>
                        <a href="javascript:void(0)" class="btn btn-primary" id="accountsGroupTab">
                            <div>Accounts Group </div>
                        </a>

                        <a href="javascript:void(0)" class="btn btn-primary" id="gatewayTab">
                            <div>Gateways</div>
                        </a>

                        <a href="javascript:void(0)" class="btn btn-primary" id="categoryTab">
                            <div>Category</div>
                        </a>
                    </div>
                    <div id="listaccountsSection">
                        @include('admin.payout.accounts')

                    </div>
                    <div id="inoffSection">
                        @include('admin.payout.inout')

                    </div>
                    <div id="accountsGroupSection">

                        @include('admin.accounts.groups')

                    </div>
                    <div id="addaccountsSection" style="display:none;">
                        @include('admin.payout.create_account')
                    </div>
                    <div id="gatewaySection" style="display:none;">
                        <h6 style="color: #7367f0">Add Gateways
                        </h6>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card card-primary shadow">
                                        <div class="card-body">
                                            @if(adminAccessRoute(config('role.payment_gateway.access.add')))
                                            <button type="button" class="btn btn-success btn-sm float-right mb-3"
                                                data-bs-toggle="modal" data-bs-target="#addGatewayModal">
                                                <i class="fa fa-plus-circle"></i> {{ trans('Add New') }}
                                            </button>

                                            @endif

                                            <table class="table">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th scope="col">@lang('Name')</th>
                                                        <th scope="col">@lang('Status')</th>

                                                        @if(adminAccessRoute(config('role.payment_gateway.access.edit')))
                                                        <th scope="col">@lang('Action')</th>
                                                        @endif
                                                    </tr>

                                                </thead>
                                                <tbody id="sortable">
                                                    @if(count($methods) > 0)
                                                    @foreach($methods as $method)
                                                    <tr data-code="{{ $method->code }}">
                                                        <td data-label="@lang('Name')">{{ $method->name }} </td>
                                                        <td data-label="@lang('Status')"
                                                            class="text-lg-center text-right">

                                                            {!! $method->status == 1 ? '<span
                                                                class="badge badge-light"><i
                                                                    class="fa fa-circle text-success success font-12"></i>'.trans('
                                                                Active').'</span>' : '<span class="badge badge-light"><i
                                                                    class="fa fa-circle text-danger danger font-12"></i>'.trans('
                                                                Inactive').'</span>' !!}
                                                        </td>
                                                        @if(adminAccessRoute(config('role.payment_gateway.access.edit')))
                                                        <td data-label="@lang('Action')">
                                                            <button type="button"
                                                                class="btn btn-primary btn-circle editBtn"
                                                                data-id="{{ $method->id }}"
                                                                data-name="{{ $method->name }}"
                                                                data-method-id="{{ $method->id }}"
                                                                data-code="{{ $method->code }}" data-bs-toggle="modal"
                                                                data-bs-target="#editMethodModal" data-toggle="tooltip"
                                                                data-placement="top"
                                                                title="@lang('Edit this Payment Methods info')">
                                                                <i class="icon-base ti tabler-pencil me-1"></i>
                                                            </button>

                                                            <button type="button" data-code="{{$method->code}}"
                                                                data-status="{{$method->status}}"
                                                                data-message="{{($method->status == 0)?'Enable':'Disable'}}"
                                                                class="btn btn-sm btn-{{($method->status == 0)?'success':'danger'}}   btn-circle disableBtn"
                                                                data-bs-toggle="modal" data-bs-target="#disableModal"><i
                                                                    class="icon-base ti tabler-{{($method->status == 0)?'check':'ban'}}"></i>
                                                            </button>
                                                        </td>
                                                        @endif
                                                    </tr>
                                                    @endforeach
                                                    @else
                                                    <tr>
                                                        <td class="text-center text-danger" colspan="8">
                                                            @lang('No Data Found')
                                                        </td>
                                                    </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>





                        <div class="modal modal-top fade" id="disableModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalTopTitle">@lang('Confirmation')</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('admin.accounts.payment.methods.deactivate') }}"
                                        method="POST">
                                        @csrf
                                        <input type="hidden" name="code">
                                        <div class="modal-body">
                                            <p class="font-weight-bold">@lang('Are you sure to') <span
                                                    class="messageShow"></span> {{trans('this?')}}</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn waves-effect waves-light btn-dark"
                                                data-bs-dismiss="modal">@lang('Close')</button>
                                            <button type="submit"
                                                class="btn waves-effect waves-light btn-primary messageShow"></button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="table-responsive">
                        <div class="card-header">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#newModal" id="newCategoryButton" style="display:none;">
                                Add Category
                            </button>
                        </div>
                        <table
                            class="categories-show-table table table-hover table-striped table-bordered settable table-sm">
                            <thead class="thead-dark bg-primary">
                                <tr>
                                    <th scope="col">@lang('ID')</th>
                                    <th scope="col">@lang('Category Name')</th>
                                    <th scope="col">@lang('Status')</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $key => $item)
                                <tr>
                                    <td>{{ $item['id'] }}</td>
                                    <td>{{ $item['name'] ?? '' }}</td>
                                    <td>
                                        <label class="switch" style="cursor: pointer;">
                                            <input type="checkbox" class="switch-input toggle-status"
                                                data-id="{{ $item['id'] }}" {{ $item['status'] == 1 ? 'checked' : '' }}>
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on"></span>
                                                <span class="switch-off"></span>
                                            </span>
                                            <span class="switch-label status-label-{{ $item['id'] }}">
                                                {{ $item['status'] == 1 ? 'Active' : 'Inactive' }}
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
                                                <form action="{{ route('admin.category.delete', $item['id']) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-icon edit_button"><i
                                                            class="icon-base ti tabler-trash me-1"></i> Delete</button>
                                                </form>
                                                <button type="button" class="btn btn-sm btn-icon edit_button"
                                                    data-bs-toggle="modal" data-bs-target="#editModal{{ $item['id'] }}">
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach ($categories as $item)
    @php
    $bankEwallets = is_array($item['bank_ewallets'] ?? null) ? $item['bank_ewallets'] :
    json_decode($item['bank_ewallets'], true);
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
                            <button type="button" class="btn btn-dark" data-bs-dismiss="modal"
                                aria-label="Close">@lang('Close')</button>
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
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal"
                            aria-label="Close">@lang('Close')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addGatewayModal" tabindex="-1" aria-labelledby="addGatewayModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header  bg-primary text-white">
                    <h5 class="modal-title" id="addGatewayModalLabel">{{ trans('Add New Gateway') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="post" action="{{ route('admin.deposit.accounts.create') }}"
                    class="needs-validation base-form" novalidate enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-md-4 mt-3">
                                <label>{{trans('Name')}}</label>
                                <input type="text" class="form-control " name="name" value="{{ old('name') }}"
                                    required="">
                                @if ($errors->has('name'))
                                <span class="invalid-text">
                                    {{ trans($errors->first('name')) }}
                                </span>
                                @endif
                            </div>

                            <div class="form-group col-md-4 mt-3">
                                <label>{{trans('Currency')}}</label>
                                <input type="text" class="form-control " name="currency" value="{{ old('currency') }}"
                                    required="required">

                                @if ($errors->has('currency'))
                                <span class="invalid-text">
                                    {{ trans($errors->first('currency')) }}
                                </span>
                                @endif
                            </div>

                            <div class="form-group col-md-4 mt-3">
                                <label>Type</label>
                                <select class="form-control" name="type" required>
                                    @foreach($categories as $type)
                                    <option value="{{$type->id ?? ''}}">{{$type->name ?? ''}}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- <div class="form-group col-md-4 mt-3">
                                    <label>{{trans('Convention Rate')}}</label>
                            <div class="input-group ">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        1 {{ $basic->currency ? : 'USD' }} =
                                    </div>
                                </div>
                                <input type="text" class="form-control " name="convention_rate"
                                    value="{{ old('convention_rate') }}" required>
                                <div class="input-group-append">
                                    <div class="input-group-text set-currency">

                                    </div>
                                </div>
                            </div>

                            @if ($errors->has('convention_rate'))
                            <span class="invalid-text">
                                {{ trans($errors->first('currency_symbol')) }}
                            </span>
                            @endif
                        </div> --}}
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4 mt-3 col-4">
                            <label>{{trans('Minimum Deposit Amount')}}</label>
                            <div class="input-group ">
                                <input type="text" class="form-control " name="minimum_deposit_amount"
                                    value="{{ old('minimum_deposit_amount') }}" required="">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        {{ $basic->currency ?? trans('USD') }}
                                    </div>
                                </div>
                            </div>

                            @if ($errors->has('minimum_deposit_amount'))
                            <span class="invalid-text">
                                {{ trans($errors->first('minimum_deposit_amount')) }}
                            </span>
                            @endif
                        </div>

                        <div class="form-group col-md-4 mt-3 col-5">
                            <label>{{trans('Minimum WithDrawl Amount')}}</label>
                            <div class="input-group ">
                                <input type="text" class="form-control " name="minimum_withdrawal_amount"
                                    value="{{ old('minimum_withdrawal_amount') }}" required="">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        {{ $basic->currency ?? trans('USD') }}
                                    </div>
                                </div>
                            </div>

                            @if ($errors->has('minimum_deposit_amount'))
                            <span class="invalid-text">
                                {{ trans($errors->first('minimum_withdrawl_amount')) }}
                            </span>
                            @endif
                        </div>
                        <div class="form-group col-md-4 mt-3 col-4">
                            <label>{{trans('Maximum Deposit Amount')}}</label>
                            <div class="input-group ">
                                <input type="text" class="form-control " name="maximum_deposit_amount"
                                    value="{{ old('maximum_deposit_amount') }}" required="">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        {{ $basic->currency ?? trans('USD') }}
                                    </div>
                                </div>
                            </div>

                            @if ($errors->has('maximum_deposit_amount'))
                            <span class="invalid-text">
                                {{ trans($errors->first('maximum_deposit_amount')) }}
                            </span>
                            @endif
                        </div>

                        <div class="form-group col-md-4 mt-3 col-4">
                            <label>{{trans('Maximum WithDrawl Amount')}}</label>
                            <div class="input-group ">
                                <input type="text" class="form-control " name="maximum_withdrawal_amount"
                                    value="{{ old('maximum_withdrawal_amount') }}" required="">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        {{ $basic->currency ?? trans('USD') }}
                                    </div>
                                </div>
                            </div>

                            @if ($errors->has('maximum_withdrawl_amount'))
                            <span class="invalid-text">
                                {{ trans($errors->first('maximum_withdrawl_amount')) }}
                            </span>
                            @endif
                        </div>
                    </div>
                    {{-- <div class="row">
                                <div class="form-group col-md-6 col-6">
                                    <label>{{trans('Percentage Charge')}}</label>
                    <div class="input-group ">
                        <input type="text" class="form-control " name="percentage_charge"
                            value="{{ old('percentage_charge') }}" required="">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                {{trans('%')}}
                            </div>
                        </div>
                    </div>

                    @if ($errors->has('percentage_charge'))
                    <span class="invalid-text">
                        {{ trans($errors->first('percentage_charge')) }}
                    </span>
                    @endif
            </div>
            <div class="form-group col-md-6 col-6">
                <label>@lang('Fixed Charge')</label>
                <div class="input-group ">
                    <input type="text" class="form-control " name="fixed_charge" value="{{ old('fixed_charge') }}"
                        required="">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            {{ $basic->currency ?? trans('USD') }}
                        </div>
                    </div>
                </div>

                @if ($errors->has('fixed_charge'))
                <span class="invalid-text">
                    {{ trans($errors->first('fixed_charge')) }}
                </span>
                @endif
            </div>
        </div> --}}

        <div class="row justify-content-between">
            <div class="col-sm-6 col-md-4 mt-3">
                <div class="image-input dropzone-container mt-5">
                    <div class="dropzone" id="image-dropzone" onclick="document.getElementById('image').click()">
                        <div class="upload-icon" id="upload-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="upload-svg">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                        </div>
                        <h3 class="dropzone-title" id="dropzone-title">Drop files here or click to upload</h3>
                        <p class="dropzone-description" id="dropzone-description">
                            (This is just a demo dropzone. Selected files are not actually uploaded.)
                        </p>

                        <input type="file" name="image" id="image" class="hidden-input" accept="image/*"
                            style="display:none;" onchange="handleImageSelection(event)">

                        <!-- Preview Image -->
                        <img id="image_preview_container" class="preview-image" src="" alt="Preview Image"
                            style="display: none; max-width: 100%; height: auto; margin-top: 10px;">
                    </div>
                    @error('image')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <script>
                function handleImageSelection(event) {
                    const file = event.target.files[0]; // Get the selected file

                    if (file) {
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            // Set the image source to the selected file's data URL
                            const imagePreview = document.getElementById('image_preview_container');
                            imagePreview.src = e.target.result;
                            imagePreview.style.display = 'block'; // Show the preview image

                            // Hide the dropzone elements
                            document.getElementById('upload-icon').style.display = 'none';
                            document.getElementById('dropzone-title').style.display = 'none';
                            document.getElementById('dropzone-description').style.display = 'none';
                        };

                        reader.readAsDataURL(file); // Read the file as a data URL
                    }
                }
                </script>

            </div>
            {{-- <div class="col-sm-12 col-md-9">
                                    <div class="form-group ">
                                        <label>@lang('Note')</label>
                                        <textarea class="form-control summernote" name="note" id="summernote" rows="15">{{old('note')}}</textarea>
            @error('note')
            <span class="text-danger">{{ trans($message) }}</span>
            @enderror
        </div>
    </div> --}}
    </div>
    <div class="row mt-3 justify-content-between">
        <div class="col-lg-3 col-md-6">
            <div class="form-group">
                <label>@lang('Status')</label>
                <div class="form-check form-switch d-flex align-items-center">
                    <span id="disableText" class="me-12 text-primary">@lang('No')</span>
                    <input class="form-check-input" type="checkbox" id="statusSwitch" name="status" value="1">
                    <span id="enableText" class="ms-2 text-secondary">@lang('Yes')</span>
                </div>
            </div>
        </div>
    </div>




    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('Close') }}</button>
        <button type="submit" class="btn btn-primary">{{ trans('Save') }}</button>
    </div>
    </form>
    </div>
    </div>
    </div>

    <div class="modal fade" id="editMethodModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editModalLabel">{{ __('Edit Deposit Method') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form method="post" action="{{ route('admin.deposit.accounts.update', $method) }}"
                        class="needs-validation" novalidate enctype="multipart/form-data">
                        @csrf
                        @method('put')

                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>{{ __('Name') }}</label>
                                <input type="text" class="form-control" name="name"
                                    value="{{ old('name', $method->name) }}" required>
                                @error('name')
                                <small class="text-danger">{{ trans($message) }}</small>
                                @enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label>{{ __('Currency') }}</label>
                                <input type="text" class="form-control" name="currency"
                                    value="{{ old('currency', $method->currency) }}" required>
                                @error('currency')
                                <small class="text-danger">{{ trans($message) }}</small>
                                @enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label>{{ __('Conversion Rate') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">1
                                            {{ $basic->currency ?? 'USD' }} =</span></div>
                                    <input type="text" name="convention_rate" class="form-control"
                                        value="{{ old('convention_rate', getAmount($method->convention_rate)) }}"
                                        required>
                                    <div class="input-group-append"><span class="input-group-text set-currency"></span>
                                    </div>
                                </div>
                                @error('convention_rate')
                                <small class="text-danger">{{ trans($message) }}</small>
                                @enderror
                            </div>
                        </div>

                        {{-- Deposit Limits --}}
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>{{ __('Minimum Deposit Amount') }}</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="minimum_deposit_amount"
                                        value="{{ old('minimum_deposit_amount', getAmount($method->min_amount)) }}"
                                        required>
                                    <div class="input-group-append"><span
                                            class="input-group-text">{{ $basic->currency ?? 'USD' }}</span></div>
                                </div>
                                @error('minimum_deposit_amount')
                                <small class="text-danger">{{ trans($message) }}</small>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label>{{ __('Maximum Deposit Amount') }}</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="maximum_deposit_amount"
                                        value="{{ old('maximum_deposit_amount', getAmount($method->max_amount)) }}"
                                        required>
                                    <div class="input-group-append"><span
                                            class="input-group-text">{{ $basic->currency ?? 'USD' }}</span></div>
                                </div>
                                @error('maximum_deposit_amount')
                                <small class="text-danger">{{ trans($message) }}</small>
                                @enderror
                            </div>
                        </div>

                        {{-- Withdrawal Limits --}}
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>{{ __('Minimum Withdrawal Amount') }}</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="minimum_withdrawal_amount"
                                        value="{{ old('minimum_withdrawal_amount', getAmount($method->minimum_withdrawal_amount)) }}"
                                        required>
                                    <div class="input-group-append"><span
                                            class="input-group-text">{{ $basic->currency ?? 'USD' }}</span></div>
                                </div>
                                @error('minimum_withdrawal_amount')
                                <small class="text-danger">{{ trans($message) }}</small>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label>{{ __('Maximum Withdrawal Amount') }}</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="maximum_withdrawal_amount"
                                        value="{{ old('maximum_withdrawal_amount', getAmount($method->maximum_withdrawal_amount)) }}"
                                        required>
                                    <div class="input-group-append"><span
                                            class="input-group-text">{{ $basic->currency ?? 'USD' }}</span></div>
                                </div>
                                @error('maximum_withdrawal_amount')
                                <small class="text-danger">{{ trans($message) }}</small>
                                @enderror
                            </div>
                        </div>

                        {{-- Charges --}}
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>{{ __('Percentage Charge') }}</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="percentage_charge"
                                        value="{{ old('percentage_charge', getAmount($method->percentage_charge)) }}"
                                        required>
                                    <div class="input-group-append"><span class="input-group-text">%</span></div>
                                </div>
                                @error('percentage_charge')
                                <small class="text-danger">{{ trans($message) }}</small>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label>{{ __('Fixed Charge') }}</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="fixed_charge"
                                        value="{{ old('fixed_charge', getAmount($method->fixed_charge)) }}" required>
                                    <div class="input-group-append"><span
                                            class="input-group-text">{{ $basic->currency ?? 'USD' }}</span></div>
                                </div>
                                @error('fixed_charge')
                                <small class="text-danger">{{ trans($message) }}</small>
                                @enderror
                            </div>
                        </div>

                        {{-- Logo & Note --}}
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label>{{ __('Gateway Logo') }}</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="image" id="image">
                                    <label class="custom-file-label" for="image">{{ __('Choose file') }}</label>
                                </div>
                                <div class="mt-2">
                                    <img id="image_preview_container" class="img-thumbnail w-100"
                                        src="{{ getFile(config('location.gateway.path') . $method->image) }}"
                                        alt="Preview">
                                </div>
                                @error('image')
                                <small class="text-danger">{{ trans($message) }}</small>
                                @enderror
                            </div>

                            <div class="form-group col-md-9">
                                <label>{{ __('Note') }}</label>
                                <textarea class="form-control summernote" name="note"
                                    rows="10">{{ old('note', $method->note) }}</textarea>
                                @error('note')
                                <small class="text-danger">{{ trans($message) }}</small>
                                @enderror
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="form-group">
                            <label>{{ __('Status') }}</label>
                            <div class="custom-control custom-switch">
                                <input type="hidden" value="1" name="status">
                                <input type="checkbox" class="custom-control-input" id="status" name="status" value="0"
                                    {{ $method->status == 0 ? 'checked' : '' }}>
                                <label class="custom-control-label" for="status">{{ __('Inactive') }}</label>
                            </div>
                        </div>

                        {{-- Dynamic Fields --}}
                        <div class="addedField">
                            @if($method->parameters)
                            @foreach ($method->parameters as $k => $v)
                            <div class="form-group">
                                <div class="input-group">
                                    <input name="field_name[]" class="form-control" type="text"
                                        value="{{ $v->field_level }}" required placeholder="{{ trans('Field Name') }}">

                                    <select name="type[]" class="form-control">
                                        <option value="text" {{ $v->type == 'text' ? 'selected' : '' }}>
                                            {{ trans('Input Text') }}</option>
                                        <option value="textarea" {{ $v->type == 'textarea' ? 'selected' : '' }}>
                                            {{ trans('Textarea') }}</option>
                                        <option value="file" {{ $v->type == 'file' ? 'selected' : '' }}>
                                            {{ trans('File Upload') }}</option>
                                    </select>

                                    <select name="validation[]" class="form-control">
                                        <option value="required" {{ $v->validation == 'required' ? 'selected' : '' }}>
                                            {{ trans('Required') }}</option>
                                        <option value="nullable" {{ $v->validation == 'nullable' ? 'selected' : '' }}>
                                            {{ trans('Optional') }}</option>
                                    </select>

                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-danger delete_desc"><i
                                                class="fas fa-times"></i></button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @endif
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ trans('Close') }}</button>
                            <button type="submit" class="btn btn-primary">{{ trans('Save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    @push('js')
    <script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
    <script>
    $(document).ready(function() {
        // Show table when Category tab is clicked
        $('#categoryTab').click(function() {
            $('.categories-show-table').show();
            $('#newCategoryButton').show();
            $('#gatewaySection').hide();
            $('#addaccountsSection').hide();
            $('#listaccountsSection').hide();
            $('#accountsGroupSection').hide();
            $('#inoffSection').hide();




        });

        $('#gatewayTab').click(function() {
            $('.categories-show-table').hide();
            $('#newCategoryButton').hide();
            $('#gatewaySection').show();
            $('#addaccountsSection').hide();
            $('#listaccountsSection').hide();
            $('#accountsGroupSection').hide();
            $('#inoffSection').hide();
        });


        $('#addaccountsTab').click(function() {
            $('.categories-show-table').hide();
            $('#newCategoryButton').hide();
            $('#gatewaySection').hide();
            $('#addaccountsSection').show();
            $('#listaccountsSection').hide();
            $('#accountsGroupSection').hide();
            $('#inoffSection').hide();

        });

        $('#listaccountsTab').click(function() {
            $('.categories-show-table').hide();
            $('#newCategoryButton').hide();
            $('#gatewaySection').hide();
            $('#addaccountsSection').hide();
            $('#listaccountsSection').show();
            $('#accountsGroupSection').hide();
            $('#inoffSection').hide();

        });

        $('#accountsGroupTab').click(function() {
            $('.categories-show-table').hide();
            $('#newCategoryButton').hide();
            $('#gatewaySection').hide();
            $('#addaccountsSection').hide();
            $('#listaccountsSection').hide();
            $('#accountsGroupSection').show();
            $('#inoffSection').hide();

        });

        $('#inoffTab').click(function() {
            $('.categories-show-table').hide();
            $('#newCategoryButton').hide();
            $('#gatewaySection').hide();
            $('#addaccountsSection').hide();
            $('#listaccountsSection').hide();
            $('#inoffSection').show();

        });


        // Rest of the JS code
        $('#image').change(function() {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#image_preview_container').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });

        $('select').select2({
            selectOnClose: true
        });

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
        $('#gatewayTab').click(function() {
            $('#gatewaySection').show();
            $('.categories-show-table').hide();
            $('#newCategoryButton').hide();
        });
    });
    </script>
    @endpush
    @push('js')
    <script>
    "use strict";

    $(document).ready(function() {
        setCurrency();
        $(document).on('change', 'input[name="currency"]', function() {
            setCurrency();
        });

        function setCurrency() {
            let currency = $('input[name="currency"]').val();
            $('.set-currency').text(currency);
        }

        $(document).on('click', '.copy-btn', function() {
            var _this = $(this)[0];
            var copyText = $(this).parents('.input-group-append').siblings('input');
            $(copyText).prop('disabled', false);
            copyText.select();
            document.execCommand("copy");
            $(copyText).prop('disabled', true);
            $(this).text('Coppied');
            setTimeout(function() {
                $(_this).text('');
                $(_this).html('<i class="fas fa-copy"></i>');
            }, 500)
        });
    })



    $(document).ready(function(e) {

        $("#generate").on('click', function() {
            var form = `<div class="col-md-12">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input name="field_name[]" class="form-control " type="text" value="" required placeholder="{{trans('Field Name')}}">

                                        <select name="type[]"  class="form-control  ">
                                            <option value="text">{{trans('Input Text')}}</option>
                                            <option value="textarea">{{trans('Textarea')}}</option>
                                            <option value="file">{{trans('File upload')}}</option>
                                        </select>

                                        <select name="validation[]"  class="form-control  ">
                                            <option value="required">{{trans('Required')}}</option>
                                            <option value="nullable">{{trans('Optional')}}</option>
                                        </select>

                                        <span class="input-group-btn">
                                            <button class="btn btn-danger delete_desc" type="button">
                                                <i class="icon-base ti tabler-ban me-1"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div> `;

            $('.addedField').append(form)
        });


        $(document).on('click', '.delete_desc', function() {
            $(this).closest('.input-group').parent().remove();
        });


        $('#image').change(function() {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#image_preview_container').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });

        $('.summernote').summernote({
            height: 250,
            callbacks: {
                onBlurCodeview: function() {
                    let codeviewHtml = $(this).siblings('div.note-editor').find('.note-codable')
                        .val();
                    $(this).val(codeviewHtml);
                }
            }
        });
    });
    </script>
    <script>
    $(document).ready(function(e) {
        "use strict";

        $('#image').change(function() {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#image_preview_container').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });


        $('select').select2({
            selectOnClose: true
        });

    });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const disableButtons = document.querySelectorAll('.disableBtn');

        disableButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const code = this.getAttribute('data-code');
                const status = this.getAttribute('data-status');
                const message = this.getAttribute('data-message');
                document.querySelector('#disableModal input[name="code"]').value = code;
                document.querySelectorAll('#disableModal .messageShow').forEach(el => el
                    .innerText = message.toLowerCase());
            });
        });
    });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.editBtn');
        editButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                const methodId = this.getAttribute('data-method-id');
                const name = this.getAttribute('data-name');
                document.querySelector('#editMethodModal input[name="name"]').value = name;
                const modal = new bootstrap.Modal(document.getElementById('editMethodModal'));
                modal.show();
            });
        });
    });
    </script>
    <script>
    $(document).on('change', '.toggle-status', function () {
        let checkbox = $(this);
        let id = checkbox.data('id');
        let isChecked = checkbox.is(':checked');

        $.ajax({
            url: '/admin/category/' + id + '/status',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                if (response.success) {
                    let labelText = response.status === 1 ? 'Active' : 'Inactive';
                    $('.status-label-' + id).text(labelText);
                    toastr.success('Status updated to ' + labelText);
                } else {
                    toastr.error('Failed to update status.');
                    checkbox.prop('checked', !isChecked); // Revert toggle
                }
            },
            error: function () {
                toastr.error('Error occurred while updating status.');
                checkbox.prop('checked', !isChecked); // Revert toggle
            }
        });
    });
</script>



    @endpush
</x-admin-layout>
