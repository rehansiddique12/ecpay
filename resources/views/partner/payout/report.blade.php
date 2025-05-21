<x-partner-layout :title="$pageTitle">
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('partner.payout-report.search') }}" method="get">
            <div class="row justify-content-between align-items-center">
                <!--<div class="col-md-3">-->
                <!--    <div class="form-group">-->
                <!--        <label>User</label>-->
                <!--        <input type="text" name="name" value="{{ @request()->name }}" class="form-control"-->
                <!--               placeholder="@lang('Email/ Username')">-->
                <!--    </div>-->
                <!--</div>-->
                <input type="text" hidden name="name" value="{{ @request()->name }}" class="form-control"
                    placeholder="@lang('Email/ Username')">

                <div class="col-md-3">
                    <div class="form-group">
                        <label>From Date</label>
                        <input type="text" class="form-control datetimepicker" autocomplete="off" value="{{ @request()->from_date }}"
                            name="from_date" id="datepicker" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>To Date</label>
                        <input type="text" class="form-control datetimepicker" autocomplete="off" value="{{ @request()->to_date }}"
                            name="to_date" id="datepicker" />
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>User Account No</label>
                        <input type="text" class="form-control" value="{{ @request()->account_no }}" name="account_no" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Transection No</label>
                        <input type="text" name="partner_transection_id" value="{{ @request()->partner_transection_id }}"
                            class="form-control" placeholder="@lang('Transection No.')">
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="form-group">
                        <label>E-Wallet</label>
                        <select name="gateway" class="form-select">
                            <option value="">All</option>
                            @foreach ($gateways as $gateway)
                                <option value="{{ $gateway->name }}" @if (@request()->gateway == $gateway->name) selected @endif>
                                    {{ $gateway->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-select">
                            <option value="">@lang('All Payment')</option>
                            <option value="1" @if (@request()->status == '1') selected @endif>@lang('Pending Payment')
                            </option>
                            <option value="2" @if (@request()->status == '2') selected @endif>@lang('Complete Payment')
                            </option>
                            <option value="3" @if (@request()->status == '3') selected @endif>@lang('Cancel Payment')
                            </option>
                        </select>
                    </div>
                </div>








                <div class="col-md-2">
                    <div class="form-group">
                        <br>
                        <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-search me-1"></i> @lang('Search')</button>
                        <button type="submit" name="export" value="export"
                            class="btn btn-success mt-2"><i class="icon-base ti tabler-download me-1"></i>
                            @lang('Export Data')</button>
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
                                <h2 class="text-dark mb-1 font-weight-medium">{{ $fund_count }}</h2>
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
                                <h2 class="text-dark mb-1 font-weight-medium">{{ $fund_sum }}</h2>
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
                            <th scope="col">@lang('User Account')</th>
                            <th scope="col">@lang('Method')</th>
                            <th scope="col">@lang('Amount')</th>
                            <th scope="col">@lang('Merchant Charge')</th>
                            <th scope="col">@lang('Net Amount')</th>
                            <th scope="col">@lang('Request Status')</th>
                            <th scope="col">@lang('Remarks')</th>
                            <th scope="col">@lang('Sent From')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $key => $item)
                            <tr>
                                <td data-label="@lang('Date')"> {{ convertToUserTimezone($item->created_at) }}</td>
                                <td data-label="@lang('Trx Number')" class="font-weight-bold text-uppercase">
                                    {{ $item->trx_id }}<br>
                                    <span class="text text-success">{{ $item->txn_id }}</span>

                                </td>
                                <td>{{ $item->partner_transection_id }}
                                    <br>
                                    {{ $item->member_id }}
                                </td>
                                <td data-label="@lang('Method')">{{ $item->user_account_no }}</td>
                                <td>{{ $item->gateway->name }}</td>
                                <td data-label="@lang('Amount')" class="font-weight-bold">
                                    {{ getAmount($item->amount) }} {{ $basic->currency_symbol }}</td>
                                <td data-label="@lang('Charge')" class="text-success">
                                    {{ $item->charge }} {{ $basic->currency_symbol }}</td>
                                <!--<td data-label="@lang('Charge')" class="text-success">-->
                                <?php
                                // if(isset($item->payout->charge)){
                                //     if(!empty($item->payout->source)){
                                //         echo "5%";
                                //     }

                                // }
                                ?>
                                <!--</td>-->
                                <td data-label="@lang('Net Amount')" class="font-weight-bold">
                                    {{ getAmount($item->net_amount) }} {{ $basic->currency_symbol }}</td>

                                <td data-label="@lang('Status')" class="text-lg-center text-right">
                                    @if ($item->status == 2)
                                        <span class="badge badge-light"><i class="fa fa-circle text-success font-12"></i>
                                            @lang('Request Approved')</span>
                                    @elseif($item->status == 1)
                                        <span class="badge badge-light"><i class="fa fa-circle text-warning font-12"></i>
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
                                                    class="fa fa-circle text-danger font-12"></i> @lang('Transfer Rejected')</span>
                                        @endif
                                    @endif
                                </td>

                                <td>{{ $item->feedback }}</td>

                                <td data-label="@lang('Method')">
                                    {{ $item->e_wallet_phone_number }}
                                    <br>
                                    {{ $item->e_wallet_type }}
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

                        @if (Request::routeIs('partner.payout-request'))
                            <div class="form-group addForm">

                            </div>
                        @endif

                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Close')
                        </button>
                        @if (Request::routeIs('partner.payout-request'))
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
    <!-- jQuery UI -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

    <!-- DateTimePicker Add-on -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js">
    </script>


    <script>
        (function($) {
            "use strict";

            $(document).ready(function() {
                $(document).on("click", '.edit_button', function(e) {
                    var id = $(this).data('id');
                    $(".action_id").val(id);
                    $(".actionRoute").attr('action', $(this).data('route'));
                    var details = Object.entries($(this).data('info'));
                    var list = [];
                    var ImgPath = "{{ asset(config('location.withdrawLog.path')) }}";
                    details.map(function(item, i) {
                        if (item[1].type == 'file') {
                            var singleInfo =
                                `<br><img src="${ImgPath}/${item[1].field_name}" alt="..." class="w-50">`;
                        } else {
                            var singleInfo =
                                `<span class="font-weight-bold ml-3">${item[1].field_name}</span>  `;
                        }
                        list[i] =
                            ` <li class="list-group-item"><span class="font-weight-bold "> ${item[0].replace('_', " ")} </span> : ${singleInfo}</li>`
                    });


                    if ($(this).data('status') != '1') {
                        list[details.length + 1] =
                            `<li class="list-group-item"><span class="font-weight-bold">@lang('Partner Feedback')</span> : <span">${$(this).data('feedback')}</span></li>`;
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


        $(document).ready(function() {
            $('select').select2({
                selectOnClose: true
            });

            $('.datetimepicker').datetimepicker({
                format: 'Y-m-d H:i',
                step: 1,
                datepicker: true,
                timepicker: true
            });

        });
    </script>

@endpush


</x-partner-layout>
