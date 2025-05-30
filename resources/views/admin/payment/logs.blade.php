<x-admin-layout :title="$pageTitle">
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('admin.payment.search') }}" method="get">
            <div class="row justify-content-between align-items-center">
                <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
                <div class="col-md-4">
                    <div class="form-group">
                        <input type="text" name="name" value="{{@request()->name}}" class="form-control"
                            placeholder="@lang('Username OR Email')">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <input type="text" name="partner_transection_id" value="{{@request()->partner_transection_id}}"
                            class="form-control" placeholder="@lang('Transection No.')">
                    </div>
                </div>

                <div class="col-md-4 mt-3">
                    <div class="form-group">
                        <select name="status" class="form-select">
                            <option value="All" @if(@request()->status == 'All') selected @endif>@lang('All Payment')
                            </option>
                            <option value="Complete" @if(@request()->status == 'Complete') selected @endif>@lang('Complete Payment')
                            </option>
                            <option value="Pending" @if(@request()->status == 'Pending') selected @endif>@lang('Pending Payment')
                            </option>
                            <option value="Reject" @if(@request()->status == 'Reject') selected @endif>@lang('Cancel Payment')
                            </option>
                            <option value="99" @if(@request()->status == '99') selected @endif>@lang('Member did not
                                complete')</option>
                        </select>
                    </div>
                </div>


                <div class="col-md-4 mt-3">
                    <div class="form-group">
                        <input type="date" class="form-control" value="{{@request()->date_time}}" name="date_time"
                            id="datepicker" />
                    </div>
                </div>

                <div class="col-md-4 mt-3">
                    <div class="form-group">
                        <!--<label>Partner</label>-->
                        <select name="website" class="form-select select2" data-allow-clear="true"
                            data-placeholder="Select Partner">
                            <option></option>
                            <option value="">All Source</option>
                            @foreach($domains as $partner)
                            <option value="{{ $partner->id }}" @if(@request()->website == $partner->id) selected
                                @endif>{{ $partner->name }} ===> ( {{ $partner->website }} )</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4 mt-5">
                    <div class="form-group d-flex gap-5">
                        <button type="submit" class="btn btn-primary"><i
                                class="icon-base ti tabler-search"></i> @lang('Search')</button>&nbsp;
                        <button type="submit" name="export" value="export" class="btn btn-success mt-1"><i
                                class="icon-base ti tabler-download"></i> @lang('Export Data')</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Add these lines to your HTML header section -->
    <link rel="stylesheet" href="{{ asset('node_modules/@fancyapps/fancybox/dist/jquery.fancybox.min.css') }}">
    <script src="{{ asset('node_modules/@fancyapps/fancybox/dist/jquery.fancybox.min.js') }}"></script>
    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">@lang('Date')</th>
                            <th scope="col">@lang('Trx Number')</th>
                            <th scope="col">@lang('Partner Trx No')</th>
                            <th scope="col">@lang('Partner Txn Input')</th>
                            <th scope="col">@lang('Username')</th>
                            <th scope="col">@lang('Method')</th>
                            <th scope="col">Acc. No.</th>
                            <th scope="col">@lang('Amount')</th>
                            <th scope="col">@lang('Merchant Charge')</th>
                            <th scope="col">@lang('Final Amount')</th>
                            <th scope="col">@lang('Status')</th>
                            <th scope="col">@lang('Source')</th>
                            <th scope="col">Completed At</th>
                            <th scope="col">@lang('Receipt')</th>
                            @if(adminAccessRoute(config('role.payment_log.access.edit')))
                            <th scope="col">@lang('Action')</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($funds as $key => $fund)
                        <tr>
                            <td data-label="@lang('Date')"> {{ dateTime($fund->created_at,'d M,Y H:i') }}</td>
                            <td data-label="@lang('Trx Number')" class="font-weight-bold text-uppercase">
                                {{ $fund->transaction }}<br>
                                <span class="text text-success">{{ $fund->txn_id }}</span>

                            </td>
                            <td>{{ !empty($fund->partner_transection_id)?$fund->partner_transection_id:'' }}
                                <br>
                                {{ !empty($fund->member_id)?$fund->member_id:'' }}
                            </td>

                            <td>
                                {{ !empty($fund->txn_record)? $fund->txn_record->txn_no : '' }}
                            </td>

                            <td data-label="@lang('Username')">
                                @if(optional($fund->user)->username && optional($fund->user)->username !== 'dummyuser')
                                <a href="{{route('admin.user-edit', $fund->user_id)}}" target="_blank">
                                    <div class="d-lg-flex d-block align-items-center ">
                                        <div class="mr-3"><img
                                                src="{{getFile(config('location.user.path').optional($fund->user)->image) }}"
                                                alt="user" class="rounded-circle" width="45" height="45"></div>
                                        <div class="">
                                            <h5 class="text-dark mb-0 font-16 font-weight-medium">{{
                                                optional($fund->user)->username }}</h5>
                                            <span class="text-muted font-14">{{ optional($fund->user)->email }}</span>
                                        </div>
                                    </div>
                                </a>
                                @elseif($fund->source=="Admin Test")
                                Admin Test
                                @else
                                {{ optional($fund->api)->name }} <b>({{ optional($fund->api)->acc_type }})</b>
                                @endif
                            </td>
                            <td data-label="@lang('Method')">{{ optional($fund->gateway)->name }}</td>
                            <td class="font-weight-bold">{{ $fund->sender }}</td>
                            <td data-label="@lang('Amount')" class="font-weight-bold">{{ getAmount($fund->amount) }}
                                {{$fund->gateway?->currency}}</td>
                            <td data-label="@lang('Charge')" class="text-success">{{ getAmount($fund->charge) }}
                                {{$fund->gateway?->currency}}</td>
                            <td data-label="@lang('Payable')" class="font-weight-bold">
                                {{ getAmount($fund->amount) - getAmount($fund->charge) }} {{$fund->gateway?->currency}}
                            </td>

                            <td data-label="@lang('Status')" class="text-lg-center text-right">
                                @if($fund->status == 'Pending')
                                    @php
                                    // Get the time difference between now and the created_at timestamp
                                    $createdAt = \Carbon\Carbon::parse($fund->created_at);
                                    $currentTime = \Carbon\Carbon::now();
                                    $diffInMinutes = $createdAt->diffInMinutes($currentTime);
                                    @endphp

                                    @if($diffInMinutes > 10 && @request()->status != 'Pending')
                                    <span class="badge bg-warning">
                                        <i class="fa fa-circle text-white warning font-12"></i>
                                        @lang('Member did not complete')
                                    </span>
                                    @else
                                    <span class="badge bg-info">
                                        <i class="fa fa-circle text-white font-12"></i>
                                        @lang('Pending')
                                    </span>
                                    @endif
                                <br>
                                <span class="text text-primary">{{ $fund->e_wallet_phone_number }}</span>
                                @elseif($fund->status == "Complete")

                                @php
                                // Check if the fund has a payment and if completed_source is set
                                if ($fund->completed_source != "AdminPanel") {
                                    // Dynamically assign the class based on completed_source
                                    // if ($fund->payment->completed_source != "AdminPanel") {
                                    $classColor = "bg-success";
                                    // } else {
                                    // $classColor = "text-purple purple ";
                                    // }
                                } else {
                                    $classColor = "bg-primary";
                                }
                                @endphp


                                <span class="badge {{ $classColor }}"><i class="fa fa-circle text-white font-12"></i>
                                    @lang('Completed')</span>
                                <br>
                                <span class="{{ $classColor }}">{{ $fund->e_wallet_phone_number
                                    }}</span>
                                @elseif($fund->status == "Reject")
                                <span class="badge bg-danger"><i class="fa fa-circle text-white danger font-12"></i>
                                    @lang('Rejected')</span>
                                <br>
                                <span class="text text-danger"> {{ $fund->e_wallet_phone_number }}</span>
                                @endif
                            </td>
                            <td data-label="@lang('Method')">
                                {{ optional($fund->api)->website }}
                                <br>
                                @if(!empty($fund->request_source))
                                <span class="text text-dark">({{ $fund->request_source }})</span>
                                @endif
                            </td>
                            <td>{{ $fund->created_at }}</td>
                            <td>
                                @if(!empty($fund->receipt_image))
                                <a data-fancybox="images"
                                    href="{{ getFile(config('location.receipts.path').$fund->receipt_image) }}">
                                    <h2><i class="fa fa-file"></i></h2>
                                </a>
                                @endif
                            </td>

                            @if(adminAccessRoute(config('role.payment_log.access.edit')))
                            <td data-label="@lang('Action')">
                                @php
                                if($fund->detail){
                                    $details =[];
                                    foreach($fund->detail as $k => $v){
                                    if($v->type == "file"){
                                    $details[kebab2Title($k)] = [
                                    'type' => $v->type,
                                    'field_name' =>
                                    getFile(config('location.deposit.path').date('Y',strtotime($fund->created_at)).'/'.date('m',strtotime($fund->created_at)).'/'.date('d',strtotime($fund->created_at))
                                    .'/'.$v->field_name)
                                    ];
                                }
                                else{
                                    $details[kebab2Title($k)] =[
                                    'type' => $v->type,
                                    'field_name' => $v->field_name
                                    ];
                                    }
                                }
                                }else{
                                $details = null;
                                }
                                @endphp

                                {{-- @if($fund->gateway_id > 999) --}}
                                <button
                                    class="edit_button  btn  {{($fund->status == "Pending") ?  'btn-primary' : 'btn-success'}} text-white  btn-sm "
                                    data-bs-toggle="modal" data-bs-target="#myModal"
                                    data-title="{{($fund->status == "Pending") ?  trans('Edit') : trans('Details')}}"
                                    data-id="{{ $fund->id }}" data-feedback="{{ $fund->feedback }}"
                                    data-info="{{json_encode($details)}}"
                                    data-amount="{{ getAmount($fund->amount)}} {{ $basic->currency }}"
                                    data-username="{{ optional($fund->user)->username }}"
                                    data-route="{{route('admin.payment.action',$fund->id)}}"
                                    data-status="{{$fund->status}}" data-sender="{{$fund->sender}}"
                                    data-e_wallet_phone_number="{{$fund->e_wallet_phone_number}}">

                                    @if(($fund->status == "Pending"))
                                    <i class="icon-base ti tabler-pencil me-1"></i>
                                    @else
                                    <i class="icon-base ti tabler-eye me-1"></i>
                                    @endif

                                </button>
                                {{-- @else --}}
                                {{-- - --}}
                                {{-- @endif --}}
                                <button class="edit_buttonc  btn btn-danger text-white  btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#myModalc" data-bs-title="Edit" data-id="{{ $fund->id }}"
                                    data-e_wallet_phone_number="{{$fund->e_wallet_phone_number}}">
                                    <i class="icon-base ti tabler-device-mobile me-1"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-dark" data-bs-toggle="modal"
                                    data-bs-target="#newModalb" onclick="setBalanceItem({{ $fund->id }})">
                                    <i class="icon-base ti tabler-direction-sign me-1"></i>
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
                    <h5 class="modal-title" id="modalTopTitle">@lang('Deposit Information')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                    <?php
                        date_default_timezone_set('Asia/Kuala_Lumpur');

                    ?>
                {{-- <form role="form" class="actionRoute" action=""> --}}
                    <form role="form" method="POST" class="actionRoute" action="" enctype="multipart/form-data"
                        onsubmit="submitForm(this)">
                        @csrf
                        @method('put')
                        <div class="modal-body">
                            <ul class="list-group withdraw-detail">
                            </ul>

                            <div class="get-feedback">
                                <label>Sender Acc. No.</label>
                                <input class="form-control sender" name="sender" type="text" />
                                <label>E-Wallet No.</label>
                                <input class="form-control e_wallet_phone_number" required name="e_wallet_phone_number"
                                    type="text" />
                                <label>Txn No.</label>
                                <input class="form-control" name="txn_id" type="text" />
                                <label>E-Wallet Type</label>
                                <select class="form-select" name="e_wallet_type">
                                    <option value="Personal">Personal</option>
                                    <option value="Merchant">Merchant</option>
                                </select>
                                <input type="hidden" name="status" value="Complete">
                                <label>Payment Receiving DateTime.</label>
                               <input class="form-control" id="e_wallet_phone_number" required
                                    value="<?php echo date('Y-m-d\TH:i'); ?>"
                                    name="date_time" type="datetime-local" />
                                <button type="submit" class="btn btn-primary mt-2" id="approvebtn" name="status"
                                    value="Complete">@lang('Approve')</button>
                            </div>

                            <input type="hidden" class="action_id" name="id">
                        </div>
                    </form>
                    <form role="form" method="POST" class="actionRoute" action="" enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">@lang('Close')</button>
                            @if(Request::routeIs('admin.payment.pending'))
                            <!-- // -->
                            @endif
                            <input type="hidden" class="action_id" name="id">
                            <input type="hidden" name="status" value="Reject">
                            <button type="submit" class="btn btn-danger" name="status"
                                value="Reject">@lang('Reject')</button>

                        </div>
                    </form>
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
                <form role="form" method="POST" action="{{ route('admin.payment.update_e_wallet') }}">
                    @csrf
                    @method('put')
                    <div class="modal-body">
                        <ul class="list-group withdraw-detail">
                        </ul>

                        <div class="get-feedback">

                            <label>E-Wallet No.</label>
                            <input class="form-control e_wallet_phone_number" required name="e_wallet_phone_number"
                                type="text" />
                            <button type="submit" class="btn btn-primary mt-2" name="status"
                                value="1">@lang('Change')</button>
                        </div>
                        <input type="hidden" class="action_id" name="id">
                    </div>
                </form>
                <form role="form" method="POST" class="actionRoute" action="" enctype="multipart/form-data">
                    @csrf
                    @method('put')
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                    </div>
                </form>
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
                <form id="addBalanceForm" action="{{ route('admin.run.deposit.callback') }}" method="POST">
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

    @push('js')
    <script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
    <script>
        function submitForm(form) {
            // Disable the submit button to prevent multiple submissions
            document.getElementById('approvebtn').disabled = true;

            // Submit the form
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
            // var seconds = date.getSeconds().toString().padStart(2, '0');

            var formattedDateTimeKL = `${year}-${month}-${day} ${hours}:${minutes}`;

            // console.log('ok');

            var currentDateTime = new Date(currentDateTimeKL).getTime();
            var twoMinutesAgoTimestamp = currentDateTime - (2 * 60 * 1000);
            if (inputDateTime > twoMinutesAgoTimestamp) {
                // console.log('ok');
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

                    jQuery(".action_id").val(id);
                    jQuery(".sender").val(sender);
                    jQuery(".e_wallet_phone_number").val(e_wallet_phone_number);
                    jQuery(".actionRoute").attr('action', jQuery(this).data('route'));

                    var details = Object.entries(jQuery(this).data('info'));
                    var list = [];
                    details.map(function(item, i) {
                        if (item[1].type == 'file') {
                            var singleInfo = `<br><img src="${item[1].field_name}" alt="..." class="w-100">`;
                        } else {
                            var singleInfo = `<span class="font-weight-bold ml-3">${item[1].field_name}</span>`;
                        }
                        list[i] = ` <li class="list-group-item"><span class="font-weight-bold"> ${item[0].replace('_', " ")} </span> : ${singleInfo}</li>`;
                    });
                    jQuery('.withdraw-detail').html(list);

                    if (feedback == '') {
                        var res = `<div class="form-group"><br>
                                        <label class="font-weight-bold">{{trans('Send You Feedback')}}</label>
                                        <textarea name="feedback" class="form-control" row="3" required>{{old('feedback')}}</textarea>
                                </div>`;
                    } else {
                        var res = `<h5>{{trans('Feedback')}}</h5>
                                    <p>${feedback}</p>`;
                    }

                    jQuery('.get-feedback').html(res);
            });

        });
        jQuery(document).on("click", ".edit_buttonc", function (e) {
            e.preventDefault();

            var id = jQuery(this).data("bs-id");
            var e_wallet_phone_number = jQuery(this).data("bs-e_wallet_phone_number");

            console.log("Edit clicked:", id, e_wallet_phone_number);

            jQuery(".action_id").val(id);
            jQuery(".e_wallet_phone_number").val(e_wallet_phone_number);
        });

        function setBalanceItem(itemId)
        {
            var account_id = document.getElementById("account_id");
            account_id.value = itemId;

            jQuery('#spinner2').show();
            jQuery('#runWithdrawalTest').prop('disabled', true);

            var formData = new FormData(jQuery('#addBalanceForm')[0]); // Get form data

            jQuery.ajax({
                type: "POST",
                url: "{{ route('admin.run.deposit.callback') }}",
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                data: formData, // Use FormData object
                processData: false, // Don't process the data
                contentType: false, // Don't set contentType
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

                    document.getElementById("text1").innerText = 'An error occurred while processing your request. Please try again.';
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



        $(document).ready(function () {
        $('form').on('submit', function () {
            const $form = $(this);
            const $submitButton = $form.find('button[type="submit"]');

            // Disable button and change text (optional)
            $submitButton.prop('disabled', true);
            $submitButton.html('<i class="fa fa-spinner fa-spin me-1"></i> @lang("Processing...")');

            // Allow form to proceed
            return true;
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
    @push('styles')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}">
    @endpush

     @push('js')
    <script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
    <script>
        jQuery(document).ready(function () {

            jQuery(document).on("click", '.edit_button', function (e) {
                var id = jQuery(this).data('id');
                var feedback = jQuery(this).data('feedback');
                var status = jQuery(this).data('status');
                $('#payment_status').val(status);
                // if(status == "Pending")
                // {
                //     $('#showBtns').show();
                // }
                // else
                // {
                //     $('#showBtns').hide();
                // }

                jQuery(".action_id").val(id);
                jQuery(".actionRoute").attr('action', jQuery(this).data('route'));
                if(details != null){
                    var details = Object.entries(jQuery(this).data('info'));
                    var list = [];
                        details.map(function (item, i) {
                            if (item[1].type == 'file') {
                                var singleInfo = `<br><img src="${item[1].field_name}" alt="..." class="w-100">`;
                            } else {
                                var singleInfo = `<span class="font-weight-bold ml-3">${item[1].field_name}</span>`;
                            }
                            list[i] = `<li class="list-group-item"><span class="font-weight-bold "> ${item[0].replace('_', " ")} </span> : ${singleInfo}</li>`
                        });
                }
                jQuery('.withdraw-detail').html(list);

                if (feedback == '') {
                    // var res = `<div class="form-group"><br>
                    //             <label class="font-weight-bold">{{trans('Send You Feedback')}}</label>
                    //             <textarea name="feedback" class="form-control" row="3" required>{{old('feedback')}}</textarea>
                    //         </div>`;
                    var res="";
                } else {
                    var res = `<h5>{{trans('Feedback')}}</h5>
                    <p>${feedback}</p>`;
                }

                jQuery('.get-feedback').html(res);
            });
        });

    </script>

    <script>
        $(document).ready(function () {
        $('form').on('submit', function () {
            const $form = $(this);
            const $submitButton = $form.find('button[type="submit"]');

            // Disable button and change text (optional)
            $submitButton.prop('disabled', true);
            $submitButton.html('<i class="fa fa-spinner fa-spin me-1"></i> @lang("Processing...")');

            // Allow form to proceed
            return true;
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
