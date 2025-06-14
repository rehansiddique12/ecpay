<x-partner-layout :title="$pageTitle">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
    th a {
        color:white !important;
        background: none !important;
    }
</style>
<div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
    <form action="{{ route('partner.reports.log_completions') }}" method="get">
        <div class="row justify-content-between align-items-center">

             <div class="col-md-4">
                <div class="form-group">
                    <label>@lang('partner_basic.from_date_label')</label>
                    <input type="text" class="form-control datetimepicker" value="{{$from_date}}" name="from_date"/>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>@lang('partner_basic.to_date_label')</label>
                    <input type="text" class="form-control datetimepicker" value="{{$to_date}}" name="to_date" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mt-2">
                    <br>
                    <button type="submit" value="submit" class="btn waves-effect waves-light btn-primary"><i
                                class="icon-base ti tabler-search me-1"></i> @lang('partner_basic.search')</button>
                    <a href="{{ route('partner.report.export_excel_record_completions', ['from_date' => $from_date, 'to_date' => $to_date , 'order' => request('order') === 'asc' ? 'asc' : 'desc']) }}" class="btn waves-effect waves-light btn-success">
                        <i class="icon-base ti tabler-download me-1"></i> @lang('partner_basic.export')
                    </a>
                </div>
            </div>

        </div>
    </form>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
            <div class="card-body">
                <div class="table-responsive">
                            <h3 style="color: #7367f0">{{ __('partner_basic.Transactions_Completions_Logs_en')}}</h3>
                    <table class="categories-show-table table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">
                                    @lang('partner_basic.Transaction_Date_en')
                                </th>
                                <th scope="col">
                                    <a href="{{ route('partner.reports.log_completions', array_merge(request()->all(), ['sort_by' => 'updated_at', 'order' => request('order') === 'asc' ? 'desc' : 'asc'])) }}">
                                        @lang('partner_basic.Completed_Date_en')
                                        @if (request('sort_by') === 'updated_at')
                                            @if (request('order') === 'asc')
                                                <i class="bi bi-caret-up-fill"></i>
                                            @else
                                                <i class="bi bi-caret-down-fill"></i>
                                            @endif
                                        @else
                                            <i class="bi bi-caret-down-fill text-muted"></i> <!-- Default unsorted icon (optional) -->
                                        @endif
                                    </a>
                                </th>
                                <th scope="col">@lang('partner_basic.Txn_No_en')</th>
                                <th scope="col">@lang('partner_basic.Partner_Txn_No_en')</th>
                                <th scope="col">@lang('partner_basic.Account_No_en')</th>
                                <th scope="col">@lang('partner_basic.Source_en')</th>
                                <th scope="col">@lang('partner_basic.Type_en')</th>
                                <th scope="col">@lang('partner_basic.E_Wallet_Acc_No_en')</th>
                                <th scope="col">@lang('partner_basic.Amount_en')</th>
                                <th scope="col">@lang('partner_basic.Charges_en')</th>
                                <th scope="col">@lang('partner_basic.Final_Amount_en')</th>
                                <th scope="col">@lang('partner_basic.Balance_en')</th>
                                <th scope="col">@lang('partner_basic.Transaction_Type_en')</th>

                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($final_data))
                            @if(request('order')=="asc")
                            @php $balance = $closing_balance + 0; @endphp
                            @forelse($final_data as $key => $item)
                            @php
                            $balance += $item['final_amount'];
                            @endphp
                            <tr>
                                <td>{{ convertToUserTimezone($item['txn_created_at']) }}</td>
                                <td>{{ convertToUserTimezone($item['updated_at']) }}</td>
                                <td>{{ $item['transection_id'] }}</td>
                                <td>{{ $item['partner_transection_id'] }}</td>
                                <td>{{ $item['sender'] }}</td>
                                <td>{{ $item['e_wallet_name'] }}</td>
                                <td>{{ $item['e_wallet_type'] }}</td>
                                <td>{{ $item['e_wallet_phone_number'] }}</td>
                                <td>{{ $item['amount'] }}</td>
                                <td>{{ $item['charge'] }}</td>
                                <td>{{ number_format($item['final_amount'], 2) }}</td>
                                <td>{{ number_format($balance, 2) }} </td>


                                <td>
                                    <?php
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
                                    ?>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="100%">
                                    <p class="text-dark">@lang('partner_basic.no_data_found')</p>
                                </td>
                            </tr>
                            @endforelse
                            @else
                            @php $balance = $closing_balance + $total_amount;  @endphp
                            @forelse($final_data as $key => $item)

                            <tr>
                                <td>{{ convertToUserTimezone($item['txn_created_at']) }}</td>
                                <td>{{ convertToUserTimezone($item['updated_at']) }}</td>
                                <td>{{ $item['transection_id'] }}</td>
                                <td>{{ $item['partner_transection_id'] }}</td>
                                <td>{{ $item['sender'] }}</td>
                                <td>{{ $item['e_wallet_name'] }}</td>
                                <td>{{ $item['e_wallet_type'] }}</td>
                                <td>{{ $item['e_wallet_phone_number'] }}</td>
                                <td>{{ $item['amount'] }}</td>
                                <td>{{ $item['charge'] }}</td>
                                <td>{{ number_format($item['final_amount'], 2) }}</td>
                                <td>{{ number_format($balance ?? 0, 2) }} </td>

                                <td>
                                    <?php
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
                                    ?>
                                </td>
                            </tr>
                            @php

                                $balance -= $item['final_amount'];

                            @endphp
                            @empty
                            <tr>
                                <td colspan="100%">
                                    <p class="text-dark">@lang('partner_basic.no_data_found')</p>
                                </td>
                            </tr>
                            @endforelse
                            @endif
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

@push('js')
<!-- jQuery UI -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<!-- DateTimePicker Add-on -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>

<script>
    $(document).ready(function() {
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
