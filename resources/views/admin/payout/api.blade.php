<x-admin-layout :title="$pageTitle">

    @push('styles')
        <script src="{{ asset('public/assets/css/select2.min.css') }}"></script>
        <style>
            @media (min-width: 1400px) {

                .container-xxl,
                .container-xl,
                .container-lg,
                .container-md,
                .container-sm,
                .container {
                    max-width: 1770px;
                }
            }
        </style>

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
                    <button type="button" class="btn btn-primary mb-5" data-bs-toggle="modal" data-bs-target="#newModal">
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
                                                href="{{ route('admin.merchant.profile', $item->id) }}">{{ $item['name'] }}</a>
                                        </td>
                                        <td style="max-width: 100px;">{{ $item['username'] }}</td>
                                        <td style="max-width: 130px;">{{ $item['website'] }}</td>
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
                                            {{ $item['api_key'] }}<br>

                                            <span class="bg-primary text-white p-1 d-inline-block mt-2 mb-2"
                                                style="border-radius: 8px; padding: 7px;">Secret
                                                Key:</span>
                                            {{ $item['secret_key'] }}
                                        </td>

                                        <td>{{ $item['balance'] }}</td>
                                        <td style="max-width: 300px;">
                                            <span class="bg-success text-white p-1 d-inline-block mb-2"
                                                style="border-radius: 6px; padding: 7px;">Deposit:</span>
                                            <span class="editable" data-id="{{ $item['id'] }}"
                                                data-field="min_deposit">{{ $item['min_deposit'] }}</span><br>

                                            <span class="bg-warning text-white p-2 d-inline-block mt-2 mb-2"
                                                style="border-radius: 10px; padding: 10px;">Withdrawal:</span>
                                            <span class="editable" data-id="{{ $item['id'] }}"
                                                data-field="min_withdrawal">{{ $item['min_withdrawal'] }}</span>
                                        </td>

                                        <td data-label="@lang('Status')" class="text-lg-center text-right">
                                            <label class="switch">
                                                <input type="checkbox" {{ $item->status == 1 ? 'checked' : '' }}
                                                    disabled>
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
                                                <form action="{{ route('admin.apis.delete', $item['id']) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="btn btn-sm edit_button"
                                                        data-bs-toggle="tooltip" data-bs-placement="right"
                                                        title="Delete">
                                                        <i class="icon-base ti tabler-trash me-1"></i>
                                                    </button>

                                                </form>
                                            @endif
                                            <button class="btn btn-sm edit_button"
                                                onclick="generateAndCopyPassword({{ $item['id'] }})"
                                                data-bs-toggle="tooltip" data-bs-placement="right" title="Relode">
                                                <i class="icon-base ti tabler-restore me-1"></i>
                                            </button>

                                            <br>
                                            <a class="btn btn-sm edit_button"
                                                data-copy="{{ $item['username'] }} | {{ $item['api_key'] }} | {{ $item['secret_key'] }}"
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


                                            <form action="{{ route('admin.apis.reset', $item['id']) }}" method="GET">
                                                <button type="submit" class="btn edit_button" data-bs-toggle="tooltip"
                                                    data-bs-placement="right" title="QR Code">
                                                    <i class="icon-base ti tabler-qrcode me-1"></i>
                                                </button>

                                            </form>
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

    {{-- ye awaly  --}}
    @foreach ($records as $item)
        <!-- Edit Modal -->
        <div id="editModal{{ $item['id'] }}" class="modal modal-top fade" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header modal-colored-header bg-warning">
                        <h5 class="modal-title" id="modalTopTitle">@lang('Edit Record') </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
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
                                        <input type="text" class="form-control" name="name"
                                            value="{{ $item['name'] }}" required />
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
                                        <input type="text" class="form-control" name="email"
                                            value="{{ $item['email'] }}" />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="pr-3">Phone</label>
                                        <input type="text" class="form-control" name="phone"
                                            value="{{ $item['phone'] }}" />
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
                                            <option value="Partner"
                                                {{ $item['acc_type'] == 'Partner' ? 'selected' : '' }}>
                                                Partner</option>
                                            <option value="Agent"
                                                {{ $item['acc_type'] == 'Agent' ? 'selected' : '' }}>Agent
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="pr-3">Status</label>
                                        <select class="form-control" name="status" required>
                                            <option value="1" {{ $item['status'] == 1 ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="0" {{ $item['status'] == 0 ? 'selected' : '' }}>
                                                Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="pr-3">Signature</label>
                                        <select class="form-control" name="sign" required>
                                            <option value="0" {{ $item['sign'] == 0 ? 'selected' : '' }}>Inactive
                                            </option>
                                            <option value="1" {{ $item['sign'] == 1 ? 'selected' : '' }}>Active
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="pr-3">Txn Verification</label>
                                        <select class="form-control" name="txn_verification" required>
                                            <option value="0"
                                                {{ $item['txn_verification'] == 0 ? 'selected' : '' }}>
                                                Optional</option>
                                            <option value="1"
                                                {{ $item['txn_verification'] == 1 ? 'selected' : '' }}>
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
    <div class="modal modal-top fade" id="newModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header bg-primary">
                    <h5 class="modal-title" id="modalTopTitle">@lang('Add New')</h5>
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
                        <button type="submit" class="btn btn-primary">@lang('Save')</button>
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
                                    <input type="number" step="0.01" class="form-control" name="amount"
                                        required />
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
                                    <input value="1" type="radio" name="amount_type" id="amount_type1"
                                        checked>
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
        <script src="{{ asset('public/assets/js/select2.min.js') }}"></script>
        <script>
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
        </script>

        <script>
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
        </script>


        <script>
            function copyToClipboard(element) {
                const text = element.getAttribute('data-copy');
                navigator.clipboard.writeText(text).then(function() {
                    alert('Copied to clipboard!');
                }, function(err) {
                    alert('Failed to copy text: ', err);
                });
            }
        </script>

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

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                    new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });
        </script>
    @endpush
</x-admin-layout>
