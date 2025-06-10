<x-admin-layout :title="$pageTitle">
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
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('Total Deposit Amount')
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
            <b>Date:</b>{{$heading['date']}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>Status:</b>{{$heading['status']}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>E-Wallet Name:</b>{{$heading['gateway']}}
            <br><br>
            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">@lang('Date')</th>
                        <th scope="col">@lang('Trx Number')</th>
                        <th scope="col">@lang('Username')</th>
                        <th scope="col">@lang('User Account')</th>
                        <th scope="col">@lang('Method')</th>
                        <th scope="col">@lang('Amount')</th>
                        <th scope="col">@lang('Merchant Charge')</th>
                        <th scope="col">@lang('Payable')</th>
                        <th scope="col">@lang('E-Wallet No')</th>
                        <th scope="col">@lang('Type')</th>
                        <th scope="col">@lang('Status')</th>
                         <th scope="col">@lang('Source')</th>
                         <th scope="col">@lang('Receipt')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($funds as $key => $fund)
                        <tr>
                            <td data-label="@lang('Date')"> {{ dateTime($fund->created_at,'d M,Y H:i') }}</td>
                            <td data-label="@lang('Trx Number')"
                                class="font-weight-bold text-uppercase">{{ $fund->txn_id }}</td>
                            <td data-label="@lang('Username')">
                                @if($fund->user->username != null && optional($fund->user)->username!="dummyuser")
                                    <div class="d-lg-flex d-block align-items-center ">
                                        <div class="mr-3"><img
                                                src="{{getFile(config('location.user.path').optional($fund->user)->image) }}"
                                                alt="user"
                                                class="rounded-circle" width="45" height="45"></div>
                                        <div class="">
                                            <h5 class="text-dark mb-0 font-16 font-weight-medium">{{ optional($fund->user)->username }}</h5>
                                            <span class="text-muted font-14">{{ optional($fund->user)->email }}</span>
                                        </div>
                                    </div>
                                 @else
                                Partner Transaction
                                @endif
                            </td>
                            <td data-label="@lang('Method')">{{ $fund->sender }}</td>
                            <td data-label="@lang('Method')">{{ optional($fund->gateway)->name }}</td>
                            <td data-label="@lang('Amount')"
                                class="font-weight-bold">{{ getAmount($fund->amount ) }} {{$fund->gateway?->currency}}</td>
                            <td data-label="@lang('Charge')"
                                class="text-success">{{ getAmount($fund->charge,2) }} {{$fund->gateway?->currency}}</td>

                            <td data-label="@lang('Payable')"
                                class="font-weight-bold">{{ getAmount($fund->amount - $fund->charge) }} {{$fund->gateway?->currency}}</td>

                                <td data-label="@lang('Method')">{{ $fund->e_wallet_phone_number	 }}</td>
                                <td data-label="@lang('Method')">{{ $fund->e_wallet_type }}</td>


                            <td data-label="@lang('Status')" class="text-lg-center text-right">
                                @if($fund->status == "Pending")
                                    <span class="badge bg-warning"><i
                                            class="fa fa-circle text-white warning font-12"></i> @lang('Pending')</span>
                                @elseif($fund->status == "Complete")
                                    <span class="badge bg-success"><i
                                            class="fa fa-circle text-white success font-12"></i> @lang('Approved')</span>
                                @elseif($fund->status == 'Reject')
                                    <span class="badge bg-danger"><i
                                            class="fa fa-circle text-white danger font-12"></i> @lang('Rejected')</span>
                                @endif
                            </td>
                            <td data-label="@lang('Method')">{{ $fund->request_source }}</td>
                            <td>
                                @if(!empty($fund->receipt_image))
                                <a data-fancybox="images" href="{{ getFile(config('location.receipts.path').$fund->receipt_image) }}">
                                    <h2><i class="fa fa-file"></i></h2>
                                </a>
                                @endif
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
                {{ $funds->appends($_GET)->links('partials.pagination') }}
            </div>
        </div>
    </div>



@push('js')
    <script>
        "use strict";
        $(document).ready(function () {
        $('[data-fancybox="images"]').fancybox({
            buttons: ["close"],
            loop: true, // Enables looping through images
        });
    });
    </script>

@endpush
</x-admin-layout>
