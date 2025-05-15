<x-admin-layout :title="$pageTitle">
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('admin.payout-log.search') }}" method="get">
            <div class="row justify-content-between align-items-center">
                <div class="col-md-4">
                    <div class="form-group">
                        <input type="text" name="name" value="{{ @request()->name }}" class="form-control"
                            placeholder="@lang('Email/ Username')">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <input type="text" name="partner_transection_id"
                            value="{{ @request()->partner_transection_id }}" class="form-control"
                            placeholder="@lang('Transection No.')">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <input type="date" class="form-control" value="{{ @request()->date_time }}" name="date_time"
                            id="datepicker" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <select name="status" class="form-control">
                            <option value="4">@lang('All Payment')</option>
                            <option value="1" @if (@request()->status == '1') selected @endif>@lang('Pending Payment')
                            </option>
                            <option value="2" @if (@request()->status == '2') selected @endif>@lang('Complete Payment')
                            </option>
                            <option value="3" @if (@request()->status == '3') selected @endif>@lang('Cancel Payment')
                            </option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <select name="domain" class="form-control">
                            <option value="">@lang('Select Domain')</option>
                            @foreach ($domains as $domain)
                                <option value="{{ $domain->id }}" @if (@request()->domain == $domain->id) selected @endif>
                                    {{ $domain->name }} ===> ( {{ $domain->website }} )</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group d-flex gap-5">
                        <button type="submit" class="btn btn-primary mt-2 "><i class="icon-base ti tabler-search me-1"></i>
                            @lang('Search')</button>
                        <button type="submit" name="export" value="export" class="btn btn-success mt-2"><i
                                class="icon-base ti tabler-download me-1"></i> @lang('Export Data')</button>
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
            <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">@lang('ID')</th>
                            <th scope="col">@lang('Date')</th>
                            <th scope="col">@lang('Trx Number')</th>
                            <th scope="col">@lang('Partner Trx Number')</th>
                            <th scope="col">@lang('Username')</th>
                            <th scope="col">@lang('Method')</th>
                            <th scope="col">@lang('Acc No.')</th>
                            <th scope="col">@lang('Amount')</th>
                            <th scope="col">@lang('Merchant Charge')</th>
                            <th scope="col">@lang('Net Amount')</th>
                            <th scope="col">@lang('Status')</th>
                            <th scope="col">@lang('Remarks')</th>
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
                            <td>{{ $item->id }}</td>
                            <td data-label="@lang('Date')"> {{ dateTime($item->created_at,'d M,Y H:i') }}</td>
                            <td data-label="@lang('Trx Number')" class="font-weight-bold text-uppercase">
                                {{ $item->trx_id }}<br>
                                <span class="text text-success">{{ $item->txn_id }}</span>

                            </td>
                            <td>{{ $item->partner_transection_id }}
                                <br>
                                {{ $item->member_id }}
                            </td>
                            <td data-label="@lang('Username')">

                                @if($item->api)
                                {{ optional($item->api)->name }} <b>({{ optional($item->api)->acc_type }})</b>
                                @else
                                Partner Transection
                                @endif

                            </td>
                            <td>{{ optional($item->gateway)->name }}</td>
                            <td>{{ $item->user_account_no }}</td>
                            <td data-label="@lang('Amount')" class="font-weight-bold">{{ getAmount($item->amount,2 ) }}
                                {{$basic->currency_symbol}}</td>
                            <td data-label="@lang('Charge')" class="text-success">
                                {{ getAmount(optional($item->payout)->charge,2) }} {{$basic->currency_symbol}}</td>

                            <td data-label="@lang('Net Amount')" class="font-weight-bold">
                                {{ getAmount($item->net_amount,2) }} {{$basic->currency_symbol}}</td>

                            <td data-label="@lang('Status')" class="text-lg-center text-right">
                                @if($item->status == 'Complete')
                                <span class="badge badge-light"><i class="fa fa-circle text-success font-12"></i>
                                    @lang('Request Approved')</span>
                                @elseif($item->status == 'inititate')
                                <span class="badge badge-light"><i class="fa fa-circle text-warning font-12"></i>
                                    @lang('Request Pending')</span>
                                @elseif($item->status == 'Reject')
                                <span class="badge badge-light"><i class="fa fa-circle text-danger font-12"></i>
                                    @lang('Request Rejected')</span>
                                @endif
                                <br>
                                @if($item->payout)
                                @if($item->payout->status == "Complete")
                                <span class="badge badge-light"><i class="fa fa-circle text-success font-12"></i>
                                    @lang('Transfered')</span>
                                @elseif($item->payout->status == "Pending")
                                <span class="badge badge-light"><i class="fa fa-circle text-warning font-12"></i>
                                    @lang('Transfer Pending')</span>
                                @elseif($item->payout->status == "Reject")
                                <span class="badge badge-light"><i class="fa fa-circle text-danger font-12"></i>
                                    @lang('Transfer Rejected')</span>
                                @else
                                {{$item->payout->status}}
                                @endif
                                @endif
                            </td>
                            <td>
                                {{$item->feedback}}
                            </td>
                            <td data-label="@lang('Method')">
                                {{ $item->e_wallet_phone_number }}
                                <br>
                                {{ $item->e_wallet_type }}
                            </td>
                            <td data-label="@lang('Method')">{{ $item->request_source }}</td>

                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="icon-base ti tabler-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <!-- active / deactive button here -->
                                        @if(adminAccessRoute(config('role.payout_manage.access.edit')))
                                        <button type="button" class="btn btn-sm edit_button" data-bs-toggle="modal"
                                            data-bs-target="#newModalb" onclick="setBalanceItem({{ $item->id }})">
                                            <i class="icon-base ti tabler-report-money me-1"></i> Send Callback
                                        </button><br>
                                        @if(isset($item))
                                        <button class="btn  edit_buttonc  btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#myModalc" data-title="Edit" data-id="{{ $item->id }}"
                                            data-e_wallet_phone_number="{{$item->e_wallet_phone_number}}">
                                            <i class="icon-base ti tabler-device-mobile  me-1"></i> Change E-Wallet No
                                        </button><br>
                                        @endif
                                    @endif

                                </td>
                                <td>{{ optional($item->method)->name }}</td>
                                <td>{{ $item->user_account_no }}</td>
                                <td data-label="@lang('Amount')" class="font-weight-bold">
                                    {{ getAmount($item->amount, 2) }}
                                    {{ $basic->currency_symbol }}</td>
                                <td data-label="@lang('Charge')" class="text-success">
                                    {{ getAmount(optional($item->payout)->charge, 2) }} {{ $basic->currency_symbol }}
                                </td>

                                <td data-label="@lang('Net Amount')" class="font-weight-bold">
                                    {{ getAmount($item->net_amount, 2) }} {{ $basic->currency_symbol }}</td>

                                <td data-label="@lang('Status')" class="text-lg-center text-right">
                                    @if ($item->status == 2)
                                        <span class="badge badge-light"><i
                                                class="fa fa-circle text-success font-12"></i>
                                            @lang('Request Approved')</span>
                                    @elseif($item->status == 1)
                                        <span class="badge badge-light"><i
                                                class="fa fa-circle text-warning font-12"></i>
                                            @lang('Request Pending')</span>
                                    @elseif($item->status == 3)
                                        <span class="badge badge-light"><i class="fa fa-circle text-danger font-12"></i>
                                            @lang('Request Rejected')</span>
                                    @endif
                                    <br>
                                    @if ($item->payout)
                                        @if ($item->payout->status == 'Complete')
                                            <span class="badge badge-light"><i
                                                    class="fa fa-circle text-success font-12"></i>
                                                @lang('Transfered')</span>
                                        @elseif($item->payout->status == 'Pending')
                                            <span class="badge badge-light"><i
                                                    class="fa fa-circle text-warning font-12"></i>
                                                @lang('Transfer Pending')</span>
                                        @elseif($item->payout->status == 'Reject')
                                            <span class="badge badge-light"><i
                                                    class="fa fa-circle text-danger font-12"></i>
                                                @lang('Transfer Rejected')</span>
                                        @else
                                            {{ $item->payout->status }}
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    {{ $item->feedback }}
                                </td>
                                <td data-label="@lang('Method')">
                                    {{ optional($item->payout)->e_wallet_phone_number }}
                                    <br>
                                    {{ optional($item->payout)->e_wallet_type }}
                                </td>
                                <td data-label="@lang('Method')">{{ optional($item->payout)->source }}</td>

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
                                                    <i class="icon-base ti tabler-report-money me-1"></i> Send Callback
                                                </button><br>
                                                @if (isset($item))
                                                    <button class="btn  edit_buttonc  btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#myModalc" data-title="Edit"
                                                        data-id="{{ $item->id }}"
                                                        data-e_wallet_phone_number="{{ $item->e_wallet_phone_number }}">
                                                        <i class="icon-base ti tabler-device-mobile  me-1"></i> Change
                                                        E-Wallet No
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
                                                    data-status="{{ $item->status }}"
                                                    data-statusb="{{ $item->status ? $item->status : '' }}">
                                                    @if (Request::routeIs('admin.payout-request'))
                                                        <i class="icon-base ti tabler-pencil me-1"></i> Edit
                                                    @else
                                                        <i class="icon-base ti tabler-eye me-1"></i>View
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
                                    <p class="text-dark">@lang('No Data Found')</p>
                                </td>
                            </tr>

                        @endforelse
                    </tbody>
                </table>
                {{ $records->appends($_GET)->links('partials.pagination') }}
            </div>
        </div>
    </div>

    <div class="modal modal-top fade" id="myModalc" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTopTitle">@lang('Change E-Wallet No.')</h5>
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

                            <label>E-Wallet No.</label>
                            <input class="form-control e_wallet_phone_number" required name="e_wallet_phone_number"
                                type="text" />
                            <button type="submit" class="btn btn-primary mt-3" name="status"
                                value="1">@lang('Change')</button>
                        </div>
                        <input type="hidden" class="action_id" name="id">
                    </div>
                </form>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">@lang('Close')</button>
                </div>

            </div>
        </div>
    </div>



    <div class="modal modal-top fade" id="newModalb" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTopTitle">@lang('Send Callback')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addBalanceForm" action="{{ route('admin.run.callback') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row justify-content-between align-items-center">



                            <input type="text" hidden id="account_id" class="form-control" name="id">



                            <div class="col-md-12">
                                Callback Status
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
                                <p>Message: <span id="text1"></span></p>
                                <br>
                                <div id="apiresponse" style="display: none;">
                                    <h4>Response</h4>
                                    <p>Response Code: <span id="text2"></span></p>
                                    <p>Response Body: </p>
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
                    <h5 class="modal-title" id="modalTopTitle">@lang('Payout Information')</h5>
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')
                        </button>

                        <input type="hidden" class="action_id" name="id">
                        <div id="submit1" style="display: none;">
                            <button type="submit" id="btn2" class="btn btn-primary" name="status"
                                value="2">@lang('Approve')</button>
                        </div>
                        <div id="submit2" style="display: none;">
                            <button type="submit" id="btn4" class="btn btn-dark" name="status"
                                value="4">@lang('Mark As
                                                                Complete')</button>
                        </div>
                        <div id="submit4" style="display: none;">
                            <button type="submit" id="btn5" class="btn btn-warning" name="status"
                                value="5">@lang('Mark
                                                                As Pending')</button>
                        </div>
                        <div id="submit3" style="display: none;">
                            <button type="submit" id="btn3" class="btn btn-danger" name="status"
                                value="3">@lang('Reject')</button>
                        </div>

                    </div>

                </form>


            </div>
        </div>
    </div>

    @push('js')
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
                            alert("Please select an issue before proceeding.");
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
        </script>

        <script>
            (function(jQuery) {

                jQuery(document).ready(function() {
                    jQuery(document).on("click", '.edit_button', function(e) {
                        var id = jQuery(this).data('id');
                        jQuery(".action_id").val(id);
                        jQuery(".actionRoute").attr('action', jQuery(this).data('route'));
                        // var details = Object.entries(jQuery(this).data('info'));
                        var list = [];
                        var ImgPath = "{{ asset(config('location.withdrawLog.path')) }}";
                        // details.map(function (item, i) {
                        //     if (item[1].type == 'file') {
                        //         var singleInfo = `<br><img src="${ImgPath}/${item[1].field_name}" alt="..." class="w-50">`;
                        //     } else {
                        //         var singleInfo = `<span class="font-weight-bold ml-3">${item[1].field_name}</span>`;
                        //     }
                        //     list[i] = `<li class="list-group-item"><span class="font-weight-bold">${item[0].replace('_', " ")}</span> : ${singleInfo}</li>`;
                        // });

                        console.log(jQuery(this).data('status'));

                        if (jQuery(this).data('status') == '2') {
                            jQuery('#submit1').hide();
                            jQuery('#submit2').show();
                            jQuery('#submit3').show();
                        } else if (jQuery(this).data('status') == '3') {
                            jQuery('#submit1').hide();
                            jQuery('#submit2').hide();
                            jQuery('#submit3').hide();
                        } else {
                            jQuery('#submit1').show();
                            jQuery('#submit2').hide();
                            jQuery('#submit3').show();
                        }

                        if (jQuery(this).data('statusb') == 'Complete') {
                            jQuery('#submit4').show();
                            jQuery('#submit2').hide();
                        } else {
                            jQuery('#submit4').hide();
                        }

                        // list[details.length + 1] = ``;

                        jQuery('.addForm').html(`
                    <div class="form-group">
                        <label for="feedback">@lang('Remarks')</label>
                        <select class="form-control" name="feedback" id="feedback">
                            <option value="">@lang('Select Feedback')</option>
                            <option value="invalid_phone_number">@lang('Invalid phone number')</option>
                            <option value="account_limit_over">@lang('Account limit over')</option>
                            <option value="kyc_incomplete">@lang('Customer account did not complete KYC')</option>
                            <option value="nagad_server_down">@lang('Nagad server down')</option>
                            <option value="bkash_server_down">@lang('bKash server down')</option>
                            <option value="rocket_server_down">@lang('Rocket server down')</option>
                            <option value="others">@lang('Others')</option>
                        </select>
                    </div>
                `);

                        jQuery('.withdraw-detail').html(list);
                    });
                });

                jQuery(document).on("click", '.edit_buttonc', function(e) {
                    var id = jQuery(this).data('id');
                    var e_wallet_phone_number = jQuery(this).data('e_wallet_phone_number');

                    jQuery(".action_id").val(id);
                    jQuery(".e_wallet_phone_number").val(e_wallet_phone_number);
                });

            })(jQuery);

            jQuery(document).ready(function() {
                jQuery('select').select2({
                    selectOnClose: true
                });
            });
        </script>


        <script>
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
                            'An error occurred while processing your request. Please try again.');
                        jQuery("#text2").text('');
                        jQuery("#text3").text('');
                    }
                });
            }
        </script>

        <script>
            $(document).ready(function() {
                var intervalId; // To store the interval id
                var orderid = document.getElementById("orderid");
                var wid = document.getElementById("wid");
                var acc_no = document.getElementById("acc_no");



                $('#runWithdrawalTest').click(function() {
                    if (acc_no.value === "") {
                        alert("Please select an Admin Account");
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
        </script>

        <script>
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
        </script>
    @endpush

</x-admin-layout>
