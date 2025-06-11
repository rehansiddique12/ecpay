<x-admin-layout :title="$pageTitle">

    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
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
                        {{ __('merchant.add_new') }}
                    </button>
                    <div class="d-flex justify-content-end mb-3">
                        <label class="form-check-label me-2" for="showAllToggle">{{ __('merchant.show_all') }}</label>
                        <input type="checkbox" id="showAllToggle" {{ $showAll == '1' ? 'checked' : '' }}>
                    </div>



                    {{-- @endif --}}

                    <div class="table-responsive ">
                        <table
                            class="categories-show-table table table-hover table-striped table-bordered settable table-responsive table-sm">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">{{ __('merchant.id') }}</th>
                                    <th scope="col">{{ __('merchant.name') }}</th>
                                    <th scope="col">{{ __('merchant.username') }}</th>
                                    <th scope="col">{{ __('merchant.website') }}</th>
                                    <th class="setcolumn" scope="col">{{ __('merchant.api_endpoint') }}</th>
                                    <th class="setcolumn" scope="col">{{ __('merchant.keys') }}</th>
                                    <th scope="col">{{ __('merchant.balance') }}</th>
                                    <th scope="col">{{ __('merchant.min') }}</th>
                                    <th scope="col">{{ __('merchant.status') }}</th>
                                    <th>{{ __('merchant.action') }}</th>
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
                                        <td style="max-width: 130px;"><span class="editable"
                                                data-id="{{ $item['id'] }}"
                                                data-field="website">{{ $item['website'] }}</span></td>
                                        <td style="max-width: 220px;">
                                            <span class="bg-success text-white p-1 d-inline-block mb-2"
                                                style="border-radius: 8px; padding: 7px;">{{ __('merchant.deposit') }}:</span>
                                            {{ $item['api_endpoint_deposit'] }}<br>

                                            <span class="bg-warning text-white  d-inline-block mt-2 mb-2"
                                                style="border-radius: 10px; padding: 7px;">{{ __('merchant.withdrawal') }}:</span>
                                            {{ $item['api_endpoint_withdrawal'] }}<br>

                                            <span class="bg-info text-white  d-inline-block mt-2"
                                                style="border-radius: 10px; padding: 7px;">{{ __('merchant.redirect_url') }}
                                                URL:</span>
                                            {{ $item['redirect_url'] }}<br>
                                        </td>

                                        <td style="max-width: 220px;">
                                            <span class="bg-success text-white p-1 d-inline-block mb-2"
                                                style="border-radius: 6px; padding: 7px;">{{ __('merchant.api_key') }}:</span>
                                            <span class="editable" data-id="{{ $item['id'] }}"
                                                data-field="api_key">{{ $item['api_key'] }}</span>
                                            <br>

                                            <span class="bg-primary text-white p-1 d-inline-block mt-2 mb-2"
                                                style="border-radius: 8px; padding: 7px;">{{ __('merchant.secret_key') }}:</span>
                                            {{ $item['secret_key'] }}
                                        </td>

                                        <td>{{ $item['balance'] }}</td>
                                        <td style="max-width: 300px;">
                                            <span class="bg-success text-white p-1 d-inline-block mb-2"
                                                style="border-radius: 6px; padding: 7px;">{{ __('merchant.deposit') }}:</span>
                                            <span class="editable" data-id="{{ $item['id'] }}"
                                                data-field="min_deposit">{{ $item['min_deposit'] }}</span><br>

                                            <span class="bg-warning text-white p-2 d-inline-block mt-2 mb-2"
                                                style="border-radius: 10px; padding: 10px;">{{ __('merchant.withdrawal') }}:</span>
                                            <span class="editable" data-id="{{ $item['id'] }}"
                                                data-field="min_withdrawal">{{ $item['min_withdrawal'] }}</span>
                                        </td>

                                        <td data-label="{{ __('merchant.status') }}" class="text-lg-center text-right">
                                            {{-- Flex container for Status --}}
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span>{{ __('merchant.status') }}&nbsp;</span>
                                                <label class="switch mb-0">
                                                    <input type="checkbox" class="toggle-switch"
                                                        data-id="{{ $item->id }}" data-type="status"
                                                        {{ $item->status == 1 ? 'checked' : '' }}>
                                                    <span
                                                        class="slider {{ $item->status == 1 ? 'active' : 'deactive' }}">
                                                        {{ $item->status == 1 ? __('merchant.active') : __('merchant.deactive') }}
                                                    </span>
                                                </label>
                                            </div>

                                            {{-- Flex container for Sign --}}
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span>{{ __('merchant.sign') }}</span>
                                                <label class="switch mb-0">
                                                    <input type="checkbox" class="toggle-switch"
                                                        data-id="{{ $item->id }}" data-type="sign"
                                                        {{ $item->sign == 1 ? 'checked' : '' }}>
                                                    <span
                                                        class="slider {{ $item->sign == 1 ? 'active' : 'deactive' }}">
                                                        {{ $item->sign == 1 ? __('merchant.active') : __('merchant.inactive') }}
                                                    </span>
                                                </label>
                                            </div>

                                            {{-- Flex container for Transaction Verification --}}
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>{{ __('merchant.txn') }}</span>
                                                <label class="switch mb-0">
                                                    <input type="checkbox" class="toggle-switch"
                                                        data-id="{{ $item->id }}" data-type="txn_verification"
                                                        {{ $item->txn_verification == 1 ? 'checked' : '' }}>
                                                    <span
                                                        class="slider {{ $item->txn_verification == 1 ? 'active' : 'deactive' }}">
                                                        {{ $item->txn_verification == 1 ? __('merchant.required') : __('merchant.optional') }}
                                                    </span>
                                                </label>
                                            </div>
                                        </td>



                                        <td>
                                            @if (adminAccessRoute(config('role.partner_login.access.view')))
                                                <a class="btn btn-sm edit_button"
                                                    href="{{ route('admin.apis.login', $item['id']) }}" target="_blank"
                                                    data-bs-toggle="tooltip" data-bs-placement="right"
                                                    title="{{ __('merchant.partner') }}">
                                                    <i class="icon-base ti tabler-login me-1"></i>
                                                </a>

                                                <br>
                                            @endif
                                            @if (adminAccessRoute(config('role.partners.access.delete')))
                                                <button type="button"
                                                    class="btn btn-sm delete_api_button edit_button delete-api"
                                                    data-id="{{ $item['id'] }}"
                                                    data-url="{{ route('admin.apis.delete', $item['id']) }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="right"
                                                    title="{{ __('merchant.delete') }}">
                                                    <i class="icon-base ti tabler-trash me-1"></i>
                                                </button>
                                            @endif
                                            <br>
                                            <button class="btn btn-sm edit_button"
                                                onclick="generateAndCopyPassword({{ $item['id'] }})"
                                                data-bs-toggle="tooltip" data-bs-placement="right"
                                                title="{{ __('merchant.reload') }}">
                                                <i class="icon-base ti tabler-restore me-1"></i>
                                            </button>

                                            <br>
                                            <a class="btn btn-sm edit_button"
                                                data-copy="Username: {{ $item['username'] }}&#10;Password: {{ $item['password_string'] }}&#10;Api Key: {{ $item['api_key'] }}"
                                                onclick="copyToClipboard(this)" data-bs-toggle="tooltip"
                                                data-bs-placement="right" title="{{ __('merchant.copy') }}">
                                                <i class="icon-base ti tabler-copy-check me-1"></i>
                                            </a>


                                            <br>
                                            <a class="btn btn-sm edit_button"
                                                href="{{ route('admin.api.profile.export', $item['id']) }}"
                                                data-bs-toggle="tooltip" data-bs-placement="right"
                                                title="{{ __('merchant.download_ex') }}">
                                                <i class="icon-base ti tabler-database-export me-1"></i>
                                            </a>

                                            <br>

                                            <a class="btn btn-sm" href="{{ route('admin.apis.reset', $item['id']) }}"
                                                data-bs-toggle="tooltip" data-bs-placement="right"
                                                title="{{ __('merchant.qr_code') }}">
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
                                            <p class="text-dark">{{ __('merchant.no_data_found') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        @if ($records instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            {{ $records->appends($_GET)->links('partials.pagination') }}
                        @endif
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
                        <h5 class="modal-title" id="modalTopTitle">{{ __('merchant.edit_record') }}</h5>
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
                                        <label class="pr-3">{{ __('merchant.name') }}</label>
                                        <input type="text" class="form-control" name="name"
                                            value="{{ $item['name'] }}" required />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="pr-3">{{ __('merchant.username') }}</label>
                                        <input type="text" class="form-control" name="username"
                                            value="{{ $item['username'] }}" required />
                                    </div>
                                </div>
                                <!-- Add other input fields for editing here -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="pr-3">{{ __('merchant.email') }}</label>
                                        <input type="text" class="form-control" name="email"
                                            value="{{ $item['email'] }}" />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="pr-3">{{ __('merchant.phone') }}</label>
                                        <input type="text" class="form-control" name="phone"
                                            value="{{ $item['phone'] }}" />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="pr-3">{{ __('merchant.min_deposit') }}</label>
                                        <input type="number" class="form-control" name="min_deposit"
                                            value="{{ $item['min_deposit'] }}" />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="pr-3">{{ __('merchant.min_withdrawal') }}</label>
                                        <input type="number" class="form-control" name="min_withdrawal"
                                            value="{{ $item['min_withdrawal'] }}" />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="pr-3">{{ __('merchant.account_type') }}</label>
                                        <select class="form-control" name="acc_type" required>
                                            <option value="Partner"
                                                {{ $item['acc_type'] == 'Partner' ? 'selected' : '' }}>
                                                {{ __('merchant.partner') }}</option>
                                            <option value="Agent"
                                                {{ $item['acc_type'] == 'Agent' ? 'selected' : '' }}>
                                                {{ __('merchant.agent') }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="pr-3">{{ __('merchant.status') }}</label>
                                        <select class="form-control" name="status" required>
                                            <option value="1" {{ $item['status'] == 1 ? 'selected' : '' }}>
                                                {{ __('merchant.active') }}
                                            </option>
                                            <option value="0" {{ $item['status'] == 0 ? 'selected' : '' }}>
                                                {{ __('merchant.inactive') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="pr-3">{{ __('merchant.signature') }}</label>
                                        <select class="form-control" name="sign" required>
                                            <option value="0" {{ $item['sign'] == 0 ? 'selected' : '' }}>
                                                {{ __('merchant.inactive') }}
                                            </option>
                                            <option value="1" {{ $item['sign'] == 1 ? 'selected' : '' }}>
                                                {{ __('merchant.active') }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="pr-3">{{ __('merchant.txn_verification') }}</label>
                                        <select class="form-control" name="txn_verification" required>
                                            <option value="0"
                                                {{ $item['txn_verification'] == 0 ? 'selected' : '' }}>
                                                {{ __('merchant.optional') }}</option>
                                            <option value="1"
                                                {{ $item['txn_verification'] == 1 ? 'selected' : '' }}>
                                                {{ __('merchant.required') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="pr-3">{{ __('merchant.password') }}</label>
                                        <input type="text" class="form-control" name="password" />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="pr-3">{{ __('merchant.website') }}</label>
                                        <input type="text" class="form-control" placeholder="http://ecwin.asia"
                                            name="website" value="{{ $item['website'] }}" />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="pr-3">{{ __('merchant.api_endpoint_deposit') }}</label>
                                        <input type="text" class="form-control" name="api_endpoint_deposit"
                                            placeholder="http://ecwin.asia/api"
                                            value="{{ $item['api_endpoint_deposit'] }}" />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="pr-3">{{ __('merchant.api_endpoint_withdrawal') }}</label>
                                        <input type="text" class="form-control" name="api_endpoint_withdrawal"
                                            placeholder="http://ecwin.asia/api"
                                            value="{{ $item['api_endpoint_withdrawal'] }}" />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="pr-3">{{ __('merchant.redirect_url') }}</label>
                                        <input type="text" class="form-control" name="redirect_url"
                                            placeholder="http://ecwin.asia" value="{{ $item['redirect_url'] }}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">{{ __('merchant.update') }}</button>
                            <button type="button" class="btn btn-dark" data-bs-dismiss="modal"
                                aria-label="Close">{{ __('merchant.close') }}</button>
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
                    <h5 class="modal-title" id="modalTopTitle">{{ __('merchant.add_new_api') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.apis.add') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row justify-content-between align-items-center">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('merchant.name') }}</label>
                                    <input type="text" class="form-control" name="name" required />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('merchant.username') }}</label>
                                    <input type="text" class="form-control" name="username" required />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('merchant.email') }}</label>
                                    <input type="text" class="form-control" name="email" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('merchant.phone') }}</label>
                                    <input type="text" class="form-control" name="phone" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('merchant.min_deposit') }}</label>
                                    <input type="number" class="form-control" name="min_deposit" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('merchant.min_withdrawal') }}</label>
                                    <input type="number" class="form-control" name="min_withdrawal" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('merchant.password') }}</label>
                                    <input type="text" class="form-control" name="password" required />
                                    <span class="text-danger error-text password_error"></span>

                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('merchant.acc_type') }}</label>
                                    <select class="form-control" name="acc_type" required>
                                        <option value="Partner">{{ __('merchant.partner') }}</option>
                                        <option value="Agent">{{ __('merchant.agent') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('merchant.status') }}</label>
                                    <select class="form-control" name="status" required>
                                        <option value="1">{{ __('merchant.active') }}</option>
                                        <option value="0">{{ __('merchant.inactive') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('merchant.sign') }}</label>
                                    <select class="form-control" name="sign" required>
                                        <option value="0">{{ __('merchant.inactive') }}</option>
                                        <option value="1">{{ __('merchant.active') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('merchant.txn_verification') }}</label>
                                    <select class="form-control" name="txn_verification" required>
                                        <option value="0">{{ __('merchant.optional') }}</option>
                                        <option value="1" selected>{{ __('merchant.required') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('merchant.website') }}</label>
                                    <input type="text" class="form-control" placeholder="http://ecwin.asia"
                                        name="website" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('merchant.api_endpoint_deposit') }}</label>
                                    <input type="text" class="form-control" placeholder="http://ecwin.asia/api"
                                        name="api_endpoint_deposit" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('merchant.api_endpoint_withdrawal') }}</label>
                                    <input type="text" class="form-control" placeholder="http://ecwin.asia/api"
                                        name="api_endpoint_withdrawal" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('merchant.redirect_url') }}</label>
                                    <input type="text" class="form-control" placeholder="http://ecwin.asia"
                                        name="redirect_url" />
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="submitBtn"
                            class="btn btn-primary">{{ __('merchant.save') }}</button>
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal"
                            aria-label="Close">{{ __('merchant.close') }}</button>
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
                    <h5 class="modal-title" id="modalTopTitle">{{ __('merchant.add_new') }}</h5>
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
                                    <label class="pr-3">{{ __('merchant.name') }}</label>
                                    <input type="text" class="form-control" name="name" required />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('merchant.username') }}</label>
                                    <input type="text" class="form-control" name="username" required />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('merchant.email') }}</label>
                                    <input type="text" class="form-control" name="email" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('merchant.phone') }}</label>
                                    <input type="text" class="form-control" name="phone" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('merchant.password') }}</label>
                                    <input type="text" class="form-control" name="password" required />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('merchant.website') }}</label>
                                    <input type="text" class="form-control" placeholder="http://ecwin.asia"
                                        name="website" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('merchant.api_endpoint_deposit') }}</label>
                                    <input type="text" class="form-control" placeholder="http://ecwin.asia/api"
                                        name="api_endpoint_deposit" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('merchant.api_endpoint_withdrawal') }}</label>
                                    <input type="text" class="form-control" placeholder="http://ecwin.asia/api"
                                        name="api_endpoint_withdrawal" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('merchant.redirect_url') }}</label>
                                    <input type="text" class="form-control" placeholder="http://ecwin.asia"
                                        name="redirect_url" />
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">{{ __('merchant.save') }}</button>
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal"
                            aria-label="Close">{{ __('merchant.close') }}</button>
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
                    <h5 class="modal-title" id="modalTopTitle">{{ __('merchant.add_balance') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.apis.balance.add') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row justify-content-between align-items-center">


                            <input type="text" hidden id="balanceInput" class="form-control" name="partner_id">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('merchant.balance') }}</label>
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
                                    <label class="pr-3">{{ __('merchant.type') }}</label>
                                    <select class="form-control" name="adjustment" id="adjustment" required>
                                        <option value="4">{{ __('merchant.topup') }}</option>
                                        <option value="1">{{ __('merchant.balance_adjustment') }}</option>
                                        <option value="2">{{ __('merchant.deposit_adjustment') }}</option>
                                        <option value="3">{{ __('merchant.withdrawal_adjustment') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <input value="1" type="radio" name="amount_type" id="amount_type1"
                                        checked>
                                    <label class="pr-3">{{ __('merchant.add') }}</label>
                                    <input value="2" type="radio" name="amount_type" id="amount_type2">
                                    <label class="pr-3">{{ __('merchant.deduct') }}</label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('merchant.source') }}</label>
                                    <select class="form-control" name="source" required>
                                        <option value="E-Wallet">{{ __('merchant.ewallet') }}</option>
                                        <option value="Cash">{{ __('merchant.cash') }}</option>
                                        <option value="Bank Transfer">{{ __('merchant.bank_transfer') }}</option>
                                        <option value="Other">{{ __('merchant.other') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('merchant.transaction_id') }}</label>
                                    <input type="text" class="form-control" name="txn" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('merchant.remarks') }}</label>
                                    <textarea name="reason" class="form-control"></textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">{{ __('merchant.add') }}</button>
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal"
                            aria-label="Close">{{ __('merchant.close') }}</button>
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
                    title: "{{ __('merchant.delete_confirm_title') }}".replace(':id', roleId),
                    text: "{!! __('merchant.delete_confirm_text') !!}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ __('merchant.delete_confirm_button') }}"
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
                                    text: response.message ||
                                        "{{ __('merchant.delete_success_message') }}"
                                        .replace(':id', roleId),
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
                                    "{{ __('merchant.delete_error_title') }}",
                                    "{{ __('merchant.delete_error_message') }}",
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

            $(document).on('change', '.toggle-switch', function() {
                const checkbox = $(this);
                const apiId = checkbox.data('id');
                const type = checkbox.data('type'); // 'status', 'sign', or 'txn_verification'
                const value = checkbox.is(':checked') ? 1 : 0;

                $.ajax({
                    url: "{{ route('admin.apis.toggleStatus') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: apiId,
                        type: type,
                        value: value
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: "{{ __('merchant.toggle_success_title') }}",
                                text: response.message ||
                                    "{{ __('merchant.toggle_success_message') }}",
                                showConfirmButton: false,
                                timer: 1500
                            });

                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            Swal.fire("{{ __('merchant.toggle_error_title') }}", response.message ||
                                "{{ __('merchant.inline_update_failed') }}", 'error');
                        }
                    },
                    error: function() {
                        Swal.fire("{{ __('merchant.toggle_error_title') }}",
                            "{{ __('merchant.toggle_generic_error') }}", 'error');
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
                                        alert(
                                            "{{ __('merchant.inline_update_failed') }}"
                                        );
                                        span.textContent = currentText;
                                    }
                                    currentlyEditing = null;
                                }).catch(err => {
                                    console.error(err);
                                    alert("{{ __('merchant.toggle_generic_error') }}");
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
                                .then(() => alert("{{ __('merchant.password_copy_success') }}".replace(':password', data
                                    .password)))
                                .catch(() => alert("{{ __('merchant.password_copy_failed') }}"));
                        } else {
                            alert("{{ __('merchant.password_generate_failed') }}");
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert("{{ __('merchant.toggle_generic_error') }}");
                    });
            }

            function copyToClipboard(element) {
                const text = element.getAttribute('data-copy');
                navigator.clipboard.writeText(text).then(function() {
                    alert("{{ __('merchant.clipboard_copy_success') }}");
                }, function(err) {
                    alert("{{ __('merchant.clipboard_copy_failed') }}");
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

        <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
        <script>
            "use strict";
            $(document).ready(function() {

                $('form').on('submit', function(e) {
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
                        success: function(response) {
                            if (response.status === 'success') {
                                $('#newModal').modal('hide');
                                $form[0].reset();
                                window.location.reload();
                            }

                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, value) {
                                    $form.find('span.' + key + '_error').text(value[0]);
                                });
                            } else {
                                alert("{{ __('merchant.toggle_generic_error') }}");
                            }
                        },
                        complete: function() {
                            // Enable the button again
                            submitBtn.prop('disabled', false).text("{{ __('merchant.save') }}");
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
                $select.on('select2:unselecting', function(e) {
                    $(this).data('unselecting', true);
                });

                $select.on('select2:opening', function(e) {
                    if ($(this).data('unselecting')) {
                        $(this).removeData('unselecting');
                        e.preventDefault();
                    }
                });
            });
        </script>
        <script>
            document.getElementById('showAllToggle').addEventListener('change', function() {
                const showAll = this.checked ? 1 : 0;
                const url = new URL(window.location.href);
                url.searchParams.set('show_all', showAll);
                window.location.href = url.toString();
            });
        </script>
    @endpush
</x-admin-layout>
