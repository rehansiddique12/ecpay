
<x-admin-layout :title="$pageTitle">
    <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
      <div class="layout-container">

        <!-- / Navbar -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Content wrapper -->
          <div class="content-wrapper">

            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">
              <!-- Header -->
              <div class="row">
                <div class="col-12">
                  <div class="card mb-6">
                    <div class="user-profile-header-banner">
                      <img src="../../assets/img/pages/profile-banner.png" alt="Banner image" class="rounded-top img-fluid" />
                    </div>
                    <div class="user-profile-header d-flex flex-column flex-lg-row text-sm-start text-center mb-5">
                      <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
                        <img
                          src="../../assets/img/avatars/1.png"
                          alt="user image"
                          class="d-block h-auto ms-0 ms-sm-6 rounded user-profile-img" />
                      </div>
                      <div class="flex-grow-1 mt-3 mt-lg-5">
                        <div
                          class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-5 flex-md-row flex-column gap-4">
                          <div class="user-profile-info">
                            <h4 class="mb-2 mt-lg-6">{{$data->name}} </h4>

                          </div>
                          @php
    $depositColor = 'text-danger'; // default red

    if ($total_deposit > 60) {
        $depositColor = 'text-success'; // green
    } elseif ($total_deposit >= 40 && $total_deposit <= 60) {
        $depositColor = 'text-warning'; // yellow
    }
@endphp

                          <span>Live Bank Balance $ 0.00<br> Available Balance ${{$data->balance}}</span>

                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!--/ Header -->

              <!-- Navbar pills -->
              <div class="row">
                <div class="col-md-12">
                  <div class="nav-align-top">
                    <ul class="nav nav-pills flex-column flex-sm-row mb-6 gap-sm-0 gap-2">
                      <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.agent.profile',$data->id)}}"
                          ><i class="icon-base ti tabler-user-check icon-sm me-1_5"></i> Profile</a
                        >
                      </li>
                      <li class="nav-item">
                        <a class="nav-link active" href="{{route('admin.agent.logs',$data->id)}}"
                          ><i class="icon-base ti tabler-list icon-sm me-1_5"></i> Logs</a
                        >
                      </li>



                    </ul>
                  </div>
                </div>
              </div>
              <!--/ Navbar pills -->

              <!-- User Profile Content -->
              <div class="row">
                <div class="col-xl-4 col-lg-5 col-md-5">
                  <!-- About User -->
                  <div class="card mb-6">
                    <div class="card position-relative">


                        <div class="card-body">
                            <span><h4 class="mb-n3">Gateway performance</h4> <br> Deposit:   <span class="{{ $depositColor }}">{{ $total_deposit }}%</span> <br> Withdrawal:  <SPAN class="text-danger">##%</SPAN> </span>
                        </div>

                    </div>

                  </div>
                </div>
                <div class="col-xl-8 col-lg-7 col-md-7">
                  <!-- Activity Timeline -->
                  <div class="card card-action mb-6">
                    <div class="card-header align-items-center">



                          <div class="table-responsive">
                            <table class="categories-show-table table table-hover table-striped table-bordered">
                                <h5 class="card-action-title mb-0">
                                    Live Account Balance Log
                                   </h5>
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">Transection Id</th>
                                        <th scope="col">Transection Date</th>
                                        <th scope="col">Txn No.</th>
                                        <th scope="col">Partner Txn No.</th>
                                        <th scope="col">Account No.</th>
                                        <th scope="col">Source</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">E-Wallet Acc. No.</th>
                                        <th scope="col">Amount</th>
                                        <th scope="col">Charges</th>

                                        <th scope="col">Final Amount</th>
                                        <th scope="col">Balance</th>
                                        <th scope="col">Differance</th>
                                        <th scope="col">Transection Type</th>
                                        <th scope="col">Source</th>
                                        <th scope="col">Created At</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @if(isset($filter_data))

                                    @forelse($filter_data as $key => $item)
                                    <tr>

                                        <td>{{ $item['transection_id'] }}</td>
                                        <td>{{ $item['txn_created_at'] }}</td>
                                        <td>{{ $item['txn_id'] }}</td>
                                        <td>{{ $item['partner_transection_id'] }}</td>
                                        <td>{{ $item['sender'] }}</td>
                                        <td>{{ $item['e_wallet_name'] }}</td>
                                        <td>{{ $item['e_wallet_type'] }}</td>
                                        <td>{{ $item['e_wallet_phone_number'] }}</td>
                                        <td>{{ $item['amount'] }}</td>
                                        <td>{{ $item['charge'] }}</td>


                                        <td>{{ number_format($item['final_amount'], 2) }}</td>
                                        <td>{{ number_format($item['balance'], 2) }}</td>
                                        <?php

                                            $differance = 0;
                                            if(isset($filter_data[$key+1]['balance'])){
                                                $differance = $filter_data[$key+1]['balance'] + $item['final_amount'] - $item['balance'];
                                            }
                                            $differance = number_format($differance, 2);

                                            if (@request()->website && !empty(@request()->website)) {
                                                if($differance==0){
                                                    echo '<td>'.$differance.'</td>';
                                                }else{
                                                    echo '<td style="background-color: red;color:white">'.$differance.'</td>';
                                                }
                                            }else{
                                                echo '<td></td>';
                                            }

                                        ?>
                                        <td><?php
                                        if($item['transection_type']==1){
                                            echo "Deposit";
                                        }elseif($item['transection_type']==2){
                                            echo "Withdrawal";
                                        }elseif($item['transection_type']==3){
                                            echo "Adjustment";
                                        }elseif($item['transection_type']==4){
                                            echo "Settlement";
                                        }elseif($item['transection_type']==5){
                                            echo "Commission";
                                        }elseif($item['transection_type']==7){
                                            echo "Withdrawal Refunded";
                                        }else{
                                            echo $item['transection_type'];
                                        }
                                        ?></td>
                                        <td>{{ $item['source'] }}</td>
                                        <td>{{ $item['created_at'] }}</td>

                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="100%">
                                            <p class="text-dark">@lang('No Data Found')</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                    @endif
                                </tbody>
                            </table>
                        </div>



                    </div>

                  </div>

                </div>

                <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                    <div class="card mb-6">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="categories-show-table table table-hover table-striped table-bordered">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th scope="col">@lang('Date')</th>
                                            <th scope="col">@lang('Trx Number')</th>
                                            <th scope="col">@lang('Partner Trx No')</th>
                                            <th scope="col">@lang('Partner Txn Input')</th>
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
                                        @forelse($deposit_logs as $key => $fund)
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
                                            <td>{{ $fund->created_at }}</td>
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

                                                {{-- @if($fund->gateway_id > 999) --}}
                                                <button class="edit_button  btn  {{($fund->status == 2) ?  'btn-primary' : 'btn-success'}} text-white  btn-sm " data-bs-toggle="modal"
                                                     data-bs-target="#myModalDeposit"
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
                                                    <i class="icon-base ti tabler-eye me-1"></i>
                                                    @endif

                                                </button>
                                                {{-- @else --}}
                                                {{-- - --}}
                                                {{-- @endif --}}
                                                <button class="edit_buttonc  btn btn-danger text-white  btn-sm" data-bs-toggle="modal" data-bs-target="#myModalDepositc" data-bs-title="Edit" data-bs-id="{{ $fund->id }}" data-bs-e_wallet_phone_number="{{$fund->e_wallet_phone_number}}">
                                                   <i class="icon-base ti tabler-device-mobile me-1"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#myModalDepositb" onclick="setBalanceItem({{ $fund->id }})">
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
                                {{ $deposit_logs->appends($_GET)->links('partials.pagination') }}
                            </div>
                        </div>
                      </div>
                </div>

                <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                    <div class="card mb-6">
                        <div class="card-body">
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
                                        @if(adminAccessRoute(config('role.payout_manage.access.edit')))
                                            <th scope="col">@lang('More')</th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($withrawl_logs as $key => $item)
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            <td data-label="@lang('Date')"> {{ dateTime($item->created_at,'d M,Y H:i') }}</td>
                                             <td data-label="@lang('Trx Number')" class="font-weight-bold text-uppercase">
                                                {{ $item->trx_id }}<br>
                                                <span class="text text-success">{{ optional($item->payout)->txn_id }}</span>

                                            </td>
                                            <td>{{ optional($item->payout)->partner_transection_id }}
                                                <br>
                                                {{ optional($item->payout)->member_id }}
                                            </td>
                                            <td data-label="@lang('Username')">
                                                @if(optional($item->user)->username!="dummyuser")
                                                {{-- <a href="{{route('admin.user-edit',[$item->user_id])}}">
                                                    <div class="d-lg-flex d-block align-items-center ">
                                                        <div class="mr-3"><img
                                                                src="{{getFile(config('location.user.path').optional($item->user)->image) }}"
                                                                alt="user" class="rounded-circle" width="45"
                                                                height="45"></div>
                                                        <div class="">
                                                            <h5 class="text-dark mb-0 font-16 font-weight-medium">{{ optional($item->user)->username }}</h5>
                                                            <span class="text-muted font-14">{{ optional($item->user)->email }}</span>
                                                        </div>
                                                    </div>
                                                </a> --}}
                                                @else
                                                @if($item->api)
                                                {{ optional($item->api)->name }} <b>({{ optional($item->api)->acc_type }})</b>
                                                @else
                                                Partner Transection
                                                @endif
                                                @endif

                                            </td>
                                            <td>{{ optional($item->method)->name }}</td>
                                            <td>{{ $item->user_account_no }}</td>
                                            <td data-label="@lang('Amount')"
                                                class="font-weight-bold">{{ getAmount($item->amount,2 ) }} {{$basic->currency_symbol}}</td>
                                            <td data-label="@lang('Charge')"
                                                class="text-success">{{ getAmount(optional($item->payout)->charge,2) }} {{$basic->currency_symbol}}</td>

                                            <td data-label="@lang('Net Amount')"
                                                class="font-weight-bold">{{ getAmount($item->net_amount,2) }} {{$basic->currency_symbol}}</td>

                                            <td data-label="@lang('Status')" class="text-lg-center text-right">
                                                @if($item->status == 2)
                                                    <span class="badge badge-light"><i class="fa fa-circle text-success font-12"></i> @lang('Request Approved')</span>
                                                @elseif($item->status == 1)
                                                    <span class="badge badge-light"><i class="fa fa-circle text-warning font-12"></i> @lang('Request Pending')</span>
                                                @elseif($item->status == 3)
                                                    <span class="badge badge-light"><i class="fa fa-circle text-danger font-12"></i> @lang('Request Rejected')</span>
                                                @endif
                                                <br>
                                                @if($item->payout)
                                                @if($item->payout->status == "Complete")
                                                    <span class="badge badge-light"><i class="fa fa-circle text-success font-12"></i> @lang('Transfered')</span>
                                                @elseif($item->payout->status == "Pending")
                                                    <span class="badge badge-light"><i class="fa fa-circle text-warning font-12"></i> @lang('Transfer Pending')</span>
                                                @elseif($item->payout->status == "Reject")
                                                    <span class="badge badge-light"><i class="fa fa-circle text-danger font-12"></i> @lang('Transfer Rejected')</span>
                                                @else
                                                {{$item->payout->status}}
                                                @endif
                                                @endif
                                            </td>
                                            <td>
                                                {{$item->feedback}}
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
                                                            @if(adminAccessRoute(config('role.payout_manage.access.edit')))
                                                            <button type="button" class="btn btn-sm edit_button" data-bs-toggle="modal" data-bs-target="#myModalDepositb" onclick="setBalanceItem({{ $item->id }})">
                                                                <i class="icon-base ti tabler-report-money me-1"></i> Send Callback
                                                            </button><br>
                                                            @if(isset($item))
                                                            <button class="btn  edit_buttonc  btn-sm" data-bs-toggle="modal" data-bs-target="#myModalDepositc" data-title="Edit" data-id="{{ $item->id }}" data-e_wallet_phone_number="{{$item->e_wallet_phone_number}}">
                                                                <i class="icon-base ti tabler-device-mobile  me-1"></i> Change E-Wallet No
                                                            </button><br>
                                                            @endif
                                                            @php

                                                        $details = ($item->information != null) ? json_encode($item->information) : null;
                                                    @endphp
                                                    <button type="button" class="btn btn-sm  edit_button"
                                                            data-bs-toggle="modal" data-bs-target="#myModalDeposit"
                                                            data-route="{{route('admin.payout-action',$item->id)}}"
                                                            data-feedback="{{$item->feedback}}"
                                                            data-info="{{$details}}"
                                                            data-id="{{$item->id}}"
                                                            data-status="{{$item->status}}"
                                                            data-statusb="{{$item->status ? $item->status:''}}">
                                                        @if(Request::routeIs('admin.payout-request'))
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
                                {{ $withrawl_logs->appends($_GET)->links('partials.pagination') }}
                            </div>
                        </div>
                      </div>
                </div>
              </div>
              <!--/ User Profile Content -->
            </div>
            <!--/ Content -->




          </div>
          <!--/ Content wrapper -->
        </div>

        <!--/ Layout container -->
      </div>
    </div>

    <!-- Modal for Edit button -->
<div class="modal modal-top fade" id="myModalDeposit" tabindex="-1">
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
                <form role="form" method="POST" class="actionRoute" action="" enctype="multipart/form-data" onsubmit="submitForm(this)">
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

<div class="modal modal-top fade" id="myModalDepositc" tabindex="-1">
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



<div class="modal modal-top fade" id="myModalDepositb" tabindex="-1">
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
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

                    {{-- @if(Request::routeIs('admin.payout-request')) --}}

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
                        <button type="submit" id="btn4" class="btn btn-dark" name="status" value="4">@lang('Mark As
                            Complete')</button>
                    </div>
                    <div id="submit4" style="display: none;">
                        <button type="submit" id="btn5" class="btn btn-warning" name="status" value="5">@lang('Mark
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
function submitForm(form) {
    // Disable the submit button to prevent multiple submissions
    document.getElementById('approvebtn').disabled = true;

    // Submit the form
    form.submit();
}
</script>
<script>
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
</script>

<script>

    $(document).ready(function() {
        // $('select[name=status]').select2({
        //     selectOnClose: true
        // });

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

{{-- Withdrawal Logs script start here --}}

<script>
    document.addEventListener("DOMContentLoaded", function () {
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
        btn2.addEventListener("click", function (event) {
            event.preventDefault(); // Prevent default form submission
            handleButtonClick(2);
        });

        btn3.addEventListener("click", function (event) {
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

        btn4.addEventListener("click", function (event) {
            event.preventDefault(); // Prevent default form submission
            handleButtonClick(4);
        });

        btn5.addEventListener("click", function (event) {
            event.preventDefault(); // Prevent default form submission
            handleButtonClick(5);
        });
    });

</script>

<script>
    (function (jQuery) {

        jQuery(document).ready(function () {
            jQuery(document).on("click", '.edit_button', function (e) {
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

        jQuery(document).on("click", '.edit_buttonc', function (e) {
            var id = jQuery(this).data('id');
            var e_wallet_phone_number = jQuery(this).data('e_wallet_phone_number');

            jQuery(".action_id").val(id);
            jQuery(".e_wallet_phone_number").val(e_wallet_phone_number);
        });

    })(jQuery);

    jQuery(document).ready(function () {
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
            success: function (response) {
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
            error: function (xhr, status, error) {
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
    $(document).ready(function () {
        var intervalId; // To store the interval id
        var orderid = document.getElementById("orderid");
        var wid = document.getElementById("wid");
        var acc_no = document.getElementById("acc_no");



        $('#runWithdrawalTest').click(function () {
            if (acc_no.value === "") {
                alert("Please select an Admin Account");
                return;
            }

        });

        // Function to perform the AJAX call


        $('.modal-header .close').click(function () {
            $('#runWithdrawalTest').prop('disabled', false);
            $('#spinner2').hide();
            $('#tickMark2').hide();
        });
    });

</script>

<script>
    $(document).ready(function () {

        function fetchNotification() {
            var letest_record = document.getElementById("letest_record").value;
            $.ajax({
                url: "{{ route('admin.payout-report.getnotification') }}",
                type: "GET",
                data: {
                    letest_record: letest_record
                },
                success: function (response) {
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
                error: function (xhr) {
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


