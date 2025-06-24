@extends('partner.layouts.app')
@section('title')
    @lang($page_title)
@endsection
@section('content')
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('partner.payout-report.search') }}" method="get">
            <div class="row justify-content-between align-items-center">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>From Date</label>
                        <input type="date" class="form-control" value="{{@request()->from_date}}" name="from_date" id="datepicker"/>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>To Date</label>
                        <input type="date" class="form-control" value="{{@request()->to_date}}" name="to_date" id="datepicker"/>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>User Account No</label>
                        <input type="text" class="form-control" value="{{@request()->account_no}}" name="account_no" id="datepicker"/>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Transection No</label>
                        <input type="text" name="partner_transection_id" value="{{@request()->partner_transection_id}}" class="form-control" placeholder="@lang('Transection No.')">
                    </div>
                </div>
                
                
                <div class="col-md-5">
                    <div class="form-group">
                        <label>Bank</label>
                        <select name="gateway" class="form-control">
                            <option value="">All</option>
                            @foreach($gateways as $gateway)
                                <option value="{{ $gateway->name }}"
                                @if(@request()->gateway == $gateway->name) selected @endif>{{ $gateway->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                
                <!--<div class="col-md-3">-->
                <!--    <div class="form-group">-->
                <!--        <label>User</label>-->
                        
                <!--    </div>-->
                <!--</div>-->
                <input type="text" name="name" hidden value="{{@request()->name}}" class="form-control"
                               placeholder="@lang('Type Here')">

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="4"
                                    @if(@request()->status == '4') selected @endif>@lang('All Payment')</option>
                            <option value="2"
                                    @if(@request()->status == '2') selected @endif>@lang('Complete Payment')</option>
                            <option value="1"
                                    @if(@request()->status == '1') selected @endif>@lang('Pending Payment')</option>
                            <option value="3"
                                    @if(@request()->status == '3') selected @endif>@lang('Cancel Payment')</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label>ID</label>
                        <input type="text" name="id" value="{{@request()->id}}" class="form-control" placeholder="@lang('ID')">
                    </div>
                </div>

                

               
                <div class="col-md-2">
                    <div class="form-group">
                        <button type="submit" class="btn waves-effect waves-light btn-primary"><i
                                class="fas fa-search"></i> @lang('Search')</button>
                        <button type="submit" name="export" value="export" class="btn waves-effect waves-light btn-success"><i class="fas fa-download"></i> @lang('Export Data')</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

<!-- Add these lines to your HTML header section -->
<link rel="stylesheet" href="{{ asset('node_modules/@fancyapps/fancybox/dist/jquery.fancybox.min.css') }}">
<script src="{{ asset('node_modules/@fancyapps/fancybox/dist/jquery.fancybox.min.js') }}"></script>



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

            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">@lang('Date')</th>
                        <th scope="col">@lang('Trx Number')</th>
                        <th scope="col">@lang('Partner Trx No')</th>
                        <th scope="col">@lang('Username')</th>
                        <th scope="col">@lang('Method')</th>
                        <th scope="col">Acc. No.</th>
                        <th scope="col">@lang('Amount')</th>
                        <th scope="col">@lang('Charge')</th>
                        <th scope="col">@lang('Final Amount')</th>
                        <th scope="col">@lang('Type')</th>
                        <th scope="col">@lang('Status')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($funds as $key => $fund)
                        <tr>
                            <td> {{ $fund->id }}</td>
                            <td data-label="@lang('Date')"> {{ dateTime($fund->created_at,'d M,Y H:i') }}</td>
                        <td data-label="@lang('Trx Number')" class="font-weight-bold text-uppercase">
                            <?php
                            if (strpos($fund->transection_no, "Your transfer is successful") === 0) {
                                // Define the regular expression
                                $pattern = '/Reference No:(.*?)Date:/';

                                // Apply preg_match to extract the value
                                if (preg_match($pattern, $fund->transection_no, $matches)) {
                                    // Extracted value is in $matches[1]
                                    $referenceNo = trim($matches[1]);
                                    echo $referenceNo;
                                }
                            }elseif (strpos($fund->transection_no, "TRANSACTION REFERENCE") === 0) {
                                    // Define the regex pattern
                                    $pattern = '/TRANSACTION REFERENCE\s+(\d+)\s+Successful\./';

                                    // Apply preg_match to extract the value
                                    if (preg_match($pattern, $fund->transection_no, $matches)) {
                                        // Extracted value is in $matches[1]
                                        $referenceNo = $matches[1];
                                        echo $referenceNo;
                                    }
                            }elseif($fund->domain=="scb"){
                                echo $fund->transection_no;
                            }else{
                            // echo $fund->transection_no;
                            }
                                ?>
                        </td>
                        <td>{{ !empty($fund->partner_transection_id)?$fund->partner_transection_id:'' }}</td>
                        <td data-label="@lang('Username')">
                            {{ optional($fund->api)->name }} <b>({{ optional($fund->api)->acc_type }})</b>
                        </td>
                        <td data-label="@lang('Method')">{{ $fund->domain }}</td>
                        <td class="font-weight-bold">{{ $fund->acc_no }}</td>
                        <td data-label="@lang('Amount')" class="font-weight-bold">{{ getAmount($fund->amount) }} {{trans($basic->currency_symbol)}}</td>
                        <td data-label="@lang('Amount')" class="font-weight-bold">{{ getAmount($fund->charge) }} {{$fund->gateway_currency}}</td>
                        <td data-label="@lang('Amount')" class="font-weight-bold">{{ getAmount($fund->amount + $fund->charge) }} {{$fund->gateway_currency}}</td>
                        <td>
                             @if($fund->type == 1)
                            Payout
                            @elseif($fund->type == 2)
                            Fund Transfer
                            @endif
                        </td>
                        
                        


                        <td data-label="@lang('Status')" class="text-lg-center text-right">
                            @if($fund->status == 1)
                            <span class="badge badge-light"><i class="fa fa-circle text-warning warning font-12"></i> @lang('Pending')</span>
                            @elseif($fund->status == 2)
                            <span class="badge badge-light"><i class="fa fa-circle text-success success font-12"></i> @lang('Completed')</span>
                            @elseif($fund->status == 3)
                            <span class="badge badge-light"><i class="fa fa-circle text-danger danger font-12"></i> @lang('Rejected')</span>
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

    <!-- Modal for Edit button -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content ">
                <div class="modal-header modal-colored-header bg-primary">
                    <h4 class="modal-title" id="myModalLabel">@lang('Deposit Information')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
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
                        @if(Request::routeIs('partner.payment.pending'))
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
@endsection

@push('js')
    <script>
        "use strict";
        $(document).ready(function () {
            $('select[name=status]').select2({
                selectOnClose: true
            });

            $(document).on("click", '.edit_button', function (e) {
                var id = $(this).data('id');
                var feedback = $(this).data('feedback');

                $(".action_id").val(id);
                $(".actionRoute").attr('action', $(this).data('route'));
                var details = Object.entries($(this).data('info'));
                var list = [];
                details.map(function (item, i) {
                    if (item[1].type == 'file') {
                        var singleInfo = `<br><img src="${item[1].field_name}" alt="..." class="w-100">`;
                    } else {
                        var singleInfo = `<span class="font-weight-bold ml-3">${item[1].field_name}</span>  `;
                    }
                    list[i] = ` <li class="list-group-item"><span class="font-weight-bold "> ${item[0].replace('_', " ")} </span> : ${singleInfo}</li>`
                });
                $('.withdraw-detail').html(list);

                if (feedback == '') {
                    var $res = `<div class="form-group"><br>
                                <label class="font-weight-bold">{{trans('Send You Feedback')}}</label>
                                <textarea name="feedback" class="form-control" row="3" required>{{old('feedback')}}</textarea>
                            </div>`
                } else {
                    var $res = `<h5>{{trans('Feedback')}}</h5>
                    <p>${feedback}</p>`
                }

                $('.get-feedback').html($res)
            });
        });
    </script>
    <script>
    $(document).ready(function () {
        $('[data-fancybox="images"]').fancybox({
            buttons: ["close"],
            loop: true, // Enables looping through images
        });
    });
</script>
@endpush