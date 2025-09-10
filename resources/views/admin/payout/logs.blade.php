<x-admin-layout :title="$pageTitle">
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('admin.payout-log.search') }}" method="get">
            <div class="row justify-content-between align-items-center">
                <div class="col-md-4">
                    <div class="form-group">
                        <input type="text" name="name" value="{{ @request()->name }}" class="form-control"
                            placeholder="{{ __('transaction.email_or_username') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <input type="text" name="partner_transection_id"
                            value="{{ @request()->partner_transection_id }}" class="form-control"
                            placeholder="{{ __('transaction.transaction_no') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <input type="date" class="form-control" value="{{ @request()->date_time }}" name="date_time"
                            id="datepicker" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select name="status" class="form-select">
                            <option value="4">{{ __('transaction.all_payment') }}</option>
                            <option value="1" @if (request()->status == '1') selected @endif>
                                {{ __('transaction.pending_payment') }}</option>
                            <option value="2" @if (request()->status == '2') selected @endif>
                                {{ __('transaction.complete_payment') }}</option>
                            <option value="3" @if (request()->status == '3') selected @endif>
                                {{ __('transaction.cancel_payment') }}</option>
                        </select>
                    </div>
                </div>



                <div class="col-md-3">
                    <div class="form-group">
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
                <div class="col-md-3">
                    <div class="form-group">
                        <select name="transfer_status" class="form-select">
                            <option value="4">{{ __('transaction.all_payment') }}</option>
                            <option value="Pending" @if (request()->transfer_status == 'Pending') selected @endif>
                                {{ __('transaction.pending_payment') }}</option>
                            <option value="Complete" @if (request()->transfer_status == 'Complete') selected @endif>
                                {{ __('transaction.complete_payment') }}</option>
                            <option value="Cancel" @if (request()->transfer_status == 'Cancel') selected @endif>
                                {{ __('transaction.cancel_payment') }}</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3 d-flex gap-5">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="icon-base ti tabler-search me-1"></i>
                            {{ __('transaction.search') }}
                        </button>

                        <a href="{{ route('admin.payout-log-export', request()->query()) }}" class="btn btn-success">
                            <i class="icon-base ti tabler-download me-1"></i>
                            {{ __('transaction.export_data') }}
                        </a>


                    </div>
                </div>

            </div>
        </form>

    </div>

    <input type="text" value="{{ $letest_record }}" id="letest_record" hidden>
    <audio id="notification-sound" src="{{ asset(config('location.withdrawLog.path')) }}/dogru-128492.mp3"
        preload="auto"></audio>


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
                                        {{ __('transaction.partner_transaction') }}
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
                                <td data-label="@lang('Method')">
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
                                                        {{ __('transaction.change_e_wallet_no') }}
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
                    <h5 class="modal-title" id="modalTopTitle">{{ __('transaction.change_e_wallet_no') }}</h5>
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

                            <label>{{ __('transaction.e_wallet_no') }}</label>
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
                                    <p>{{ __('transaction.response_body') }}:</p>
                                    <div style="background-color: black;color:white;padding:10px"><span
                                            id="text3"></span></div>
                                </div>

                            </div>

                            <!-- <br>
                        <br> -->

                            <!-- <div class="col-md-12">
                            <button type="button" disabled id="runWithdrawalTest" class="btn btn-primary">Run Withdrawal Test</button>

                        </div> -->
                        </div>

                    </div>
            </div>
            </form>
        </div>
    </div>




    <!-- Modal for Edit button -->
    <div class="modal modal-top fade" id="myModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTopTitle">{{ __('transaction.payout_info') }}</h5>
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
            document.addEventListener("DOMContentLoaded", function() {
                // Get references to all buttons
                var btn2 = document.getElementById("btn2");
                var btn3 = document.getElementById("btn3");
                var btn4 = document.getElementById("btn4");
                var btn5 = document.getElementById("btn5");

                // Function to handle button click
                function handleButtonClick(statusValue) {
                    // Set the status input field value
                    document.getElementById("status").value = statusValue;

                    // Disable all buttons
                    btn2.disabled = true;
                    btn3.disabled = true;
                    btn4.disabled = true;
                    btn5.disabled = true;

                    // Submit the form
                    document.querySelector('#actionRoutee').submit();
                }

                // Attach event listeners to each button
                btn2.addEventListener("click", function(event) {
                    event.preventDefault(); // Prevent default form submission
                    handleButtonClick(2);
                });

                btn3.addEventListener("click", function(event) {
                    event.preventDefault(); // Prevent default form submission

                    // Find the select box
                    const selectBox = document.querySelector("select[name='feedback']");
                    if (selectBox) {
                        // Add the 'required' attribute
                        selectBox.setAttribute("required", "required");

                        // Check if the select box has an empty value
                        if (selectBox.value === "") {
                            alert("{{ __('transaction.select_issue_alert') }}");
                            return; // Prevent further execution
                        }
                    }

                    // Call the function to handle button click
                    handleButtonClick(3);
                });

                btn4.addEventListener("click", function(event) {
                    event.preventDefault(); // Prevent default form submission
                    handleButtonClick(4);
                });

                btn5.addEventListener("click", function(event) {
                    event.preventDefault(); // Prevent default form submission
                    handleButtonClick(5);
                });
            });

            $(document).ready(function() {
                var intervalId; // To store the interval id
                var orderid = document.getElementById("orderid");
                var wid = document.getElementById("wid");
                var acc_no = document.getElementById("acc_no");

                $('#runWithdrawalTest').click(function() {
                    if (acc_no.value === "") {
                        alert("{{ __('transaction.select_admin_account_alert') }}");
                        return;
                    }

                });
                // Function to perform the AJAX call
                $('.modal-header .close').click(function() {
                    $('#runWithdrawalTest').prop('disabled', false);
                    $('#spinner2').hide();
                    $('#tickMark2').hide();
                });
            });

            function setBalanceItem(itemId) {
                var account_id = jQuery("#account_id");
                account_id.val(itemId);

                jQuery('#spinner2').show();
                jQuery('#runWithdrawalTest').prop('disabled', true);
                var formData = new FormData(jQuery('#addBalanceForm')[0]); // Get form data
                jQuery.ajax({
                    type: "POST",
                    url: "{{ route('admin.run.callback') }}",
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log(response);
                        if (response.status === "success") {
                            jQuery('#spinner2').hide();
                            jQuery('#tickMark2').show();
                            jQuery('#apiresponse').show();
                        } else {
                            jQuery('#spinner2').hide();
                            jQuery('#tickMark3').show();
                            jQuery('#apiresponse').hide();
                        }

                        jQuery("#text1").text(response.message);
                        jQuery("#text2").text(response.code);
                        jQuery("#text3").text(response.response_payload);
                    },
                    error: function(xhr, status, error) {
                        jQuery('#spinner2').hide();
                        jQuery('#tickMark3').show();
                        jQuery('#apiresponse').hide();

                        jQuery("#text1").text(
                            "{{ __('transaction.error_occurred') }}");
                        jQuery("#text2").text('');
                        jQuery("#text3").text('');
                    }
                });
            }

            $(document).ready(function() {
                function fetchNotification() {
                    var letest_record = document.getElementById("letest_record").value;
                    $.ajax({
                        url: "{{ route('admin.payout-report.getnotification') }}",
                        type: "GET",
                        data: {
                            letest_record: letest_record
                        },
                        success: function(response) {
                            // console.log(response.message);
                            if (response.message === "success") {
                                var sound = document.getElementById("notification-sound");
                                const audio = new Audio();
                                audio.addEventListener("canplaythrough", () => {
                                    audio.play()
                                });
                                sound.play();
                                window.location.reload();
                            }

                        },
                        error: function(xhr) {
                            console.log('Error:', xhr.responseText);
                        }
                    });
                }
                // Run fetchNotification every 5 seconds (5000 milliseconds)
                setInterval(fetchNotification, 5000);
            });

            $(document).ready(function() {
                $('form').on('submit', function() {
                    const $form = $(this);
                    const $submitButton = $form.find('button[type="submit"]');
                    // Disable button and change text (optional)
                    $submitButton.prop('disabled', true);
                    $submitButton.html(
                        "<i class='fa fa-spinner fa-spin me-1'></i> {{ __('transaction.processing') }}");
                    // Allow form to proceed
                    return true;
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

            $(document).on("click", '.edit_button', function(e) {
                var id = $(this).data('id');
                $(".action_id").val(id);
                $(".actionRoute").attr('action', $(this).data('route'));

                var list = [];
                var ImgPath = "{{ asset(config('location.withdrawLog.path')) }}";
                console.log($(this).data('status'));
                if ($(this).data('status') == '2') {
                    $('#submit1').hide();
                    $('#submit2').show();
                    $('#submit3').show();
                } else if ($(this).data('status') == '3') {
                    $('#submit1').hide();
                    $('#submit2').hide();
                    $('#submit3').hide();
                } else {
                    $('#submit1').show();
                    $('#submit2').hide();
                    $('#submit3').show();
                }

                if ($(this).data('statusb') == 'Complete') {
                    $('#submit4').show();
                    $('#submit2').hide();
                } else {
                    $('#submit4').hide();
                }

                $('.addForm').html(`
                    <div class="form-group">
                        <label for="feedback">{{ __('feedback.remarks') }}</label>
                        <select class="form-control" name="feedback" id="feedback">
                            <option value="">{{ __('feedback.select_feedback') }}</option>
                            <option value="invalid_phone_number">{{ __('feedback.invalid_phone_number') }}</option>
                            <option value="account_limit_over">{{ __('feedback.account_limit_over') }}</option>
                            <option value="kyc_incomplete">{{ __('feedback.kyc_incomplete') }}</option>
                            <option value="nagad_server_down">{{ __('feedback.nagad_server_down') }}</option>
                            <option value="bkash_server_down">{{ __('feedback.bkash_server_down') }}</option>
                            <option value="rocket_server_down">{{ __('feedback.rocket_server_down') }}</option>
                            <option value="others">{{ __('feedback.others') }}</option>
                        </select>
                    </div>
                `);

                $('.withdraw-detail').html(list);
            });

            $(document).on("click", '.edit_buttonc', function(e) {
                var id = $(this).data('id');
                var e_wallet_phone_number = $(this).data('e_wallet_phone_number');

                $(".action_id").val(id);
                $(".e_wallet_phone_number").val(e_wallet_phone_number);
            });
            document.addEventListener("DOMContentLoaded", function() {
                const exportButton = document.querySelector('a[href*="admin.payout-log-export"]');

                if (exportButton) {
                    exportButton.addEventListener('click', function(e) {
                        e.preventDefault();
                        const originalText = exportButton.innerHTML;
                        exportButton.innerHTML =
                            '<i class="icon-base ti tabler-download me-1"></i> Exporting...';
                        exportButton.classList.add('disabled');

                        const form = document.querySelector('form[action*="admin.payout-log.search"]');
                        const formData = new FormData(form);
                        const queryParams = new URLSearchParams(formData).toString();
                        const exportUrl = exportButton.getAttribute('href');

                        fetch(`${exportUrl}?${queryParams}`, {
                                method: 'GET',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content'),
                                    'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                                }
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.blob();
                            })
                            .then(blob => {
                                const url = window.URL.createObjectURL(blob);
                                const a = document.createElement('a');
                                a.style.display = 'none';
                                a.href = url;
                                a.download =
                                    `payout_logs_${new Date().toLocaleString('en-GB', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true }).replace(/[,]/g, '').replace(/ /g, '_')}.xlsx`;
                                document.body.appendChild(a);
                                a.click();
                                window.URL.revokeObjectURL(url);
                                document.body.removeChild(a);

                                exportButton.innerHTML = originalText;
                                exportButton.classList.remove('disabled');
                            })
                            .catch(error => {
                                console.error('Error exporting Excel:', error);
                                alert('Failed to export data. Please try again.');
                                exportButton.innerHTML = originalText;
                                exportButton.classList.remove('disabled');
                            });
                    });
                }
            });
        </script>
    @endpush
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
    @endpush

</x-admin-layout>
