<x-admin-layout :title="$pageTitle">

    @push('styles')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}">
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 90px;
            height: 30px;
            font-size: 13px;
            font-weight: bold;
            text-align: center;
            user-select: none;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            color: white;
            line-height: 30px;
            border-radius: 20px;
            transition: 0.4s;
        }

        .slider.active {
            background: linear-gradient(to right, #28a745, #20c997);
        }

        .slider.deactive {
            background: linear-gradient(to right, #dc3545, #d1404f);
        }
    </style>
    @endpush

    <div class="row ">
        <div class="col-md-12">
            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                <div class="card-body">
                    <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
                    {{-- @if (adminAccessRoute(config('role.partners.access.add'))) --}}
                    <button type="button" class="btn btn-primary mb-5" data-bs-toggle="modal"
                        data-bs-target="#newModal">
                        Add New
                    </button>

                    {{-- @endif --}}

                    <div class="table-responsive ">
                        <table
                            class="categories-show-table table table-hover table-striped table-bordered settable table-responsive table-sm">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">@lang('ID')</th>
                                    <th scope="col">@lang('Name')</th>
                                    <th scope="col">@lang('Username')</th>
                                    <th scope="col">@lang('Website')</th>
                                    <th class="setcolumn" scope="col">API End-Point</th>
                                    <th class="setcolumn" scope="col">@lang('Keys')</th>
                                    <th scope="col">@lang('Balance')</th>
                                    <th scope="col">@lang('Min')</th>
                                    <th scope="col">@lang('Status')</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $key => $item)
                                <tr>
                                    <td style="max-width: 70px;">{{ $item['id'] }}</td>
                                    <td style="max-width: 110px;"><a
                                            href="{{ route('admin.merchant.profile', $item->id) }}">{{ $item['name']
                                            }}</a>
                                    </td>
                                    <td style="max-width: 100px;">{{ $item['username'] }}</td>
                                    <td style="max-width: 130px;"><span class="editable" data-id="{{ $item['id'] }}"
                                            data-field="website">{{ $item['website'] }}</span></td>
                                    <td style="max-width: 220px;">
                                        <span class="bg-success text-white p-1 d-inline-block mb-2"
                                            style="border-radius: 8px; padding: 7px;">Deposit:</span>
                                        {{ $item['api_endpoint_deposit'] }}<br>

                                        <span class="bg-warning text-white  d-inline-block mt-2 mb-2"
                                            style="border-radius: 10px; padding: 7px;">Withdrawal:</span>
                                        {{ $item['api_endpoint_withdrawal'] }}<br>

                                        <span class="bg-info text-white  d-inline-block mt-2"
                                            style="border-radius: 10px; padding: 7px;">Redirect
                                            URL:</span>
                                        {{ $item['redirect_url'] }}<br>
                                    </td>

                                    <td style="max-width: 220px;">
                                        <span class="bg-success text-white p-1 d-inline-block mb-2"
                                            style="border-radius: 6px; padding: 7px;">API Key:</span>
                                        <span class="editable" data-id="{{ $item['id'] }}" data-field="api_key">{{
                                            $item['api_key'] }}</span>
                                        <br>

                                        <span class="bg-primary text-white p-1 d-inline-block mt-2 mb-2"
                                            style="border-radius: 8px; padding: 7px;">Secret
                                            Key:</span>
                                        {{ $item['secret_key'] }}
                                    </td>

                                    <td>{{ $item['balance'] }}</td>
                                    <td style="max-width: 300px;">
                                        <span class="bg-success text-white p-1 d-inline-block mb-2"
                                            style="border-radius: 6px; padding: 7px;">Deposit:</span>
                                        <span class="editable" data-id="{{ $item['id'] }}" data-field="min_deposit">{{
                                            $item['min_deposit'] }}</span><br>

                                        <span class="bg-warning text-white p-2 d-inline-block mt-2 mb-2"
                                            style="border-radius: 10px; padding: 10px;">Withdrawal:</span>
                                        <span class="editable" data-id="{{ $item['id'] }}"
                                            data-field="min_withdrawal">{{ $item['min_withdrawal'] }}</span>
                                    </td>

                                   <td data-label="@lang('Status')" class="text-lg-center text-right">
                                        <label class="switch">
                                            <input type="checkbox" class="toggle-status"
                                                data-id="{{ $item->id }}"
                                                {{ $item->status == 1 ? 'checked' : '' }}>
                                            <span class="slider {{ $item->status == 1 ? 'active' : 'deactive' }}">
                                                {{ $item->status == 1 ? __('Active') : __('Deactive') }}
                                            </span>
                                        </label>
                                    </td>


                                    <td>
                                        @if (adminAccessRoute(config('role.partner_login.access.view')))
                                        <a class="btn btn-sm edit_button"
                                            href="{{ route('admin.apis.login', $item['id']) }}" target="_blank"
                                            data-bs-toggle="tooltip" data-bs-placement="right" title="Partner">
                                            <i class="icon-base ti tabler-login me-1"></i>
                                        </a>

                                        <br>
                                        @endif
                                        @if (adminAccessRoute(config('role.partners.access.delete')))

                                        <button type="button"
                                            class="btn btn-sm delete_api_button edit_button delete-api"
                                            data-id="{{ $item['id'] }}"
                                            data-url="{{ route('admin.apis.delete', $item['id']) }}"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="right"
                                            title="Delete">
                                            <i class="icon-base ti tabler-trash me-1"></i>
                                        </button>
                                                                                @endif
                                        <br>
                                        <button class="btn btn-sm edit_button"
                                            onclick="generateAndCopyPassword({{ $item['id'] }})"
                                            data-bs-toggle="tooltip" data-bs-placement="right" title="Reload">
                                            <i class="icon-base ti tabler-restore me-1"></i>
                                        </button>

                                        <br>
                                        <a class="btn btn-sm edit_button"
                                            data-copy="Username: {{ $item['username'] }}&#10;Password: {{ $item['password_string'] }}&#10;Api Key: {{ $item['api_key'] }}"
                                            onclick="copyToClipboard(this)" data-bs-toggle="tooltip"
                                            data-bs-placement="right" title="Copy">
                                            <i class="icon-base ti tabler-copy-check me-1"></i>
                                        </a>


                                        <br>
                                        <a class="btn btn-sm edit_button"
                                            href="{{ route('admin.api.profile.export', $item['id']) }}"
                                            data-bs-toggle="tooltip" data-bs-placement="right" title="Download EX">
                                            <i class="icon-base ti tabler-database-export me-1"></i>
                                        </a>

                                        <br>

                                        <a class="btn btn-sm"
                                            href="{{ route('admin.apis.reset', $item['id']) }}"
                                            data-bs-toggle="tooltip" data-bs-placement="right" title="QR Code">
                                            <i class="icon-base ti tabler-qrcode me-1"></i>
                                        </a>
                                        {{-- <form action="{{ route('admin.apis.reset', $item['id']) }}" method="GET">
                                            <button type="submit" class="btn"
                                                data-bs-placement="right" title="QR Code">
                                                <i class="icon-base ti tabler-qrcode me-1"></i>
                                            </button>
                                        </form> --}}
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

    {{-- ye awaly --}}
    @foreach ($records as $item)
    <!-- Edit Modal -->
    <div id="editModal{{ $item['id'] }}" class="modal modal-top fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header modal-colored-header bg-warning">
                    <h5 class="modal-title" id="modalTopTitle">@lang('Edit Record') </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.apis.update', $item['id']) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row justify-content-between align-items-center">
                            <!-- Input fields for editing the record -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Name</label>
                                    <input type="text" class="form-control" name="name" value="{{ $item['name'] }}"
                                        required />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Username</label>
                                    <input type="text" class="form-control" name="username"
                                        value="{{ $item['username'] }}" required />
                                </div>
                            </div>
                            <!-- Add other input fields for editing here -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Email</label>
                                    <input type="text" class="form-control" name="email" value="{{ $item['email'] }}" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Phone</label>
                                    <input type="text" class="form-control" name="phone" value="{{ $item['phone'] }}" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Min Deposit</label>
                                    <input type="number" class="form-control" name="min_deposit"
                                        value="{{ $item['min_deposit'] }}" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Min Withdrawal</label>
                                    <input type="number" class="form-control" name="min_withdrawal"
                                        value="{{ $item['min_withdrawal'] }}" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Account Type</label>
                                    <select class="form-control" name="acc_type" required>
                                        <option value="Partner" {{ $item['acc_type']=='Partner' ? 'selected' : '' }}>
                                            Partner</option>
                                        <option value="Agent" {{ $item['acc_type']=='Agent' ? 'selected' : '' }}>Agent
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Status</label>
                                    <select class="form-control" name="status" required>
                                        <option value="1" {{ $item['status']==1 ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="0" {{ $item['status']==0 ? 'selected' : '' }}>
                                            Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Signature</label>
                                    <select class="form-control" name="sign" required>
                                        <option value="0" {{ $item['sign']==0 ? 'selected' : '' }}>Inactive
                                        </option>
                                        <option value="1" {{ $item['sign']==1 ? 'selected' : '' }}>Active
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Txn Verification</label>
                                    <select class="form-control" name="txn_verification" required>
                                        <option value="0" {{ $item['txn_verification']==0 ? 'selected' : '' }}>
                                            Optional</option>
                                        <option value="1" {{ $item['txn_verification']==1 ? 'selected' : '' }}>
                                            Required</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Password</label>
                                    <input type="text" class="form-control" name="password" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Website</label>
                                    <input type="text" class="form-control" placeholder="http://ecwin.asia"
                                        name="website" value="{{ $item['website'] }}" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">API End-Point Deposit</label>
                                    <input type="text" class="form-control" name="api_endpoint_deposit"
                                        placeholder="http://ecwin.asia/api"
                                        value="{{ $item['api_endpoint_deposit'] }}" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">API End-Point Withdrawal</label>
                                    <input type="text" class="form-control" name="api_endpoint_withdrawal"
                                        placeholder="http://ecwin.asia/api"
                                        value="{{ $item['api_endpoint_withdrawal'] }}" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Redirect URL</label>
                                    <input type="text" class="form-control" name="redirect_url"
                                        placeholder="http://ecwin.asia" value="{{ $item['redirect_url'] }}" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">@lang('Update')</button>
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal"
                            aria-label="Close">@lang('Close')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    {{-- New MODAL --}}
    <div class="modal modal-top fade" id="newModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTopTitle">@lang('Add New API')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.apis.add') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row justify-content-between align-items-center">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Name</label>
                                    <input type="text" class="form-control" name="name" required />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Username</label>
                                    <input type="text" class="form-control" name="username" required />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">E-Mail</label>
                                    <input type="text" class="form-control" name="email" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Phone</label>
                                    <input type="text" class="form-control" name="phone" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Min Deposit</label>
                                    <input type="number" class="form-control" name="min_deposit" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Min Withdrawal</label>
                                    <input type="number" class="form-control" name="min_withdrawal" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Password</label>
                                    <input type="text" class="form-control" name="password" required />
                                    <span class="text-danger error-text password_error"></span>

                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Account Type</label>
                                    <select class="form-control" name="acc_type" required>
                                        <option value="Partner">Partner</option>
                                        <option value="Agent">Agent</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Status</label>
                                    <select class="form-control" name="status" required>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Signature</label>
                                    <select class="form-control" name="sign" required>
                                        <option value="0">Inactive</option>
                                        <option value="1">Active</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Txn Verification</label>
                                    <select class="form-control" name="txn_verification" required>
                                        <option value="0">Optional</option>
                                        <option value="1">Required</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Website</label>
                                    <input type="text" class="form-control" placeholder="http://ecwin.asia"
                                        name="website" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">API End-Point</label>
                                    <input type="text" class="form-control" placeholder="http://ecwin.asia/api"
                                        name="api_endpoint_deposit" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">API End-Point</label>
                                    <input type="text" class="form-control" placeholder="http://ecwin.asia/api"
                                        name="api_endpoint_withdrawal" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Redirect URL</label>
                                    <input type="text" class="form-control" placeholder="http://ecwin.asia"
                                        name="redirect_url" />
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="submitBtn" class="btn btn-primary">@lang('Save')</button>
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal"
                            aria-label="Close">@lang('Close')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- New MODAL End here --}}

    {{-- New Partner MODAL --}}
    <div class="modal modal-top fade" id="newModalByParent" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTopTitle">@lang('Add New')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.apis.addByParent') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row justify-content-between align-items-center">


                            <input type="text" hidden id="parentid" class="form-control" name="parent_id">
                            <input type="text" hidden id="acc_id" class="form-control" name="acc_type">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Name</label>
                                    <input type="text" class="form-control" name="name" required />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Username</label>
                                    <input type="text" class="form-control" name="username" required />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">E-Mail</label>
                                    <input type="text" class="form-control" name="email" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Phone</label>
                                    <input type="text" class="form-control" name="phone" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Password</label>
                                    <input type="text" class="form-control" name="password" required />
                                </div>
                            </div>



                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Website</label>
                                    <input type="text" class="form-control" placeholder="http://ecwin.asia"
                                        name="website" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">API End-Point</label>
                                    <input type="text" class="form-control" placeholder="http://ecwin.asia/api"
                                        name="api_endpoint_deposit" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">API End-Point</label>
                                    <input type="text" class="form-control" placeholder="http://ecwin.asia/api"
                                        name="api_endpoint_withdrawal" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Redirect URL</label>
                                    <input type="text" class="form-control" placeholder="http://ecwin.asia"
                                        name="redirect_url" />
                                </div>
                            </div>


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
    {{-- New Partner End here --}}


    <div class="modal modal-top fade" id="newModalb" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTopTitle">@lang('Add Balance')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.apis.balance.add') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row justify-content-between align-items-center">


                            <input type="text" hidden id="balanceInput" class="form-control" name="partner_id">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Balance</label>
                                    <input type="number" step="0.01" class="form-control" name="amount" required />
                                </div>
                            </div>



                            <!--<div class="col-md-12">-->
                            <!--    <div class="form-group">-->
                            <!--        <label class="pr-3">Adjustment</label>-->

                            <!--    </div>-->
                            <!--</div>-->




                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Type</label>
                                    <select class="form-control" name="adjustment" id="adjustment" required>
                                        <option value="4">Topup</option>
                                        <option value="1">Balance Adjustment</option>
                                        <option value="2">Deposit Adjustment</option>
                                        <option value="3">Withdrawal Adjustment</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <input value="1" type="radio" name="amount_type" id="amount_type1" checked>
                                    <label class="pr-3">(+) Add</label>
                                    <input value="2" type="radio" name="amount_type" id="amount_type2">
                                    <label class="pr-3">(-) Deduct</label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Source</label>
                                    <select class="form-control" name="source" required>
                                        <option value="E-Wallet">E-Wallet</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Bank Transfer">Bank Transfer</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Transactions Id</label>
                                    <input type="text" class="form-control" name="txn" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Remarks</label>
                                    <textarea name="reason" class="form-control"></textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">@lang('Add')</button>
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal"
                            aria-label="Close">@lang('Close')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    @push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>

        $(document).on('click', '.delete_api_button', function(e) {
            e.preventDefault();
            var roleId = $(this).data('id');
            var url = $(this).data('url');
            // SweetAlert2 confirmation dialog
            Swal.fire({
                 title: `Are you sure you want to delete ID: ${roleId}?`,
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url, // Your delete route
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: roleId
                        },
                        success: function(response) {
                            // Handle success
                            Swal.fire({
                                title: 'Deleted!',
                                text: response.message || `ID ${roleId} was deleted successfully.`,
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true,
                                willClose: () => {
                                    window.location.reload();
                                }
                            });

                        },
                        error: function(xhr, status, error) {
                            // Handle error
                            Swal.fire(
                                'Error!',
                                'There was an error deleting the role.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    
        $(document).on('change', '.toggle-status', function () {
            const checkbox = $(this);
            const apiId = checkbox.data('id');
            const status = checkbox.is(':checked') ? 1 : 0;

            $.ajax({
                url: "{{ route('admin.apis.toggleStatus') }}",  // Laravel route name used here
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: apiId,
                    status: status
                },
                success: function (response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: response.message || 'Status updated successfully.',
                            showConfirmButton: false,
                            timer: 1500
                        });

                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        Swal.fire('Error!', response.message || 'Update failed.', 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error!', 'Something went wrong.', 'error');
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
                let currentlyEditing = null;

                document.querySelectorAll('.editable').forEach(function(span) {
                    span.addEventListener('click', function() {
                        if (currentlyEditing) return; // Only one field at a time

                        currentlyEditing = this;
                        const currentText = this.textContent.trim();
                        const input = document.createElement('input');
                        input.type = 'text';
                        input.value = currentText;
                        input.classList.add('form-control', 'form-control-sm');

                        this.textContent = '';
                        this.appendChild(input);
                        input.focus();

                        input.addEventListener('blur', function() {
                            const newValue = this.value.trim();
                            const id = span.dataset.id;
                            const field = span.dataset.field;

                            // Send AJAX update
                            fetch(`{{ route('admin.apis.inlineUpdate') }}`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({
                                        id: id,
                                        field: field,
                                        value: newValue
                                    })
                                }).then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        span.textContent = newValue;
                                    } else {
                                        alert('Update failed');
                                        span.textContent = currentText;
                                    }
                                    currentlyEditing = null;
                                }).catch(err => {
                                    console.error(err);
                                    alert('Something went wrong');
                                    span.textContent = currentText;
                                    currentlyEditing = null;
                                });
                        });
                    });
                });
            });

        function generateAndCopyPassword(id) {
                const url = `{{ route('admin.apis.generatePassword', ':id') }}`.replace(':id', id);

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.password) {
                            navigator.clipboard.writeText(data.password)
                                .then(() => alert("New password generated and copied to clipboard: " + data.password))
                                .catch(() => alert("Failed to copy to clipboard."));
                        } else {
                            alert("Failed to generate password.");
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert("Something went wrong.");
                    });
            }

        function copyToClipboard(element) {
                const text = element.getAttribute('data-copy');
                navigator.clipboard.writeText(text).then(function() {
                    alert('Copied to clipboard!');
                }, function(err) {
                    alert('Failed to copy text: ', err);
                });
            }

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

         document.addEventListener('DOMContentLoaded', function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                    new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });
    </script>

    <script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
    <script>
        "use strict";
            $(document).ready(function() {

                $('form').on('submit', function (e) {
                    e.preventDefault();

                    let $form = $(this);
                    let submitBtn = $('#submitBtn');

                    // Disable the button
                    submitBtn.prop('disabled', true).text('Saving...');

                    // Clear previous errors
                    $form.find('span.error-text').text('');

                    $.ajax({
                        url: $form.attr('action'),
                        method: $form.attr('method'),
                        data: $form.serialize(),
                        success: function (response) {
                            if (response.status === 'success') {
                                $('#newModal').modal('hide');
                                $form[0].reset();

                            }
                        },
                        error: function (xhr) {
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                $.each(errors, function (key, value) {
                                    $form.find('span.' + key + '_error').text(value[0]);
                                });
                            } else {
                                alert('Something went wrong.');
                            }
                        },
                        complete: function () {
                            // Enable the button again
                            submitBtn.prop('disabled', false).text('@lang("Save")');
                        }
                    });
                });

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
                 let $select = $('.select2').select2({
                    // placeholder: "Select Partner",
                    allowClear: true,
                    selectOnClose: true,
                });

                // Prevent dropdown from opening on clear
                $select.on('select2:unselecting', function (e) {
                    $(this).data('unselecting', true);
                });

                $select.on('select2:opening', function (e) {
                    if ($(this).data('unselecting')) {
                        $(this).removeData('unselecting');
                        e.preventDefault();
                    }
                });
            });
    </script>

    @endpush
</x-admin-layout>
