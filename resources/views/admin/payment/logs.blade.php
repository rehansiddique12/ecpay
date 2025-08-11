<x-admin-layout :title="$pageTitle">
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <!-- Search Form -->
        <form id="searchForm" action="{{ route('admin.payment.search') }}" method="get">
            <div class="row justify-content-between align-items-center">
                <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
                <div class="col-md-4">
                    <div class="form-group">
                        <input type="text" name="name" value="{{ @request()->name }}" class="form-control"
                            placeholder="{{ __('transaction.username_or_email') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <input type="text" name="partner_transection_id"
                            value="{{ @request()->partner_transection_id }}" class="form-control"
                            placeholder="{{ __('transaction.transection_no') }}">
                    </div>
                </div>

                <div class="col-md-4 mt-3">
                    <div class="form-group">
                        <select name="status" class="form-select">
                            <option value="All" @if (@request()->status == 'All') selected @endif>
                                {{ __('transaction.all_payment') }}</option>
                            <option value="Complete" @if (@request()->status == 'Complete') selected @endif>
                                {{ __('transaction.complete_payment') }}</option>
                            <option value="Pending" @if (@request()->status == 'Pending') selected @endif>
                                {{ __('transaction.pending_payment') }}</option>
                            <option value="Reject" @if (@request()->status == 'Reject') selected @endif>
                                {{ __('transaction.cancel_payment') }}</option>
                            <option value="99" @if (@request()->status == '99') selected @endif>
                                {{ __('transaction.member_not_completed') }}</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4 mt-3">
                    <div class="form-group">
                        <input type="date" class="form-control" value="{{ @request()->date_time }}" name="date_time"
                            id="datepicker" />
                    </div>
                </div>

                <div class="col-md-4 mt-3">
                    <div class="form-group">
                        <select name="website" class="form-select select2" data-allow-clear="true"
                            data-placeholder="{{ __('transaction.select_partner') }}">
                            <option></option>
                            <option value="">{{ __('transaction.all_source') }}</option>
                            @foreach ($domains as $partner)
                                <option value="{{ $partner->id }}" @if (@request()->website == $partner->id) selected @endif>
                                    {{ $partner->name }} ===> ( {{ $partner->website }} )</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4 mt-5">
                    <div class="form-group d-flex gap-5">
                        <button type="submit" class="btn btn-primary">
                            <i class="icon-base ti tabler-search"></i> {{ __('transaction.search') }}
                        </button>

                        <!-- Export Button (will be handled by JavaScript) -->
                        {{-- <button type="button" id="exportBtn" class="btn btn-success">
                            <i class="icon-base ti tabler-download"></i> {{ __('transaction.export_data') }}
                        </button> --}}
                        <a href="{{ route('admin.payment_log_export', [
                            'name' => request()->name,
                            'partner_transection_id' => request()->partner_transection_id,
                            'status' => request()->status,
                            'date_time' => request()->date_time,
                            'website' => request()->website,
                        ]) }}"
                            class="btn waves-effect waves-light btn-success">
                            <i class="icon-base ti tabler-download me-1"></i> {{ __('merchant_reports.export') }}
                        </a>


                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Hidden Export Form -->
    <form id="exportForm" action="{{ route('admin.payment_log_export') }}" method="get" style="display: none;">
        @if (request()->has('name'))
            <input type="hidden" name="name" value="{{ request()->name }}">
        @endif
        @if (request()->has('partner_transection_id'))
            <input type="hidden" name="partner_transection_id" value="{{ request()->partner_transection_id }}">
        @endif
        @if (request()->has('status'))
            <input type="hidden" name="status" value="{{ request()->status }}">
        @endif
        @if (request()->has('date_time'))
            <input type="hidden" name="date_time" value="{{ request()->date_time }}">
        @endif
        @if (request()->has('website'))
            <input type="hidden" name="website" value="{{ request()->website }}">
        @endif
    </form>

    <!-- Fancybox CSS/JS -->
    <link rel="stylesheet" href="{{ asset('node_modules/@fancyapps/fancybox/dist/jquery.fancybox.min.css') }}">
    <script src="{{ asset('node_modules/@fancyapps/fancybox/dist/jquery.fancybox.min.js') }}"></script>

    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">{{ __('transaction.date') }}</th>
                            <th scope="col">{{ __('transaction.trx_number') }}</th>
                            <th scope="col">{{ __('transaction.partner_trx_no') }}</th>
                            <th scope="col">{{ __('transaction.partner_txn_input') }}</th>
                            <th scope="col">{{ __('transaction.username') }}</th>
                            <th scope="col">{{ __('transaction.method') }}</th>
                            <th scope="col">{{ __('transaction.acc_no') }}</th>
                            <th scope="col">{{ __('transaction.amount') }}</th>
                            <th scope="col">{{ __('transaction.merchant_charge') }}</th>
                            <th scope="col">{{ __('transaction.final_amount') }}</th>
                            <th scope="col">{{ __('transaction.status') }}</th>
                            <th scope="col">{{ __('transaction.source') }}</th>
                            <th scope="col">{{ __('transaction.completed_at') }}</th>
                            <th scope="col">{{ __('transaction.receipt') }}</th>
                            @if (adminAccessRoute(config('role.payment_log.access.edit')))
                                <th scope="col">{{ __('transaction.action') }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($funds as $key => $fund)
                            <tr>
                                <td data-label="{{ __('transaction.date') }}">
                                    {{ dateTime($fund->created_at, 'd M,Y H:i') }}
                                </td>
                                <td data-label="{{ __('transaction.trx_number') }}"
                                    class="font-weight-bold text-uppercase">
                                    {{ $fund->transaction }}<br>
                                    <span class="text text-success">{{ $fund->txn_id }}</span>
                                </td>
                                <td>{{ !empty($fund->partner_transection_id) ? $fund->partner_transection_id : '' }}
                                    <br>
                                    {{ !empty($fund->member_id) ? $fund->member_id : '' }}
                                </td>

                                <td>
                                    {{ !empty($fund->txn_record) && $fund->partner_transection_id != 0 ? $fund->txn_record->txn_no : '' }}
                                </td>

                                <td data-label="{{ __('transaction.username') }}">
                                    @if (optional($fund->user)->username && optional($fund->user)->username !== 'dummyuser')
                                        <a href="{{ route('admin.user-edit', $fund->user_id) }}" target="_blank">
                                            <div class="d-lg-flex d-block align-items-center ">
                                                <div class="mr-3"><img
                                                        src="{{ getFile(config('location.user.path') . optional($fund->user)->image) }}"
                                                        alt="user" class="rounded-circle" width="45"
                                                        height="45"></div>
                                                <div class="">
                                                    <h5 class="text-dark mb-0 font-16 font-weight-medium">
                                                        {{ optional($fund->user)->username }}</h5>
                                                    <span
                                                        class="text-muted font-14">{{ optional($fund->user)->email }}</span>
                                                </div>
                                            </div>
                                        </a>
                                    @elseif($fund->source == 'Admin Test')
                                        Admin Test
                                    @else
                                        {{ optional($fund->api)->name }} <b>({{ optional($fund->api)->acc_type }})</b>
                                    @endif
                                </td>
                                <td data-label="{{ __('transaction.method') }}">{{ optional($fund->gateway)->name }}
                                </td>
                                <td class="font-weight-bold">{{ $fund->sender }}</td>
                                <td data-label="{{ __('transaction.amount') }}" class="font-weight-bold">
                                    {{ getAmount($fund->amount) }}
                                    {{ $fund->gateway?->currency }}</td>
                                <td data-label="{{ __('transaction.charge') }}" class="text-success">
                                    {{ getAmount($fund->charge) }}
                                    {{ $fund->gateway?->currency }}</td>
                                <td data-label="{{ __('transaction.payable') }}" class="font-weight-bold">
                                    {{ getAmount($fund->amount) - getAmount($fund->charge) }}
                                    {{ $fund->gateway?->currency }}
                                </td>

                                <td data-label="{{ __('transaction.status') }}" class="text-lg-center text-right">
                                    @if ($fund->status == 'Confirm')
                                        <span class="badge bg-info"><i
                                                class="fa fa-circle text-white font-12"></i>
                                            Confirmed</span>
                                    @elseif ($fund->status == 'Pending')
                                        @php
                                            $createdAt = \Carbon\Carbon::parse($fund->created_at);
                                            $currentTime = \Carbon\Carbon::now();
                                            $diffInMinutes = $createdAt->diffInMinutes($currentTime);
                                        @endphp

                                        @if ($diffInMinutes > 10 && @request()->status != 'Pending')
                                            <span class="badge bg-warning">
                                                <i class="fa fa-circle text-white warning font-12"></i>
                                                {{ __('transaction.member_not_completed') }}
                                            </span>
                                        @else
                                            <span class="badge bg-info">
                                                <i class="fa fa-circle text-white font-12"></i>
                                                {{ __('transaction.pending') }}
                                            </span>
                                        @endif
                                        <br>
                                        <span class="text text-primary">{{ $fund->e_wallet_phone_number }}</span>
                                    @elseif($fund->status == 'Complete')
                                        @php
                                            if ($fund->completed_source != 'AdminPanel') {
                                                $classColor = 'bg-success';
                                            } else {
                                                $classColor = 'bg-primary';
                                            }
                                        @endphp

                                        <span class="badge {{ $classColor }}"><i
                                                class="fa fa-circle text-white font-12"></i>
                                            {{ __('transaction.completed') }}
                                            <br>
                                            <span
                                                class="{{ $classColor }}">{{ $fund->e_wallet_phone_number }}</span>
                                        @elseif($fund->status == 'Reject')
                                            <span class="badge bg-danger"><i
                                                    class="fa fa-circle text-white danger font-12"></i>
                                                {{ __('transaction.rejected') }}
                                                <br>
                                                <span class="text text-danger">
                                                    {{ $fund->e_wallet_phone_number }}</span>
                                    @endif
                                </td>
                                <td data-label="{{ __('transaction.method') }}">
                                    {{ optional($fund->api)->website }}
                                    <br>
                                    @if (!empty($fund->request_source))
                                        <span class="text text-dark">({{ $fund->request_source }})</span>
                                    @endif
                                </td>
                                <td>{{ $fund->created_at }}</td>
                                <td>
                                    @if (!empty($fund->receipt_image))
                                        <a data-fancybox="images"
                                            href="{{ getFile(config('location.receipts.path') . $fund->receipt_image) }}">
                                            <h2><i class="fa fa-file"></i></h2>
                                        </a>
                                    @endif
                                </td>

                                @if (adminAccessRoute(config('role.payment_log.access.edit')))
                                    <td data-label="{{ __('transaction.action') }}">
                                        @php
                                            if ($fund->detail) {
                                                $details = [];
                                                foreach ($fund->detail as $k => $v) {
                                                    if ($v->type == 'file') {
                                                        $details[kebab2Title($k)] = [
                                                            'type' => $v->type,
                                                            'field_name' => getFile(
                                                                config('location.deposit.path') .
                                                                    date('Y', strtotime($fund->created_at)) .
                                                                    '/' .
                                                                    date('m', strtotime($fund->created_at)) .
                                                                    '/' .
                                                                    date('d', strtotime($fund->created_at)) .
                                                                    '/' .
                                                                    $v->field_name,
                                                            ),
                                                        ];
                                                    } else {
                                                        $details[kebab2Title($k)] = [
                                                            'type' => $v->type,
                                                            'field_name' => $v->field_name,
                                                        ];
                                                    }
                                                }
                                            } else {
                                                $details = null;
                                            }
                                        @endphp

                                        <button
                                            class="edit_button  btn  {{ $fund->status == 'Pending' ? 'btn-primary' : 'btn-success' }} text-white  btn-sm "
                                            data-bs-toggle="modal" data-bs-target="#myModal"
                                            data-title="{{ $fund->status == 'Pending' ? __('transaction.edit') : __('transaction.details') }}"
                                            data-id="{{ $fund->id }}" data-feedback="{{ $fund->feedback }}"
                                            data-info="{{ json_encode($details) }}"
                                            data-amount="{{ getAmount($fund->amount) }}"
                                            data-username="{{ optional($fund->user)->username }}"
                                            data-route="{{ route('admin.payment.action', $fund->id) }}"
                                            data-status="{{ $fund->status }}" data-sender="{{ $fund->sender }}"

                                            data-confirm="{{ adminAccessRoute(config('role.depositconfirm.access.view'))?1:0 }}"
                                            data-approved="{{ adminAccessRoute(config('role.depositapporve.access.view'))?1:0 }}"
                                            data-txn_id="{{ $fund->txn_id }}"
                                            data-e_wallet_type="{{ $fund->e_wallet_type }}"
                                            data-date_time="{{ $fund->date_time }}"

                                            data-e_wallet_phone_number="{{ $fund->e_wallet_phone_number }}">

                                            <i class="icon-base ti tabler-pencil me-1"></i>
                                            {{-- @if ($fund->status == 'Pending')
                                                <i class="icon-base ti tabler-pencil me-1"></i>
                                            @else
                                                <i class="icon-base ti tabler-eye me-1"></i>
                                            @endif --}}

                                        </button>
                                        <button class="edit_buttonc  btn btn-danger text-white  btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#myModalc"
                                            data-bs-title="{{ __('transaction.edit') }}"
                                            data-id="{{ $fund->id }}"
                                            data-e_wallet_phone_number="{{ $fund->e_wallet_phone_number }}">
                                            <i class="icon-base ti tabler-device-mobile me-1"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-dark" data-bs-toggle="modal"
                                            data-bs-target="#newModalb"
                                            onclick="setBalanceItem({{ $fund->id }})">
                                            <i class="icon-base ti tabler-direction-sign me-1"></i>
                                        </button>
                                    </td>
                                @endif
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
                    {{ $funds->appends($_GET)->links('partials.pagination') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Edit button -->
    <div class="modal modal-top fade" id="myModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTopTitle">{{ __('transaction.deposit_information') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?php
                date_default_timezone_set('Asia/Kuala_Lumpur');
                ?>
<<<<<<< HEAD
=======
                {{-- <form role="form" class="actionRoute" action=""> --}}
                   {{-- @if (adminAccessRoute(config('role.depositconfirm.access.view')) || adminAccessRoute(config('role.depositapporve.access.view'))) --}}
                   <div id="form_div">
>>>>>>> 2ee44e37a7af6c05c1294ff2125f19fe51930e99
                <form role="form" method="POST" class="actionRoute" action=""
                    enctype="multipart/form-data" onsubmit="submitForm(this)">
                    @csrf
                    @method('put')
                    <div class="modal-body">
                        <ul class="list-group withdraw-detail">
                        </ul>

                        <div class="get-feedback">
                            <label>{{ __('transaction.sender_acc_no') }}</label>
                            <input class="form-control sender" name="sender" type="text" />
                            <label>{{ __('transaction.e_wallet_no') }}</label>
                            <input class="form-control e_wallet_phone_number" required name="e_wallet_phone_number"
                                type="text" />
                            <label>{{ __('transaction.txn_no') }}</label>
                            <input class="form-control txn_id" name="txn_id" type="text" />
                            <label>{{ __('transaction.e_wallet_type') }}</label>
                            <select class="form-select e_wallet_type" name="e_wallet_type">
                                <option value="Personal">{{ __('transaction.personal') }}</option>
                                <option value="Merchant">{{ __('transaction.merchant') }}</option>
                            </select>
                            
                            <label>{{ __('transaction.payment_receiving_datetime') }}</label>
                            <input class="form-control date_time" id="e_wallet_phone_number" required
                                value="<?php echo date('Y-m-d\TH:i'); ?>" name="date_time" type="datetime-local" />
<<<<<<< HEAD
                            <div id="2fa_div">
                                <label>{{ __('transaction.2fa') }}</label>
                                <input class="form-control" name="twofa" type="text" />
                            </div>
                            <button type="submit" class="btn btn-primary mt-2" id="approvebtn" name="status"
=======
                            <div id="2fa_div">   
                            <label>{{ __('transaction.2fa') }}</label>
                            <input class="form-control" name="twofa" type="text" />
                            </div> 

                            <input type="hidden" name="status" id="setstatus">

                            <div id="confirm_div">
                            
                            <button type="submit" class="btn btn-success mt-2" id="approvebtn" name="submit"
                                value="Confirm">{{ __('transaction.confirm') }}</button>
                            </div>
                               <div id="approve_div">
                            
                            <button type="submit" class="btn btn-primary mt-2" id="approvebtn" name="submit"
>>>>>>> 2ee44e37a7af6c05c1294ff2125f19fe51930e99
                                value="Complete">{{ __('transaction.approve') }}</button>
                               </div>

                               <div id="update_div">
                            
                            <button type="submit" class="btn btn-success mt-2" id="approvebtn" name="submit"
                                value="Update">Edit</button>
                            </div>

                        </div>

                        <input type="hidden" class="action_id" name="id">
                    </div>
                </form>
                </div>
                {{-- @endif --}}
                <form role="form" method="POST" class="actionRoute" action=""
                    enctype="multipart/form-data">
                    @csrf
                    @method('put')
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('transaction.close') }}</button>
                        @if (Request::routeIs('admin.payment.pending'))
                            <!-- // -->
                        @endif
                        <input type="hidden" class="action_id" name="id">
                        <input type="hidden" name="status" value="Reject">
                        <button type="submit" class="btn btn-danger" name="status"
                            value="Reject">{{ __('transaction.reject') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal modal-top fade" id="myModalc" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTopTitle">{{ __('transaction.change_e_wallet_no') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?php
                date_default_timezone_set('Asia/Kuala_Lumpur');
                ?>
                <form role="form" method="POST" action="{{ route('admin.payment.update_e_wallet') }}">
                    @csrf
                    @method('put')
                    <div class="modal-body">
                        <ul class="list-group withdraw-detail">
                        </ul>

                        <div class="get-feedback">
                            <label>{{ __('transaction.e_wallet_no') }}</label>
                            <input class="form-control e_wallet_phone_number" required name="e_wallet_phone_number"
                                type="text" />
                            <button type="submit" class="btn btn-primary mt-2" name="status"
                                value="1">{{ __('transaction.change') }}</button>
                        </div>
                        <input type="hidden" class="action_id" name="id">
                    </div>
                </form>
                <form role="form" method="POST" class="actionRoute" action=""
                    enctype="multipart/form-data">
                    @csrf
                    @method('put')
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('transaction.close') }}</button>
                    </div>
                </form>
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
                <form id="addBalanceForm" action="{{ route('admin.run.deposit.callback') }}" method="POST">
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
                                    <p>{{ __('transaction.response_body') }}:</p>
                                    <div style="background-color: black;color:white;padding:10px"><span
                                            id="text3"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('js')
        <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
        <script>
            // Handle export button click
            document.getElementById('exportBtn').addEventListener('click', function() {
                // Get all form inputs from search form
                const formInputs = document.getElementById('searchForm').elements;

                // Add them to the export form
                for (let i = 0; i < formInputs.length; i++) {
                    const input = formInputs[i];
                    if (input.name && input.type !== 'button' && input.type !== 'submit') {
                        let existingInput = document.querySelector(`#exportForm input[name="${input.name}"]`);
                        if (!existingInput) {
                            let newInput = document.createElement('input');
                            newInput.type = 'hidden';
                            newInput.name = input.name;
                            newInput.value = input.value;
                            document.getElementById('exportForm').appendChild(newInput);
                        }
                    }
                }

                // Submit the export form
                document.getElementById('exportForm').submit();
            });

            function submitForm(form) {
                document.getElementById('approvebtn').disabled = true;
                form.submit();
            }

            function refreshDateTime() {
                var inputDateTimeString = document.getElementById("e_wallet_phone_number").value;
                var inputDateTime = new Date(inputDateTimeString).getTime();
                var currentDateTimeKL = new Date().toLocaleString("en-US", {
                    timeZone: "Asia/Kuala_Lumpur"
                });

                var date = new Date(currentDateTimeKL);
                var year = date.getFullYear();
                var month = (date.getMonth() + 1).toString().padStart(2, '0');
                var day = date.getDate().toString().padStart(2, '0');
                var hours = date.getHours().toString().padStart(2, '0');
                var minutes = date.getMinutes().toString().padStart(2, '0');

                var formattedDateTimeKL = `${year}-${month}-${day} ${hours}:${minutes}`;

                var currentDateTime = new Date(currentDateTimeKL).getTime();
                var twoMinutesAgoTimestamp = currentDateTime - (2 * 60 * 1000);
                if (inputDateTime > twoMinutesAgoTimestamp) {
                    document.getElementById("e_wallet_phone_number").value = formattedDateTimeKL;
                }
            }

            setInterval(refreshDateTime, 5000);

            $(document).ready(function() {
                jQuery(document).on("click", '.edit_button', function(e) {
                    var id = jQuery(this).data('id');
                    var sender = jQuery(this).data('sender');
                    var feedback = jQuery(this).data('feedback');
                    var e_wallet_phone_number = jQuery(this).data('e_wallet_phone_number');
<<<<<<< HEAD
=======
                    var t_status = jQuery(this).data('status');
                    var u_confirm = jQuery(this).data('confirm');
                    var t_approved = jQuery(this).data('approved');
                    var txn_id = jQuery(this).data('txn_id');
                    var date_time = jQuery(this).data('date_time');
                    var e_wallet_type = jQuery(this).data('e_wallet_type');


                    if (((u_confirm == 1 && (t_status === 'Pending' || t_status === 'Reject' || t_status === 'Complete')) || (t_approved == 1 && (t_status === 'Confirm' || t_status === 'Complete')))) {
                        // Show the form
                        jQuery("#form_div").show();

                        // Hide both action sections first
                        jQuery("#confirm_div").hide();
                        jQuery("#approve_div").hide();
                        jQuery("#update_div").hide();

                        

                        // If (status is Pending or Reject) AND u_confirm == 1 → show confirm_div
                        if ((t_status === 'Pending' || t_status === 'Reject') && u_confirm == 1) {
                            jQuery("#confirm_div").show();
                            jQuery("#setstatus").val('Confirm');
                        }

                        // If status is Confirm AND t_approved == 1 → show approve_div
                        else if (t_status === 'Confirm' && t_approved == 1) {
                            jQuery("#approve_div").show();
                            jQuery("#setstatus").val('Complete');
                        }

                        else if (t_status === 'Complete' && (t_approved == 1 || u_confirm == 1)) {
                            jQuery("#update_div").show();
                            jQuery("#setstatus").val('Update');
                        }

                        // Populate form values (optional)
                        jQuery(".action_id").val(id);
                        jQuery(".sender").val(sender);
                        jQuery(".e_wallet_phone_number").val(e_wallet_phone_number);
                        // Add other fields as necessary...
                    } else {
                        // Hide form if condition doesn't meet
                        jQuery("#form_div").hide();
                    }

>>>>>>> 2ee44e37a7af6c05c1294ff2125f19fe51930e99
                    var amount = jQuery(this).data('amount');

                    if (amount >= 1000) {
                        jQuery('#2fa_div').show();
                    } else {
                        jQuery('#2fa_div').hide();
                    }

                    jQuery(".action_id").val(id);
                    jQuery(".sender").val(sender);
                    jQuery(".e_wallet_phone_number").val(e_wallet_phone_number);
                    jQuery(".actionRoute").attr('action', jQuery(this).data('route'));

                    jQuery(".txn_id").val(txn_id);
                    if (date_time) {
                        jQuery(".date_time").val(date_time);
                    }
                    if (e_wallet_type) {
                        jQuery('.e_wallet_type').val(e_wallet_type);
                    }

                    var details = Object.entries(jQuery(this).data('info'));
                    var list = [];
                    details.map(function(item, i) {
                        if (item[1].type == 'file') {
                            var singleInfo =
                                `<br><img src="${item[1].field_name}" alt="..." class="w-100">`;
                        } else {
                            var singleInfo =
                                `<span class="font-weight-bold ml-3">${item[1].field_name}</span>`;
                        }
                        list[i] =
                            ` <li class="list-group-item"><span class="font-weight-bold"> ${item[0].replace('_', " ")} </span> : ${singleInfo}</li>`;
                    });
                    jQuery('.withdraw-detail').html(list);

                    if (feedback == '') {
                        var res = `<div class="form-group"><br>
                                        <label class="font-weight-bold">{{ __('transaction.send_you_feedback') }}</label>
                                        <textarea name="feedback" class="form-control" row="3" required>{{ old('feedback') }}</textarea>
                                </div>`;
                    } else {
                        var res = `<h5>{{ __('transaction.feedback') }}</h5>
                                    <p>${feedback}</p>`;
                    }

                    jQuery('.get-feedback').html(res);
                });

            });

            jQuery(document).on("click", ".edit_buttonc", function(e) {
                e.preventDefault();
                var id = jQuery(this).data("bs-id");
                var e_wallet_phone_number = jQuery(this).data("bs-e_wallet_phone_number");
                jQuery(".action_id").val(id);
                jQuery(".e_wallet_phone_number").val(e_wallet_phone_number);
            });

            function setBalanceItem(itemId) {
                var account_id = document.getElementById("account_id");
                account_id.value = itemId;

                jQuery('#spinner2').show();
                jQuery('#runWithdrawalTest').prop('disabled', true);

                var formData = new FormData(jQuery('#addBalanceForm')[0]);

                jQuery.ajax({
                    type: "POST",
                    url: "{{ route('admin.run.deposit.callback') }}",
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status === "success") {
                            jQuery('#spinner2').hide();
                            jQuery('#tickMark2').show();
                            jQuery('#apiresponse').show();
                        } else {
                            jQuery('#spinner2').hide();
                            jQuery('#tickMark3').show();
                            jQuery('#apiresponse').hide();
                        }

                        document.getElementById("text1").innerText = response.message;
                        document.getElementById("text2").innerText = response.code;
                        document.getElementById("text3").innerText = response.response_payload;
                    },
                    error: function(xhr, status, error) {
                        jQuery('#spinner2').hide();
                        jQuery('#tickMark3').show();
                        jQuery('#apiresponse').hide();

                        document.getElementById("text1").innerText =
                            "{{ __('transaction.processing_error') }}";
                        document.getElementById("text2").innerText = '';
                        document.getElementById("text3").innerText = '';
                    }
                });
            }

            jQuery(document).ready(function() {
                jQuery('.modal-header .close').click(function() {
                    jQuery('#spinner2').hide();
                    jQuery('#tickMark2').hide();
                });
            });

            $(document).ready(function() {
                $('form').on('submit', function() {
                    const $form = $(this);
                    const $submitButton = $form.find('button[type="submit"]');

                    $submitButton.prop('disabled', true);
                    $submitButton.html(
                        "<i class='fa fa-spinner fa-spin me-1'></i> {{ __('transaction.processing') }}");

                    return true;
                });

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
            });
        </script>
    @endpush

    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
    @endpush
<<<<<<< HEAD
=======

>>>>>>> 2ee44e37a7af6c05c1294ff2125f19fe51930e99
</x-admin-layout>
