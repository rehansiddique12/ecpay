<x-admin-layout :title="$pageTitle">
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('admin.payment.report.search') }}" method="get">
            <div class="row justify-content-between align-items-center">
                <div class="col-md-3">
                    <div class="form-group mt-2">
                        <label>From Date</label>
                        <input type="date" class="form-control" value="{{@request()->from_date}}" name="from_date" id="datepicker"/>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mt-2">
                        <label>To Date</label>
                        <input type="date" class="form-control" value="{{@request()->to_date}}" name="to_date" id="datepicker"/>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mt-2">
                        <label>User Account No</label>
                        <input type="text" class="form-control" value="{{@request()->account_no}}" name="account_no" id="datepicker"/>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mt-2">
                        <label>Source</label>
                        <select name="website" class="form-control">
                            <option value="">All Source</option>
                            @foreach($domains as $partner)
                                <option value="{{ $partner->id }}"
                                @if(@request()->website == $partner->id) selected @endif>{{ $partner->name }} ===> ( {{ $partner->website }} )</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group mt-2">
                        <label>E-Wallet</label>
                        <select name="gateway" class="form-control">
                            <option value="">All</option>
                            @foreach($gateways as $gateway)
                                <option value="{{ $gateway->name }}"
                                @if(@request()->gateway == $gateway->name) selected @endif>{{ $gateway->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mt-2">
                        <label>User</label>
                        <input type="text" name="name" value="{{@request()->name}}" class="form-control"
                               placeholder="@lang('Type Here')">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group mt-2">
                        <label>Payments</label>
                        <select name="status" class="form-control">
                            <option value="4"
                                    @if(@request()->status == '4') selected @endif>@lang('All Payment')</option>
                            <option value="1"
                                    @if(@request()->status == '1') selected @endif>@lang('Complete Payment')</option>
                            <option value="2"
                                    @if(@request()->status == '2') selected @endif>@lang('Pending Payment')</option>
                            <option value="3"
                                    @if(@request()->status == '3') selected @endif>@lang('Cancel Payment')</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mt-2">
                        <label>Transaction</label>
                        <input type="text" name="partner_transection_id" value="{{@request()->partner_transection_id}}" class="form-control" placeholder="@lang('Transection No.')">
                    </div>
                </div>


                <div class="col-md-3">
                    <div class="form-group mt-2">
                        <button type="submit" class="btn  btn-primary mt-2"><i class="icon-base ti tabler-search me-1"></i>  @lang('Search')</button>
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
                                    <h2 class="text-dark mb-1 font-weight-medium">{{$fund_count}}</h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('Total Transactions')
                                </h6>
                            </div>
                            <div class="ml-auto mt-md-3 mt-lg-0">
                                <span class="opacity-7 text-muted"><i class="icon-base ti tabler-wallet me-1"></i></span>



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
                                    <h2 class="text-dark mb-1 font-weight-medium">{{$fund_sum}}</h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('Total Deposit Amount')
                                </h6>
                            </div>
                            <div class="ml-auto mt-md-3 mt-lg-0">
                                <span class="opacity-7 text-muted"> <i class="icon-base ti tabler-currency-dollar me-1"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
</div>


    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
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
                        <th scope="col">@lang('Payable')</th>
                        <th scope="col">@lang('Status')</th>
                         <th scope="col">@lang('Source')</th>
                         <th scope="col">@lang('Receipt')</th>
                        @if(adminAccessRoute(config('role.payment_log.access.edit')))
                            <th scope="col">@lang('Action')</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($funds as $key => $fund)
                        <tr>
                            <td data-label="@lang('Date')"> {{ dateTime($fund->updated_at,'d M,Y H:i') }}</td>
                            <td data-label="@lang('Trx Number')" class="font-weight-bold text-uppercase">
                            {{ $fund->transaction }}<br>
                            <span class="text text-success">{{ optional($fund->payment)->txn_id }}</span>

                        </td>
                                <td>{{ $fund->partner_transection_id!=0?$fund->partner_transection_id:'' }}
                                    <br>
                            {{ !empty($fund->member_id)?$fund->member_id:'' }}
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
                            <td class="font-weight-bold">{{ $fund->account_no }}</td>
                            <td data-label="@lang('Method')">{{ optional($fund->gateway)->name }}</td>
                            <td data-label="@lang('Amount')"
                                class="font-weight-bold">{{ getAmount($fund->amount ) }} {{$fund->gateway->currency}}</td>
                            <td data-label="@lang('Charge')" class="text-success">{{ getAmount($fund->charge) }} {{$fund->gateway_currency}}</td>

                            <td data-label="@lang('Payable')"
                                class="font-weight-bold">{{ getAmount($fund->final_amount) }} {{$fund->gateway->currency}}</td>




                            <td data-label="@lang('Status')" class="text-lg-center text-right">
                            @if($fund->status == 2)
                            <span class="badge badge-light"><i class="fa fa-circle text-warning warning font-12"></i> @lang('Pending')</span>
                            <br>
                            <span class="text text-primary">{{ $fund->e_wallet_phone_number }}</span>
                            @elseif($fund->status == 1)
                            <span class="badge badge-light"><i class="fa fa-circle text-success success font-12"></i> @lang('Completed')</span>
                            <br>
                            <span class="text text-success">{{ optional($fund->payment)->e_wallet_phone_number }}</span>
                            @elseif($fund->status == 3)
                            <span class="badge badge-light"><i class="fa fa-circle text-danger danger font-12"></i> @lang('Rejected')</span>
                            <br>
                            <span class="text text-danger"> {{ $fund->e_wallet_phone_number }}</span>
                            @endif
                            <br>
                            {{ optional($fund->payment)->e_wallet_type }}
                        </td>
                            <td data-label="@lang('Method')">
                            {{ optional($fund->api)->website }}
                            <br>
                            @if(!empty($fund->source))
                            <span class="text text-dark">({{ $fund->source }})</span>
                            @endif
                        </td>
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
                                        <button
                                            class="edit_button   btn  {{($fund->status == 2) ?  'btn-primary' : 'btn-success'}} text-white  btn-sm "
                                            data-bs-toggle="modal" data-target="#myModal"
                                            data-bs-title="{{($fund->status == 2) ?  trans('Edit') : trans('Details')}}"

                                            data-id="{{ $fund->id }}"
                                            data-feedback="{{ $fund->feedback }}"
                                            data-info="{{json_encode($details)}}"
                                            data-amount="{{ getAmount($fund->amount)}} {{ $basic->currency }}"
                                            data-username="{{ optional($fund->user)->username }}"
                                            data-route="{{route('admin.payment.action',$fund->id)}}"
                                            data-status="{{$fund->status}}">

                                            @if(($fund->status == 2))
                                            <i class="icon-base ti tabler-pencil me-1"></i>
                                            @else
                                            <i class="icon-base ti tabler-eye me-1"></i>
                                            @endif

                                        </button>
                                    @else
                                        -
                                    @endif
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

                <form role="form" method="POST" class="actionRoute" action="" enctype="multipart/form-data">
                    @csrf
                    @method('put')
                    <div class="modal-body">
                        <ul class="list-group withdraw-detail">
                        </ul>

                        <div class="get-feedback">

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Close')</button>
                        @if(Request::routeIs('admin.payment.pending'))
                            <input type="hidden" class="action_id" name="id">
                            <button type="submit" class="btn btn-primary" name="status"
                                    value="1">@lang('Approve')</button>
                            <button type="submit" class="btn btn-danger" name="status"
                                    value="3">@lang('Reject')</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>


@push('js')
<script>
    jQuery(document).ready(function () {

        jQuery(document).on("click", '.edit_button', function (e) {
            var id = jQuery(this).data('id');
            var feedback = jQuery(this).data('feedback');

            jQuery(".action_id").val(id);
            jQuery(".actionRoute").attr('action', jQuery(this).data('route'));
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
</script>

@endpush
</x-admin-layout>
