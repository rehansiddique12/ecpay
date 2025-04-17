<x-admin-layout :title="$pageTitle">
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('admin.payout-report.search') }}" method="get">
            <div class="row justify-content-between align-items-center">
                <div class="col-md-3">
                    <div class="form-group mt-3">
                        <label>User</label>
                        <input type="text" name="name" value="{{@request()->name}}" class="form-control"
                               placeholder="@lang('Email/ Username')">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mt-3">
                        <label>From Date</label>
                        <input type="date" class="form-control" value="{{@request()->from_date}}" name="from_date" id="datepicker"/>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mt-3">
                        <label>To Date</label>
                        <input type="date" class="form-control" value="{{@request()->to_date}}" name="to_date" id="datepicker"/>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group mt-3">
                        <label>Transection No</label>
                        <input type="text" name="partner_transection_id" value="{{@request()->partner_transection_id}}" class="form-control" placeholder="@lang('Transection No.')">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group mt-3">
                        <label>User Account No</label>
                        <input type="text" class="form-control" value="{{@request()->account_no}}" name="account_no"/>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mt-3">
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
                    <div class="form-group mt-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">@lang('All Payment')</option>
                            <option value="1"
                                    @if(@request()->status == '1') selected @endif>@lang('Pending Payment')</option>
                            <option value="2"
                                    @if(@request()->status == '2') selected @endif>@lang('Complete Payment')</option>
                            <option value="3"
                                    @if(@request()->status == '3') selected @endif>@lang('Cancel Payment')</option>
                        </select>
                    </div>
                </div>






                <div class="col-md-3">
                    <div class="form-group mt-3">
                        <label for="">Domain</label>
                        <select name="domain" class="form-control">
                            <option value="">@lang('Select Domain')</option>
                            @foreach($domains as $domain)
                                <option value="{{ $domain->id }}"
                                @if(@request()->domain == $domain->id) selected @endif>{{ $domain->name }} ===> ( {{ $domain->website }} )</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group mt-3">
                        <button type="submit" class="btn waves-effect waves-light btn-primary"><i class="icon-base ti tabler-search me-1"></i>  @lang('Search')</button>
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
                                    <h2 class="text-dark mb-1 font-weight-medium">{{$fund_sum}}</h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('Total Withdrawal Amount')
                                </h6>
                            </div>
                            <div class="ml-auto mt-md-3 mt-lg-0">
                                <span class="opacity-7 text-muted"><i class="fa fa-hand-holding-usd"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
</div>


    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
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
                        @if(adminAccessRoute(config('role.payout_manage.access.edit')))
                            <th scope="col">@lang('More')</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($records as $key => $item)
                        <tr>
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
                                <a href="{{route('admin.user-edit',[$item->user_id])}}">
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
                                </a>
                                @else
                                @if($item->api)
                                {{ optional($item->api)->name }} <b>({{ optional($item->api)->acc_type }})</b>
                                @else
                                Partner Transection
                                @endif
                                @endif

                            </td>
                            <td data-label="@lang('Method')">{{ optional($item->payout)->user_account_no }}</td>
                            <td>{{ optional($item->method)->name }}</td>
                            <td data-label="@lang('Amount')"
                                class="font-weight-bold">{{ getAmount($item->amount ) }} {{ $basic->currency_symbol }}</td>
                            <td data-label="@lang('Charge')"
                                class="text-success">{{ getAmount(optional($item->payout)->charge,2) }} {{ $basic->currency_symbol }}</td>

                            <td data-label="@lang('Net Amount')"
                                class="font-weight-bold">{{ getAmount($item->net_amount) }} {{$basic->currency_symbol}}</td>

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
                                @endif
                                @endif
                            </td>
                            <td data-label="@lang('Method')">
                                {{ optional($item->payout)->e_wallet_phone_number }}
                                <br>
                                {{ optional($item->payout)->e_wallet_type }}
                            </td>
                            <td data-label="@lang('Method')">{{ optional($item->payout)->source }}</td>


                            @if(adminAccessRoute(config('role.payout_manage.access.edit')))
                                <td data-label="@lang('More')">
                                    @php
                                        $details = ($item->information != null) ? json_encode($item->information) : null;
                                    @endphp
                                    <button type="button" class="btn btn-primary btn-icon edit_button"
                                            data-bs-toggle="modal" data-bs-target="#myModal"
                                            data-route="{{route('admin.payout-action',$item->id)}}"
                                            data-feedback="{{$item->feedback}}"
                                            data-info="{{$details}}"
                                            data-id="{{$item->id}}"
                                            data-status="{{$item->status}}">
                                        @if(Request::routeIs('admin.payout-request'))
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
                {{ $records->appends($_GET)->links('partials.pagination') }}
            </div>
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

                <form role="form" method="POST" class="actionRoute" action="" enctype="multipart/form-data">
                    @csrf
                    @method('put')
                    <div class="modal-body">
                        <ul class="list-group withdraw-detail">
                        </ul>

                        @if(Request::routeIs('admin.payout-request'))

                            <div class="form-group addForm">

                            </div>
                        @endif

                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')
                        </button>
                        @if(Request::routeIs('admin.payout-request'))
                            <input type="hidden" class="action_id" name="id">
                            <button type="submit" class="btn btn-primary" name="status"
                                    value="2">@lang('Approve')</button>
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
        (function ($) {

            $(document).ready(function () {
                $(document).on("click", '.edit_button', function (e) {
                    var id = $(this).data('id');
                    $(".action_id").val(id);
                    $(".actionRoute").attr('action', $(this).data('route'));
                    var details = Object.entries($(this).data('info'));
                    var list = [];
                    var ImgPath = "{{asset(config('location.withdrawLog.path'))}}";
                    details.map(function (item, i) {
                        if (item[1].type == 'file') {
                            var singleInfo = `<br><img src="${ImgPath}/${item[1].field_name}" alt="..." class="w-50">`;
                        } else {
                            var singleInfo = `<span class="font-weight-bold ml-3">${item[1].field_name}</span>  `;
                        }
                        list[i] = ` <li class="list-group-item"><span class="font-weight-bold "> ${item[0].replace('_', " ")} </span> : ${singleInfo}</li>`
                    });


                    if ($(this).data('status') != '1') {
                        list[details.length + 1] = `<li class="list-group-item"><span class="font-weight-bold">@lang('Admin Feedback')</span> : <span">${$(this).data('feedback')}</span></li>`;
                        $('.addForm').html(``)
                    } else {
                        list[details.length + 1] = ``;
                        $('.addForm').html(`
                                <div class="form-group">
                                <label for="feedback">@lang('feedback')</label>
                                <textarea class="form-control" name="feedback"></textarea>
                                </div>
                        `);
                    }

                    $('.withdraw-detail').html(list);
                });
            });
        })(jQuery);


        $(document).ready(function () {
            $('select').select2({
                selectOnClose: true
            });
        });
    </script>
@endpush

</x-admin-layout>

