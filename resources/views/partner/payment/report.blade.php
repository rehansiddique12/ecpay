<x-partner-layout :title="$pageTitle">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css">

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('partner.payment.report.search') }}" method="get">
            <div class="row justify-content-between align-items-center">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>@lang('partner_basic.from_date_label')</label>
                        <input type="text" class="form-control datetimepicker" autocomplete="off" value="{{ $from_date }}" name="from_date"
                             />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>@lang('partner_basic.to_date_label')</label>
                        <input type="text" class="form-control datetimepicker" autocomplete="off" value="{{ $to_date }}" name="to_date"
                            />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>@lang('partner_basic.user_account_no')</label>
                        <input type="text" class="form-control" autocomplete="off" value="{{ @request()->account_no }}" name="account_no"
                            id="datepicker" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>@lang('partner_basic.transaction_no')</label>
                        <input type="text" name="partner_transection_id" value="{{ @request()->partner_transection_id }}"
                            class="form-control" placeholder="@lang('Transection No.')">
                    </div>
                </div>


                <div class="col-md-5">
                    <div class="form-group">
                        <label>@lang('partner_basic.e_wallet_label')</label>
                        <select name="gateway" class="form-select">
                            <option value="">@lang('partner_basic.all_en')</option>
                            @foreach ($gateways as $gateway)
                                <option value="{{ $gateway->name }}" @if (@request()->gateway == $gateway->name) selected @endif>
                                    {{ $gateway->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <!--<div class="col-md-3">-->
                <!--    <div class="form-group">-->
                <!--        <label>User</label>-->

                <!--    </div>-->
                <!--</div>-->
                <input type="text" name="name" hidden value="{{ @request()->name }}" class="form-control "
                    placeholder="@lang('Type Here')">

                <div class="col-md-5">
                    <div class="form-group">
                        <label for="status">@lang('partner_basic.status')</label>
                        <select name="status" class="form-select">
                            <option value="All" @if (@request()->status == 'All') selected @endif>@lang('All Payment')
                            </option>
                            <option value="Complete" @if (@request()->status == 'Complete') selected @endif>@lang('Complete Payment')
                            </option>
                            <option value="Pending" @if (@request()->status == 'Pending') selected @endif>@lang('Pending
                                                                                                                                                                            Payment')
                            </option>
                            <option value="Reject" @if (@request()->status == 'Reject') selected @endif>@lang('Cancel
                                                                                                                                                                            Payment')
                            </option>
                        </select>

                    </div>
                </div>


                <div class="col-md-2">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-search me-1"></i> @lang('partner_basic.search')</button>
                        <button type="submit" name="export" value="export"
                            class="btn btn-success mt-2"><i class="icon-base ti tabler-download me-1"></i>
                            @lang('partner_basic.export')</button>
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
                                <h2 class="text-dark mb-1 font-weight-medium">{{ $fund_count }}</h2>
                            </div>
                            <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('partner_basic.total_transactions')
                            </h6>
                        </div>
                        <div class="ml-auto mt-md-3 mt-lg-0">
                            <span class="opacity-7 text-muted"><i class="fas fa-wallet fa-3x"></i></span>
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
                            <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">@lang('partner_basic.total_deposit_amount')
                            </h6>
                        </div>
                        <div class="ml-auto mt-md-3 mt-lg-0">
                            <span class="opacity-7 text-muted"><i class="fa fa-hand-holding-usd fa-3x"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">

            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="categories-show-table table table-hover table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">@lang('partner_basic.id')</th>
                                    <th scope="col">@lang('partner_basic.datetime')</th>
                                    <th scope="col">@lang('partner_basic.txn_no')</th>
                                    <th scope="col">@lang('partner_basic.partner_txn_no')</th>
                                    <th scope="col">@lang('partner_basic.partner_txn_input')</th>
                                    <th scope="col">@lang('partner_basic.type')</th>
                                    <th scope="col">@lang('partner_basic.code')</th>
                                    <th scope="col">@lang('partner_basic.user_account_no')</th>
                                    <th scope="col">@lang('partner_basic.amount')</th>
                                    <th scope="col">@lang('partner_basic.merchant_charges')</th>
                                    <th scope="col">@lang('partner_basic.final_amount')</th>
                                    <th scope="col">@lang('partner_basic.status')</th>
                                    <th scope="col">@lang('partner_basic.completed_at')</th>
                                    <th scope="col">@lang('partner_basic.receipt')</th>

                                </tr>
                            </thead>
                            <tbody>
                                @forelse($funds as $key => $fund)
                                    <tr>
                                        <td data-label="@lang('ID')"> {{ $fund->id }} </td>
                                        <td data-label="@lang('Date_Time')"> {{ dateTime($fund->created_at, 'd M,Y H:i') }}
                                        </td>
                                        <td data-label="@lang('Trx Number')" class="font-weight-bold text-uppercase">
                                            {{ $fund->transaction }}<br>
                                            <span class="text text-success">{{ $fund->txn_id }}</span>

                                        </td>
                                        <td>{{ !empty($fund->partner_transection_id) ? $fund->partner_transection_id : '' }}
                                            <br>
                                            {{ !empty($fund->member_id) ? $fund->member_id : '' }}
                                        </td>

                                        <td>
                                            {{ !empty($fund->txn_record) ? $fund->txn_record->txn_no : '' }}
                                        </td>


                                        <td data-label="@lang('Type')">{{ $fund->gateway?->category?->name ?? 'N/A' }}</td>
                                        <td data-label="@lang('Code')">{{ optional($fund->gateway)->name }}</td>
                                        <td class="font-weight-bold">{{ $fund->sender }}</td>
                                        <td data-label="@lang('Amount')" class="font-weight-bold">
                                            {{ getAmount($fund->amount) }}
                                            {{ $fund->gateway?->currency }}</td>
                                        <td data-label="@lang('Charge')" class="text-success">
                                            {{ getAmount($fund->charge) }}
                                            {{ $fund->gateway?->currency }}</td>
                                        <td data-label="@lang('Payable')" class="font-weight-bold">
                                            {{ getAmount($fund->amount) - getAmount($fund->charge) }}
                                            {{ $fund->gateway?->currency }}
                                        </td>

                                        <td data-label="@lang('Status')" class="text-lg-center text-right">
                                            @if ($fund->status == 'Pending')
                                                @php
                                                    // Get the time difference between now and the created_at timestamp
                                                    $createdAt = \Carbon\Carbon::parse($fund->created_at);
                                                    $currentTime = \Carbon\Carbon::now();
                                                    $diffInMinutes = $createdAt->diffInMinutes($currentTime);
                                                @endphp

                                                @if ($diffInMinutes > 10 && @request()->status != 'Pending')
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
                                            @elseif($fund->status == 'Complete')
                                                @php
                                                    // Check if the fund has a payment and if completed_source is set
                                                    if ($fund->completed_source != 'AdminPanel') {
                                                        // Dynamically assign the class based on completed_source
                                                        // if ($fund->payment->completed_source != "AdminPanel") {
                                                        $classColor = 'bg-success';
                                                        // } else {
                                                        // $classColor = "text-purple purple ";
                                                        // }
                                                    } else {
                                                        $classColor = 'bg-primary';
                                                    }
                                                @endphp


                                                <span class="badge {{ $classColor }}"><i
                                                        class="fa fa-circle text-white font-12"></i>
                                                    @lang('Completed')</span>
                                                <br>
                                                <span
                                                    class="{{ $classColor }}">{{ $fund->e_wallet_phone_number }}</span>
                                            @elseif($fund->status == 'Reject')
                                                <span class="badge bg-danger"><i
                                                        class="fa fa-circle text-white danger font-12"></i>
                                                    @lang('Rejected')</span>
                                                <br>
                                                <span class="text text-danger"> {{ $fund->e_wallet_phone_number }}</span>
                                            @endif
                                        </td>

                                        <td>{{ $fund->created_at }}</td>
                                        <td>
                                            @if (!empty($fund->receipt_image))
                                                <a data-fancybox="images"
                                                    href="{{ getFile(config('location.receipts.path') . $fund->receipt_image) }}">
                                                    <h2><i class="fa fa-file"></i></h2>
                                                </a>
                                            @endif
                                        </td>


                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%">
                                            <p class="text-dark">@lang('partner_basic.no_data_found')</p>
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
                        @if (Request::routeIs('partner.payment.pending'))
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

     <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js">
     </script>

    <script>
        "use strict";
        $(document).ready(function() {

            $('.datetimepicker').datetimepicker({
                format: 'Y-m-d H:i',
                step: 1,
                datepicker: true,
                timepicker: true
            });

            $('select[name=status]').select2({
                selectOnClose: true
            });

            $(document).on("click", '.edit_button', function(e) {
                var id = $(this).data('id');
                var feedback = $(this).data('feedback');

                $(".action_id").val(id);
                $(".actionRoute").attr('action', $(this).data('route'));
                var details = Object.entries($(this).data('info'));
                var list = [];
                details.map(function(item, i) {
                    if (item[1].type == 'file') {
                        var singleInfo =
                            `<br><img src="${item[1].field_name}" alt="..." class="w-100">`;
                    } else {
                        var singleInfo =
                            `<span class="font-weight-bold ml-3">${item[1].field_name}</span>  `;
                    }
                    list[i] =
                        ` <li class="list-group-item"><span class="font-weight-bold "> ${item[0].replace('_', " ")} </span> : ${singleInfo}</li>`
                });
                $('.withdraw-detail').html(list);

                if (feedback == '') {
                    var $res = `<div class="form-group"><br>
                                <label class="font-weight-bold">{{ trans('Send You Feedback') }}</label>
                                <textarea name="feedback" class="form-control" row="3" required>{{ old('feedback') }}</textarea>
                            </div>`
                } else {
                    var $res = `<h5>{{ trans('Feedback') }}</h5>
                    <p>${feedback}</p>`
                }

                $('.get-feedback').html($res)
            });
        });
    </script>
    <script>


        $(document).ready(function() {
            $('[data-fancybox="images"]').fancybox({
                buttons: ["close"],
                loop: true, // Enables looping through images
            });



        });
    </script>
@endpush

</x-partner-layout>
