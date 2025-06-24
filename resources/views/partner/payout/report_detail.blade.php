@extends('partner.layouts.app')
@section('title')
    @lang($page_title)
@endsection
@section('content')
    
    <br>
    <br>
    <br>
    <br>
    
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
            <b>Date:</b>{{$heading['date']}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>Status:</b>{{$heading['status']}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>Bank Name:</b>{{$heading['gateway']}}
            <br><br>
            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">@lang('Date')</th>
                        <th scope="col">@lang('Trx Number')</th>
                        <th scope="col">@lang('User Account')</th>
                        <th scope="col">@lang('Method')</th>
                        <th scope="col">@lang('Amount')</th>
                        <th scope="col">@lang('Merchant Charge')</th>
                        <th scope="col">@lang('Net Amount')</th>
                        <th scope="col">@lang('Status')</th>
                        <!--<th scope="col">@lang('Transfer Status')</th>-->
                        <th scope="col">@lang('Sent From')</th>
                        <th scope="col">@lang('Account Type')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($records as $key => $item)
                        <tr>
                            <td data-label="@lang('Date')"> {{ dateTime($item->created_at,'d M,Y H:i') }}</td>
                            <td>{{ optional($item->payout)->txn_id }}</td>
                            
                            <td data-label="@lang('Method')">{{ optional($item->payout)->user_account_no }}</td>
                            <td>{{ optional($item->method)->name }}</td>
                            <td data-label="@lang('Amount')"
                                class="font-weight-bold">{{ getAmount($item->amount ) }} {{ $basic->currency_symbol }}</td>
                            <td data-label="@lang('Charge')"
                                class="text-success">{{ optional($item->payout)->charge }} {{$basic->currency_symbol}}</td>
                                
                            <td data-label="@lang('Net Amount')"
                                class="font-weight-bold">{{ getAmount($item->net_amount) }} {{$basic->currency_symbol}}</td>

                            <td data-label="@lang('Status')" class="text-lg-center text-right">
                                @if($item->status == 2)
                                    <span class="badge badge-light"><i class="fa fa-circle text-success font-12"></i> @lang('Completed')</span>
                                @elseif($item->status == 1)
                                    <span class="badge badge-light"><i class="fa fa-circle text-warning font-12"></i> @lang('Pending')</span>
                                @elseif($item->status == 3)
                                    <span class="badge badge-light"><i class="fa fa-circle text-danger font-12"></i> @lang('Rejected')</span>
                                @endif
                            </td>
                            <!--<td data-label="@lang('Method')">{{ optional($item->payout)->status }}</td>-->
                            <td data-label="@lang('Method')">{{ optional($item->payout)->e_wallet_phone_number }}</td>
                            <td data-label="@lang('Method')">{{ optional($item->payout)->e_wallet_type }}</td>
                            


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
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content ">
                <div class="modal-header modal-colored-header bg-primary">
                    <h4 class="modal-title" id="myModalLabel">@lang('Payout Information')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>

                <form role="form" method="POST" class="actionRoute" action="" enctype="multipart/form-data">
                    @csrf
                    @method('put')
                    <div class="modal-body">
                        <ul class="list-group withdraw-detail">
                        </ul>

                        @if(Request::routeIs('partner.payout-request'))

                            <div class="form-group addForm">

                            </div>
                        @endif

                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Close')
                        </button>
                        @if(Request::routeIs('partner.payout-request'))
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

@endsection
@push('js')
    <script>
        (function ($) {
            "use strict";

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
                        list[details.length + 1] = `<li class="list-group-item"><span class="font-weight-bold">@lang('Partner Feedback')</span> : <span">${$(this).data('feedback')}</span></li>`;
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

