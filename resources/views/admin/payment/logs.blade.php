<x-admin-layout :title="$pageTitle">
<div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
    <form action="{{ route('admin.payment.search') }}" method="get">
        <div class="row justify-content-between align-items-center">
            <div class="col-md-4">
                <div class="form-group">
                    <input type="text" name="name" value="{{@request()->name}}" class="form-control" placeholder="@lang('Username OR Email')">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <input type="text" name="partner_transection_id" value="{{@request()->partner_transection_id}}" class="form-control" placeholder="@lang('Transection No.')">
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <select name="status" class="form-control">
                        <option value="4" @if(@request()->status == '4') selected @endif>@lang('All Payment')</option>
                        <option value="1" @if(@request()->status == '1') selected @endif>@lang('Complete Payment')</option>
                        <option value="2" @if(@request()->status == '2') selected @endif>@lang('Pending Payment')</option>
                        <option value="3" @if(@request()->status == '3') selected @endif>@lang('Cancel Payment')</option>
                        <option value="99" @if(@request()->status == '99') selected @endif>@lang('Member did not complete')</option>
                    </select>
                </div>
            </div>


            <div class="col-md-5">
                <div class="form-group">
                    <input type="date" class="form-control" value="{{@request()->date_time}}" name="date_time" id="datepicker" />
                </div>
            </div>

            <div class="col-md-5">
                <div class="form-group">
                    <!--<label>Partner</label>-->
                    <select name="website" class="form-control">
                        <option value="">All Source</option>
                        @foreach($domains as $partner)
                        <option value="{{ $partner->id }}"
                                @if(@request()->website == $partner->id) selected @endif>{{ $partner->name }} ===> ( {{ $partner->website }} )</option>
                        @endforeach
                    </select>
                </div>
            </div>


            <div class="col-md-2">
                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-search me-1"></i> @lang('Search')</button>
                    <button type="submit" name="export" value="export" class="btn btn-success mt-1"><i class="icon-base ti tabler-download me-1"></i> @lang('Export Data')</button>
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
                            <span class="text text-success">{{ optional($fund->payment)->txn_id }}</span>

                        </td>
                        <td>{{ !empty($fund->partner_transection_id)?$fund->partner_transection_id:'' }}
                            <br>
                            {{ !empty($fund->member_id)?$fund->member_id:'' }}
                        </td>

                        <td>
                            {{ !empty($fund->txn_record)? $fund->txn_record->txn_no : '' }}
                        </td>

                        <td data-label="@lang('Username')">
                            @if(optional($fund->user)->username!="dummyuser")
                            <a href="{{route('admin.user-edit', $fund->user_id)}}" target="_blank">
                                <div class="d-lg-flex d-block align-items-center ">
                                    <div class="mr-3"><img src="{{getFile(config('location.user.path').optional($fund->user)->image) }}" alt="user" class="rounded-circle" width="45" height="45"></div>
                                    <div class="">
                                        <h5 class="text-dark mb-0 font-16 font-weight-medium">{{ optional($fund->user)->username }}</h5>
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
                        <td class="font-weight-bold">{{ $fund->account_no }}</td>
                        <td data-label="@lang('Amount')" class="font-weight-bold">{{ getAmount($fund->amount) }} {{$fund->gateway_currency}}</td>
                        <td data-label="@lang('Charge')" class="text-success">{{ getAmount($fund->charge) }} {{$fund->gateway_currency}}</td>
                        <td data-label="@lang('Payable')" class="font-weight-bold">{{ getAmount($fund->final_amount) }} {{$fund->gateway_currency}}</td>


                        <td data-label="@lang('Status')" class="text-lg-center text-right">
                            @if($fund->status == 2)
                                @php
                                    // Get the time difference between now and the created_at timestamp
                                    $createdAt = \Carbon\Carbon::parse($fund->created_at);
                                    $currentTime = \Carbon\Carbon::now();
                                    $diffInMinutes = $createdAt->diffInMinutes($currentTime);
                                @endphp

                                @if($diffInMinutes > 10 && @request()->status != 2)
                                    <span class="badge badge-light">
                                        <i class="fa fa-circle text-warning warning font-12"></i>
                                        @lang('Member did not complete')
                                    </span>
                                @else
                                    <span class="badge badge-light">
                                        <i class="fa fa-circle text-warning warning font-12"></i>
                                        @lang('Pending')
                                    </span>
                                @endif
                            <br>
                            <span class="text text-primary">{{ $fund->e_wallet_phone_number }}</span>
                            @elseif($fund->status == 1)

                                @php
                                    // Check if the fund has a payment and if completed_source is set
                                    if ($fund->payment && isset($fund->payment->completed_source)) {
                                        // Dynamically assign the class based on completed_source
                                        if ($fund->payment->completed_source != "AdminPanel") {
                                            $classColor = "text-success success";
                                        } else {
                                            $classColor = "text-purple purple ";
                                        }
                                    } else {
                                        $classColor = "text-purple purple ";
                                    }
                                @endphp


                            <span class="badge badge-light"><i class="fa fa-circle {{ $classColor }} font-12"></i> @lang('Completed')</span>
                            <br>
                            <span class="{{ $classColor }}">{{ optional($fund->payment)->e_wallet_phone_number }}</span>
                            @elseif($fund->status == 3)
                            <span class="badge badge-light"><i class="fa fa-circle text-danger danger font-12"></i> @lang('Rejected')</span>
                            <br>
                            <span class="text text-danger"> {{ $fund->e_wallet_phone_number }}</span>
                            @endif
                        </td>
                        <td data-label="@lang('Method')">
                            {{ optional($fund->api)->website }}
                            <br>
                            @if(!empty($fund->source))
                            <span class="text text-dark">({{ $fund->source }})</span>
                            @endif
                        </td>
                        <td>{{ optional($fund->payment)->completion_at }}</td>
                        <td>
                            @if(!empty($fund->receipt_image))
                            <a data-fancybox="images" href="{{ getFile(config('location.receipts.path').$fund->receipt_image) }}">
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
                            'field_name' => getFile(config('location.deposit.path').date('Y',strtotime($fund->created_at)).'/'.date('m',strtotime($fund->created_at)).'/'.date('d',strtotime($fund->created_at)) .'/'.$v->field_name)
                            ];
                            }else{
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

                            @if($fund->gateway_id > 999)
                            <button class="edit_button  btn  {{($fund->status == 2) ?  'btn-primary' : 'btn-success'}} text-white  btn-sm " data-bs-toggle="modal"
                                 data-bs-target="#myModal"
                                  data-title="{{($fund->status == 2) ?  trans('Edit') : trans('Details')}}"
                                   data-id="{{ $fund->id }}" data-feedback="{{ $fund->feedback }}" data-info="{{json_encode($details)}}"
                                   data-amount="{{ getAmount($fund->amount)}} {{ $basic->currency }}"
                                   data-username="{{ optional($fund->user)->username }}"
                                    data-route="{{route('admin.payment.action',$fund->id)}}"
                                    data-status="{{$fund->status}}" data-sender="{{$fund->account_no}}"
                                     data-e_wallet_phone_number="{{$fund->e_wallet_phone_number}}">

                                @if(($fund->status == 2))
                               <i class="icon-base ti tabler-pencil me-1"></i>
                                @else
                                <i class="fa fa-eye"></i>
                                @endif

                            </button>
                            @else
                            -
                            @endif
                            <button class="edit_buttonc  btn btn-danger text-white  btn-sm" data-bs-toggle="modal" data-bs-target="#myModalc" data-bs-title="Edit" data-bs-id="{{ $fund->id }}" data-bs-e_wallet_phone_number="{{$fund->e_wallet_phone_number}}">
                               <i class="icon-base ti tabler-device-mobile me-1"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#newModalb" onclick="setBalanceItem({{ $fund->id }})">
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
            {{ $funds->appends($_GET)->links('partials.pagination') }}
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
            <form role="form" class="actionRoute" action="{{route('admin.payment.action',1)}}">
                @csrf
                @method('put')
                <div class="modal-body">
                    <ul class="list-group withdraw-detail">
                    </ul>

                    <div class="get-feedback">
                        <label>Sender Acc. No.</label>
                        <input class="form-control sender" name="sender" type="text" />
                        <label>E-Wallet No.</label>
                        <input class="form-control e_wallet_phone_number" required name="e_wallet_phone_number" type="text" />
                        <label>Txn No.</label>
                        <input class="form-control" name="txn_id" type="text" />
                        <label>E-Wallet Type</label>
                        <select class="form-control" name="e_wallet_type">
                            <option value="Personal">Personal</option>
                            <option value="Merchant">Merchant</option>
                        </select>
                        <input type="hidden" name="status" value="1">
                        <label>Payment Receiving DateTime.</label>
                        <input class="form-control" id="e_wallet_phone_number" required value="<?php echo date("Y-m-d H:i"); ?>" name="date_time" type="datetime-local" />
                        <button type="submit" class="btn btn-primary mt-2" id="approvebtn" name="status" value="1">@lang('Approve')</button>
                    </div>

                    <input type="hidden" class="action_id" name="id">
                </div>
            </form>
            <form role="form" method="POST" class="actionRoute" action="" enctype="multipart/form-data">
                @csrf
                @method('put')
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                    @if(Request::routeIs('admin.payment.pending'))
                    <!-- // -->
                    @endif
                    <input type="hidden" class="action_id" name="id">
                    <button type="submit" class="btn btn-danger" name="status" value="3">@lang('Reject')</button>

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
                        <input class="form-control e_wallet_phone_number" required name="e_wallet_phone_number" type="text" />
                        <button type="submit" class="btn btn-primary mt-2" name="status" value="1">@lang('Change')</button>
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
                            <div style="background-color: black;color:white;padding:10px"><span id="text3"></span></div>
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
{{-- <script>
function submitForm(form) {
    // Disable the submit button to prevent multiple submissions
    document.getElementById('approvebtn').disabled = true;

    // Submit the form
    form.submit();
}
</script> --}}
{{-- <script>
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
</script> --}}

<script>

    $(document).ready(function() {
        // $('select[name=status]').select2({
        //     selectOnClose: true
        // });

        // $(document).on("click", '.edit_button', function(e) {
        //     var id = $(this).data('id');
        //     var sender = $(this).data('sender');
        //     var feedback = $(this).data('feedback');
        //     var e_wallet_phone_number = $(this).data('e_wallet_phone_number');

        //     $(".action_id").val(id);
        //     $(".sender").val(sender);
        //     $(".e_wallet_phone_number").val(e_wallet_phone_number);
        //     $(".actionRoute").attr('action', $(this).data('route'));
        //     var details = Object.entries($(this).data('info'));
        //     var list = [];
        //     details.map(function(item, i) {
        //         if (item[1].type == 'file') {
        //             var singleInfo = `<br><img src="${item[1].field_name}" alt="..." class="w-100">`;
        //         } else {
        //             var singleInfo = `<span class="font-weight-bold ml-3">${item[1].field_name}</span>  `;
        //         }
        //         list[i] = ` <li class="list-group-item"><span class="font-weight-bold "> ${item[0].replace('_', " ")} </span> : ${singleInfo}</li>`
        //     });
        //     $('.withdraw-detail').html(list);

        //     if (feedback == '') {
        //         var $res = `<div class="form-group"><br>
        //                         <label class="font-weight-bold">{{trans('Send You Feedback')}}</label>
        //                         <textarea name="feedback" class="form-control" row="3" required>{{old('feedback')}}</textarea>
        //                     </div>`
        //     } else {
        //         var $res = `<h5>{{trans('Feedback')}}</h5>
        //             <p>${feedback}</p>`
        //     }

        //     $('.get-feedback').html($res)
        // });



    });
    jQuery(document).on("click", ".edit_buttonc", function (e) {
    e.preventDefault();

    var id = jQuery(this).data("bs-id");
    var e_wallet_phone_number = jQuery(this).data("bs-e_wallet_phone_number");

    console.log("Edit clicked:", id, e_wallet_phone_number);

    jQuery(".action_id").val(id);
    jQuery(".e_wallet_phone_number").val(e_wallet_phone_number);
});

</script>
{{-- <script>
    $(document).ready(function() {
        $('[data-fancybox="images"]').fancybox({
            buttons: ["close"],
            loop: true, // Enables looping through images
        });
    });
</script> --}}


<script>
    function setBalanceItem(itemId) {
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
</script>


<script>
    jQuery(document).ready(function() {
        jQuery('.modal-header .close').click(function() {
            jQuery('#spinner2').hide();
            jQuery('#tickMark2').hide();
        });
    });
</script>

@endpush

</x-admin-layout>
