<x-admin-layout :title="$pageTitle">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
    @endpush

    @php
        $today = \Carbon\Carbon::today()->toDateString();
        $yesterday = \Carbon\Carbon::yesterday()->toDateString();
        $last7 = \Carbon\Carbon::today()->subDays(6)->toDateString();
    @endphp


    <style>
        .hover:hover {
            background-color: #ffc000;
            color: white;
        }

        .btn-yellow.active {
            background-color: #ffc000 !important;
            color: white !important;
            border: 2px solid #e0a800;
        }
    </style>
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <div class="d-flex d-lg-flex d-md-block align-items-center">
            <h4 class="mb-10 text-primary font-weight-medium ">{{ __('transaction.withdraw') }}</h4>
            <div class="ml-20 d-flex gap-5 mb-10" style="margin-left: 30px;">
                <button type="button"
                    class="btn btn-yellow btn-date-filter {{ request('from_date') == $today && request('to_date') == $today ? 'active' : '' }}"
                    id="btn-today">{{ __('transaction.today') }}</button>

                <button type="button"
                    class="btn btn-yellow btn-date-filter {{ request('from_date') == $yesterday && request('to_date') == $yesterday ? 'active' : '' }}"
                    id="btn-yesterday">{{ __('transaction.yesterday') }}</button>

                <button type="button"
                    class="btn btn-yellow btn-date-filter {{ request('from_date') == $last7 && request('to_date') == $today ? 'active' : '' }}"
                    id="btn-last7">{{ __('transaction.last_7_days') }}</button>
            </div>
        </div>
        <form id="filterForm" action="{{ route('admin.payout-report.search') }}" method="get">
            <div class="row justify-content-between align-items-center">
                <div class="col-md-3">
                    <div class="form-group mt-3">
                        <label>{{ __('transaction.user') }}</label>
                        <input type="text" name="name" value="{{ @request()->name }}" class="form-control"
                            placeholder="{{ __('transaction.email_or_username') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mt-3">
                        <label>{{ __('transaction.from_date') }}</label>
                        <input type="date" class="form-control" value="{{ @request()->from_date }}" name="from_date"
                            id="from_date" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mt-3">
                        <label>{{ __('transaction.to_date') }}</label>
                        <input type="date" class="form-control" value="{{ @request()->to_date }}" name="to_date"
                            id="to_date" />
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group mt-3">
                        <label>{{ __('transaction.transaction_no') }}</label>
                        <input type="text" name="partner_transection_id"
                            value="{{ @request()->partner_transection_id }}" class="form-control"
                            placeholder="{{ __('transaction.transaction_no') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group mt-3">
                        <label>{{ __('transaction.user_account_no') }}</label>
                        <input type="text" class="form-control" value="{{ @request()->account_no }}"
                            name="account_no" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mt-3">
                        <label>{{ __('transaction.e_wallet') }}</label>
                        <select name="gateway" class="form-select">
                            <option value="">{{ __('transaction.all') }}</option>
                            @foreach ($gateways as $gateway)
                                <option value="{{ $gateway->name }}" @if (@request()->gateway == $gateway->name) selected @endif>
                                    {{ $gateway->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group mt-3">
                        <label>{{ __('transaction.status') }}</label>
                        <select name="status" class="form-select">
                            <option value="">{{ __('transaction.all_payment') }}</option>
                            <option value="1" @if (@request()->status == '1') selected @endif>
                                {{ __('transaction.pending_payment') }}
                            </option>
                            <option value="2" @if (@request()->status == '2') selected @endif>
                                {{ __('transaction.complete_payment') }}
                            </option>
                            <option value="3" @if (@request()->status == '3') selected @endif>
                                {{ __('transaction.cancel_payment') }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group mt-3">
                        <label for="">{{ __('transaction.domain') }}</label>
                        <select name="domain" class="form-select select2" data-allow-clear="true"
                            data-placeholder="{{ __('transaction.select_domain') }}">
                            <option></option>
                            <option value="">{{ __('transaction.select_domain') }}</option>
                            @foreach ($domains as $domain)
                                <option value="{{ $domain->id }}" @if (@request()->domain == $domain->id) selected @endif>
                                    {{ $domain->name }} ===> ( {{ $domain->website }} )</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-12 d-flex justify-content-end align-items-center gap-6">
                    <div class="form-group mt-2">
                        <button type="submit" class="btn  btn-primary mt-2"><i
                                class="icon-base ti tabler-search me-1"></i> {{ __('transaction.search') }}</button>
                    </div>
                    <div class="form-group mt-2">
                        <a href="{{ route('admin.merchant_reports.export_by_logs_for_WithDrawl', ['from_date' => $from_date]) }}"
                            class="btn waves-effect waves-light btn-success" id="exportButton">
                            <i class="icon-base ti tabler-download me-1"></i>{{ __('transaction.export') }}
                        </a>
                    </div>

                </div>

            </div>
        </form>

    </div>
    <div class="row">
        <div class="col-2"></div>
        <div class="col-4">
            <div class="card shadow border-right">
                <div class="card-body">
                    <div class="d-flex d-lg-flex d-md-block align-items-center">
                        <div>
                            <div class="d-inline-flex align-items-center">
                                <h2 class="text-dark mb-1 font-weight-medium">{{ $fund_count }}</h2>
                            </div>
                            <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">
                                {{ __('transaction.total_transactions') }}
                            </h6>
                        </div>
                        <div class="ml-auto mt-md-3 mt-lg-0">
                            <span class="opacity-7 text-muted"><i class="fas fa-wallet"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card shadow border-right">
                <div class="card-body">
                    <div class="d-flex d-lg-flex d-md-block align-items-center">
                        <div>
                            <div class="d-inline-flex align-items-center">
                                <h2 class="text-dark mb-1 font-weight-medium">{{ $fund_sum }}</h2>
                            </div>
                            <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">
                                {{ __('transaction.total_withdrawal_amount') }}</h6>
                        </div>
                        <div class="ml-auto mt-md-3 mt-lg-0">
                            <span class="opacity-7 text-muted"><i class="fa fa-hand-holding-usd"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">@lang('Date')</th>
                            <th scope="col">@lang('Trx Number')</th>
                            <th scope="col">@lang('Partner Trx Number')</th>
                            <th scope="col">@lang('Username')</th>
                            <th scope="col">@lang('User Account')</th>
                            <th scope="col">@lang('Method')</th>
                            <th scope="col">@lang('Amount')</th>
                            <th scope="col">@lang('Merchant Charge')</th>
                            <th scope="col">@lang('Net Amount')</th>
                            <th scope="col">@lang('Status')</th>
                            <th scope="col">@lang('Sent From')</th>
                            <th scope="col">@lang('Source')</th>
                            @if (adminAccessRoute(config('role.payout_manage.access.edit')))
                                <th scope="col">@lang('More')</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $key => $item)
                            <tr>
                                <td data-label="@lang('Date')"> {{ dateTime($item->created_at, 'd M,Y H:i') }}
                                </td>
                                <td data-label="@lang('Trx Number')" class="font-weight-bold text-uppercase">
                                    {{ $item->trx_id }}<br>
                                    <span class="text text-success">{{ $item->txn_id }}</span>

                                </td>
                                <td>{{ $item->partner_transection_id }}
                                    <br>
                                    {{ $item->member_id }}
                                </td>
                                <td data-label="@lang('Username')">
                                    @if (optional($item->user)->username != null && optional($item->user)->username != 'dummyuser')
                                        <a href="{{ route('admin.user-edit', [$item->user_id]) }}">
                                            <div class="d-lg-flex d-block align-items-center ">
                                                <div class="mr-3"><img
                                                        src="{{ getFile(config('location.user.path') . optional($item->user)->image) }}"
                                                        alt="user" class="rounded-circle" width="45"
                                                        height="45"></div>
                                                <div class="">
                                                    <h5 class="text-dark mb-0 font-16 font-weight-medium">
                                                        {{ optional($item->user)->username }}</h5>
                                                    <span
                                                        class="text-muted font-14">{{ optional($item->user)->email }}</span>
                                                </div>
                                            </div>
                                        </a>
                                    @else
                                        @if ($item->api)
                                            {{ optional($item->api)->name }}
                                            <b>({{ optional($item->api)->acc_type }})</b>
                                        @else
                                            Partner Transaction
                                        @endif
                                    @endif

                                </td>
                                <td data-label="@lang('Method')">{{ $item->user_account_no }}</td>
                                <td>{{ $item->e_wallet_name }}</td>
                                <td data-label="@lang('Amount')" class="font-weight-bold">
                                    {{ getAmount($item->amount) }} {{ $basic->currency_symbol }}</td>
                                <td data-label="@lang('Charge')" class="text-success">
                                    {{ getAmount($item->charge, 2) }} {{ $basic->currency_symbol }}</td>

                                <td data-label="@lang('Net Amount')" class="font-weight-bold">
                                    {{ getAmount($item->amount + $item->charge) }}
                                    {{ $basic->currency_symbol }}</td>

                                <td data-label="@lang('Status')" class="d-flex flex-column align-items-center">
                                    @if ($item->transfer_status == 2)
                                        <span class="badge bg-success mb-1"><i
                                                class="fa fa-circle text-white font-12"></i>
                                            @lang('Request Approved')</span>
                                    @elseif($item->transfer_status == 1)
                                        <span class="badge bg-warning mb-1"><i
                                                class="fa fa-circle text-white font-12"></i>
                                            @lang('Request Pending')</span>
                                    @elseif($item->transfer_status == 3)
                                        <span class="badge bg-danger mb-1"><i
                                                class="fa fa-circle text-white font-12"></i>
                                            @lang('Request Rejected')</span>
                                    @endif
                                    @if ($item->status == 'Complete')
                                        <span class="badge bg-success mt-1"><i
                                                class="fa fa-circle text-white font-12"></i>
                                            @lang('Transferred')</span>
                                    @elseif($item->status == 'Pending')
                                        <span class="badge bg-warning mt-1"><i
                                                class="fa fa-circle text-white font-12"></i>
                                            @lang('Transfer Pending')</span>
                                    @elseif($item->status == 'Reject')
                                        <span class="badge bg-danger mt-1"><i
                                                class="fa fa-circle text-white font-12"></i>
                                            @lang('Transfer Rejected')</span>
                                    @endif
                                </td>
                                <td data-label="@lang('Method')">
                                    {{ $item->e_wallet_phone_number }}
                                    <br>
                                    {{ $item->e_wallet_type }}
                                </td>
                                <td data-label="@lang('Method')">{{ $item->e_wallet_name }}</td>


                                @if (adminAccessRoute(config('role.payout_manage.access.edit')))
                                    <td data-label="@lang('More')">
                                        @php
                                            $details =
                                                $item->information != null ? json_encode($item->information) : null;
                                        @endphp
                                        <button type="button" class="btn btn-primary btn-icon edit_button"
                                            data-bs-toggle="modal" data-bs-target="#myModal"
                                            data-route="{{ route('admin.payout-action', $item->id) }}"
                                            data-feedback="{{ $item->feedback }}" data-info="{{ $details }}"
                                            data-id="{{ $item->id }}"
                                            data-status="{{ $item->transfer_status }}">
                                            @if (Request::routeIs('admin.payout-request'))
                                                <i class="icon-base ti tabler-pencil me-1"></i>
                                            @else
                                                <i class="icon-base ti tabler-eye me-1"></i>
                                            @endif
                                        </button>
                                    </td>
                                @endif

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
                <div class="mt-5">
                    {{ $records->appends($_GET)->links('partials.pagination') }}
                </div>
            </div>
        </div>
    </div> --}}


    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">

            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">{{ __('transaction.id') }}</th>
                            <th scope="col">{{ __('transaction.date') }}</th>
                            <th scope="col">{{ __('transaction.trx_number') }}</th>
                            <th scope="col">{{ __('transaction.partner_trx_number') }}</th>
                            <th scope="col">{{ __('transaction.username') }}</th>
                            <th scope="col">{{ __('transaction.method') }}</th>
                            <th scope="col">{{ __('transaction.acc_no') }}</th>
                            <th scope="col">{{ __('transaction.amount') }}</th>
                            <th scope="col">{{ __('transaction.merchant_charge') }}</th>
                            <th scope="col">{{ __('transaction.net_amount') }}</th>
                            <th scope="col">{{ __('transaction.status') }}</th>
                            <th scope="col">{{ __('transaction.remarks') }}</th>
                            <th scope="col">{{ __('transaction.sent_from') }}</th>
                            <th scope="col">{{ __('transaction.source') }}</th>
                            @if (adminAccessRoute(config('role.payout_manage.access.edit')))
                                <th scope="col">{{ __('transaction.more') }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $key => $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td data-label="{{ __('transaction.date') }}">
                                    {{ dateTime($item->created_at, 'd M,Y H:i') }}
                                </td>
                                <td data-label="{{ __('transaction.trx_number') }}"
                                    class="font-weight-bold text-uppercase">
                                    {{ $item->trx_id }}<br>
                                    <span class="text text-success">{{ $item->txn_id }}</span>

                                </td>
                                <td>{{ $item->partner_transection_id }}
                                    <br>
                                    {{ $item->member_id }}
                                </td>
                                <td data-label="{{ __('transaction.username') }}">

                                    @if ($item->api)
                                        {{ optional($item->api)->name }} <b>({{ optional($item->api)->acc_type }})</b>
                                    @else
                                        {{ __('transaction.partner_transection') }}
                                    @endif

                                </td>
                                <td>{{ $item->e_wallet_name }}</td>
                                <td>{{ $item->user_account_no }}</td>
                                <td data-label="{{ __('transaction.amount') }}" class="font-weight-bold">
                                    {{ getAmount($item->amount, 2) }}
                                    {{ $basic->currency_symbol }}</td>
                                <td data-label="{{ __('transaction.charge') }}" class="text-success">
                                    {{ getAmount($item->charge, 2) }} {{ $basic->currency_symbol }}</td>

                                <td data-label="{{ __('transaction.net_amount') }}" class="font-weight-bold">
                                    {{ getAmount($item->amount + $item->charge, 2) }} {{ $basic->currency_symbol }}
                                </td>

                                <td data-label="{{ __('transaction.status') }}" class="text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        @if ($item->transfer_status == 2)
                                            <span class="badge bg-success mb-1">
                                                <i class="fa fa-circle text-white font-12"></i>
                                                {{ __('transaction.request_approved') }}
                                            </span>
                                        @elseif($item->transfer_status == 1)
                                            <span class="badge bg-warning mb-1">
                                                <i class="fa fa-circle text-white font-12"></i>
                                                {{ __('transaction.request_pending') }}
                                            </span>
                                        @elseif($item->transfer_status == 3)
                                            <span class="badge bg-danger mb-1">
                                                <i class="fa fa-circle text-white font-12"></i>
                                                {{ __('transaction.request_rejected') }}
                                            </span>
                                        @endif

                                        @if ($item->status == 'Complete')
                                            <span class="badge bg-success mt-1">
                                                <i class="fa fa-circle text-white font-12"></i>
                                                {{ __('transaction.transferred') }}
                                            </span>
                                        @elseif($item->status == 'inititate' || $item->status == 'Pending')
                                            <span class="badge bg-warning mt-1">
                                                <i class="fa fa-circle text-white font-12"></i>
                                                {{ __('transaction.transfer_pending') }}
                                            </span>
                                        @elseif($item->status == 'Reject')
                                            <span class="badge bg-danger mt-1">
                                                <i class="fa fa-circle text-white font-12"></i>
                                                {{ __('transaction.transfer_rejected') }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    {{ $item->feedback }}
                                </td>
                                <td data-label="{{ __('transaction.method') }}">
                                    {{ $item->e_wallet_phone_number }}
                                    <br>
                                    {{ $item->e_wallet_type }}
                                </td>
                                <td data-label="{{ __('transaction.method') }}">{{ $item->request_source }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="icon-base ti tabler-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <!-- active / deactive button here -->
                                            @if (adminAccessRoute(config('role.payout_manage.access.edit')))
                                                <button type="button" class="btn btn-sm edit_button"
                                                    data-bs-toggle="modal" data-bs-target="#newModalb"
                                                    onclick="setBalanceItem({{ $item->id }})">
                                                    <i class="icon-base ti tabler-report-money me-1"></i>
                                                    {{ __('transaction.send_callback') }}
                                                </button><br>
                                                @if (isset($item))
                                                    <button class="btn  edit_buttonc  btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#myModalc" data-title="Edit"
                                                        data-id="{{ $item->id }}"
                                                        data-e_wallet_phone_number="{{ $item->e_wallet_phone_number }}">
                                                        <i class="icon-base ti tabler-device-mobile  me-1"></i>
                                                        {{ __('transaction.change_ewallet') }}
                                                    </button><br>
                                                @endif
                                                @php
                                                    $details =
                                                        $item->information != null
                                                            ? json_encode($item->information)
                                                            : null;
                                                @endphp
                                                <button type="button" class="btn btn-sm  edit_button"
                                                    data-bs-toggle="modal" data-bs-target="#myModal"
                                                    data-route="{{ route('admin.payout-action', $item->id) }}"
                                                    data-feedback="{{ $item->feedback }}"
                                                    data-info="{{ $details }}" data-id="{{ $item->id }}"
                                                    data-status="{{ $item->transfer_status }}"
                                                    data-statusb="{{ $item->status ? $item->status : '' }}">
                                                    @if (Request::routeIs('admin.payout-request'))
                                                        <i class="icon-base ti tabler-pencil me-1"></i>
                                                        {{ __('transaction.edit') }}
                                                    @else
                                                        <i
                                                            class="icon-base ti tabler-eye me-1"></i>{{ __('transaction.view') }}
                                                    @endif
                                                </button>
                                            @endif

                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%">
                                    <p class="text-dark">{{ __('transaction.no_data_found') }}</p>
                                </td>
                            </tr>

                        @endforelse
                    </tbody>
                </table>
                <div class="mt-5">
                    {{ $records->appends($_GET)->links('partials.pagination') }}
                </div>
            </div>
        </div>
    </div>
    <div class="modal modal-top fade" id="myModalc" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTopTitle">{{ __('transaction.change_ewallet_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?php
                date_default_timezone_set('Asia/Kuala_Lumpur');
                ?>
                <form role="form" method="POST" action="{{ route('admin.payout.update_e_wallet') }}">
                    @csrf
                    @method('put')
                    <div class="modal-body">
                        <ul class="list-group withdraw-detail">
                        </ul>

                        <div class="get-feedback">

                            <label>{{ __('transaction.ewallet_no') }}</label>
                            <input class="form-control e_wallet_phone_number" required name="e_wallet_phone_number"
                                type="text" />
                            <button type="submit" class="btn btn-primary mt-3" name="status"
                                value="1">{{ __('transaction.change') }}</button>
                        </div>
                        <input type="hidden" class="action_id" name="id">
                    </div>
                </form>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('transaction.close') }}</button>
                </div>

            </div>
        </div>
    </div>
    <div class="modal modal-top fade" id="newModalb" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTopTitle">{{ __('transaction.send_callback') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addBalanceForm" action="{{ route('admin.run.callback') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row justify-content-between align-items-center">
                            <input type="text" hidden id="account_id" class="form-control" name="id">
                            <div class="col-md-12">
                                {{ __('transaction.callback_status') }}
                                <span id="spinner2" style="display: none;">
                                    <span class="spinner-border text-primary" role="status">
                                    </span>
                                </span>
                                <span id="tickMark2" style="display: none;">
                                    <i class="fa fa-check-circle text-success"></i>
                                </span>
                                <span id="tickMark3" style="display: none;">
                                    <i class="fa fa-times-circle text-danger"></i>
                                </span>
                                <br>
                                <br>
                                <p>{{ __('transaction.message') }}: <span id="text1"></span></p>
                                <br>
                                <div id="apiresponse" style="display: none;">
                                    <h4>{{ __('transaction.response') }}</h4>
                                    <p>{{ __('transaction.response_code') }}: <span id="text2"></span></p>
                                    <p>{{ __('transaction.response_body') }}: </p>
                                    <div style="background-color: black;color:white;padding:10px"><span
                                            id="text3"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            </form>
        </div>
    </div>

    <!-- Modal for Edit button -->
    {{-- <div class="modal modal-top fade" id="myModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTopTitle">@lang('Payout Information')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form role="form" method="POST" class="actionRoute" action=""
                    enctype="multipart/form-data">
                    @csrf
                    @method('put')
                    <div class="modal-body">
                        <ul class="list-group withdraw-detail">
                        </ul>
                        <div class="form-group addForm">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')
                        </button>
                        @if (Request::routeIs('admin.payout-request'))
                            <input type="hidden" class="action_id" name="id">
                            <input type="hidden" name="status" id="statusInput">
                            <button type="submit" class="btn btn-primary status-btn"
                                data-status="2">@lang('Approve')</button>
                            <button type="submit" class="btn btn-danger status-btn"
                                data-status="3">@lang('Reject')</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div> --}}


    <div class="modal modal-top fade" id="myModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTopTitle">{{ __('transaction.payout_information') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form role="form" method="POST" class="actionRoute" id="actionRoutee" action=""
                    enctype="multipart/form-data" onsubmit="submitForm(this)">
                    @csrf
                    @method('put')
                    <div class="modal-body">
                        <ul class="list-group withdraw-detail">
                        </ul>
                        {{-- @if (Request::routeIs('admin.payout-request')) --}}
                        <div class="form-group addForm">
                        </div>
                        {{-- @endif --}}
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="status" name="status">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('transaction.close') }}
                        </button>

                        <input type="hidden" class="action_id" name="id">
                        <div id="submit1" style="display: none;">
                            <button type="submit" id="btn2" class="btn btn-primary" name="status"
                                value="2">{{ __('transaction.approve') }}</button>
                        </div>
                        <div id="submit2" style="display: none;">
                            <button type="submit" id="btn4" class="btn btn-dark" name="status"
                                value="4">{{ __('transaction.mark_as_complete') }}</button>
                        </div>
                        <div id="submit4" style="display: none;">
                            <button type="submit" id="btn5" class="btn btn-warning" name="status"
                                value="5">{{ __('transaction.mark_as_pending') }}</button>
                        </div>
                        <div id="submit3" style="display: none;">
                            <button type="submit" id="btn3" class="btn btn-danger" name="status"
                                value="3">{{ __('transaction.reject') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('js')
        <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
        <script>
            (function($) {
                $(document).ready(function() {
                    // Select2 Initialization
                    let $select = $('.select2').select2({
                        allowClear: true,
                        selectOnClose: true,
                    });
                    $select.on('select2:unselecting', function(e) {
                        $(this).data('unselecting', true);
                    });
                    $select.on('select2:opening', function(e) {
                        if ($(this).data('unselecting')) {
                            $(this).removeData('unselecting');
                            e.preventDefault();
                        }
                    });

                    // Disable submit button on form submit
                    $('form').on('submit', function() {
                        const $submitButton = $(this).find('button[type="submit"]');
                        $submitButton.prop('disabled', true).html(
                            "<i class='fa fa-spinner fa-spin me-1'></i> {{ __('transaction.processing') }}"
                        );
                        return true;
                    });

                    // Date Filter Buttons
                    const today = new Date();
                    const yyyy = today.getFullYear();
                    const mm = String(today.getMonth() + 1).padStart(2, '0');
                    const dd = String(today.getDate()).padStart(2, '0');
                    const todayStr = `${yyyy}-${mm}-${dd}`;
                    const filterForm = document.getElementById('filterForm');

                    function setDateInputs(from, to) {
                        document.getElementById('from_date').value = from;
                        document.getElementById('to_date').value = to;
                    }

                    function setActiveButton(buttonId) {
                        document.querySelectorAll('.btn-date-filter').forEach(btn => btn.classList.remove(
                            'active'));
                        document.getElementById(buttonId).classList.add('active');
                    }

                    $('#btn-today').on('click', function() {
                        setDateInputs(todayStr, todayStr);
                        setActiveButton('btn-today');
                        filterForm.submit();
                    });

                    $('#btn-yesterday').on('click', function() {
                        const y = new Date();
                        y.setDate(today.getDate() - 1);
                        const ys =
                            `${y.getFullYear()}-${String(y.getMonth() + 1).padStart(2, '0')}-${String(y.getDate()).padStart(2, '0')}`;
                        setDateInputs(ys, ys);
                        setActiveButton('btn-yesterday');
                        filterForm.submit();
                    });

                    $('#btn-last7').on('click', function() {
                        const from = new Date();
                        from.setDate(today.getDate() - 6);
                        const fromStr =
                            `${from.getFullYear()}-${String(from.getMonth() + 1).padStart(2, '0')}-${String(from.getDate()).padStart(2, '0')}`;
                        setDateInputs(fromStr, todayStr);
                        setActiveButton('btn-last7');
                        filterForm.submit();
                    });

                    // Edit Button Handling
                    $(document).on("click", '.edit_button', function() {
                        const $this = $(this);
                        const id = $this.data('id');
                        const feedback = $this.data('feedback');
                        const status = $this.data('status');
                        const statusb = $this.data('statusb');
                        const info = $this.data('info');
                        const ImgPath = "{{ asset(config('location.withdrawLog.path')) }}";

                        $(".action_id").val(id);
                        $(".actionRoute").attr('action', $this.data('route'));

                        let list = [];
                        if (info) {
                            Object.entries(info).forEach(([key, val]) => {
                                let content = val.type === 'file' ?
                                    `<br><img src="${ImgPath}/${val.field_name}" alt="..." class="w-50">` :
                                    `<span class="font-weight-bold ml-3">${val.field_name}</span>`;
                                list.push(
                                    `<li class="list-group-item"><span class="font-weight-bold">${key.replace('_', ' ')}</span> : ${content}</li>`
                                );
                            });
                        }

                        // Toggle buttons based on status
                        $('#submit1, #submit2, #submit3, #submit4').hide();
                        if (status == 2) {
                            $('#submit2, #submit3').show();
                        } else if (status == 3) {
                            // all hidden
                        } else {
                            $('#submit1, #submit3').show();
                        }
                        if (statusb == 'Complete') {
                            $('#submit4').show();
                        }

                        // Show remarks dropdown
                        $('.addForm').html(`
                    <div class="form-group">
                        <label for="feedback">{{ __('transaction.remarks') }}</label>
                        <select class="form-control" name="feedback" id="feedback" required>
                            <option value="">{{ __('transaction.select_feedback') }}</option>
                            <option value="invalid_phone_number">{{ __('transaction.invalid_phone_number') }}</option>
                            <option value="account_limit_over">{{ __('transaction.account_limit_over') }}</option>
                            <option value="kyc_incomplete">{{ __('transaction.kyc_incomplete') }}</option>
                            <option value="nagad_server_down">{{ __('transaction.nagad_server_down') }}</option>
                            <option value="bkash_server_down">{{ __('transaction.bkash_server_down') }}</option>
                            <option value="rocket_server_down">{{ __('transaction.rocket_server_down') }}</option>
                            <option value="others">{{ __('transaction.others') }}</option>
                        </select>
                    </div>
                `);

                        $('.withdraw-detail').html(list);
                    });

                    // Status Change Buttons
                    const statusForm = document.querySelector('#actionRoutee');
                    ['btn2', 'btn3', 'btn4', 'btn5'].forEach((btnId, idx) => {
                        const btn = document.getElementById(btnId);
                        if (!btn) return;
                        btn.addEventListener('click', function(e) {
                            e.preventDefault();
                            const status = idx + 2;
                            if (btnId === 'btn3') {
                                const selectBox = document.querySelector("select[name='feedback']");
                                if (!selectBox || selectBox.value === '') {
                                    alert("{{ __('transaction.select_issue_alert') }}");
                                    return;
                                }
                            }
                            document.getElementById("status").value = status;
                            ['btn2', 'btn3', 'btn4', 'btn5'].forEach(id => document.getElementById(
                                id).disabled = true);
                            statusForm.submit();
                        });
                    });

                    // E-wallet phone number handler
                    $(document).on("click", '.edit_buttonc', function() {
                        $(".action_id").val($(this).data('id'));
                        $(".e_wallet_phone_number").val($(this).data('e_wallet_phone_number'));
                    });

                    // Withdrawal Test
                    $('#runWithdrawalTest').click(function() {
                        if ($('#acc_no').val() === "") {
                            alert("{{ __('transaction.select_admin_account') }}");
                            return;
                        }
                    });

                    // Modal reset
                    $('.modal-header .close').click(function() {
                        $('#runWithdrawalTest').prop('disabled', false);
                        $('#spinner2, #tickMark2').hide();
                    });

                    // Set balance
                    window.setBalanceItem = function(itemId) {
                        $('#account_id').val(itemId);
                        $('#spinner2').show();
                        $('#runWithdrawalTest').prop('disabled', true);

                        const formData = new FormData($('#addBalanceForm')[0]);
                        $.ajax({
                            type: "POST",
                            url: "{{ route('admin.run.callback') }}",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                $('#spinner2').hide();
                                if (response.status === "success") {
                                    $('#tickMark2').show();
                                    $('#apiresponse').show();
                                } else {
                                    $('#tickMark3').show();
                                    $('#apiresponse').hide();
                                }
                                $("#text1").text(response.message);
                                $("#text2").text(response.code);
                                $("#text3").text(response.response_payload);
                            },
                            error: function() {
                                $('#spinner2').hide();
                                $('#tickMark3').show();
                                $('#apiresponse').hide();
                                $("#text1").text("{{ __('transaction.processing_error') }}");
                                $("#text2, #text3").text('');
                            }
                        });
                    }

                    // Notification polling
                    setInterval(function() {
                        $.get("{{ route('admin.payout-report.getnotification') }}", {
                            letest_record: $('#letest_record').val()
                        }, function(response) {
                            if (response.message === "success") {
                                document.getElementById("notification-sound").play();
                                window.location.reload();
                            }
                        }).fail(function(xhr) {
                            console.log('Error:', xhr.responseText);
                        });
                    }, 5000);
                });
            })(jQuery);
        </script>
    @endpush



</x-admin-layout>
