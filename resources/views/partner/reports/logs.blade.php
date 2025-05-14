<x-partner-layout :title="$pageTitle">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
    th a {
        color:white !important;
        background: none !important;
    }
</style>
<div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
    <form action="{{ route('partner.reports.logs') }}" method="get">
        <div class="row justify-content-between align-items-center">
            <div class="col-md-3">
                <div class="form-group">
                    <label>From Date</label>
                    <input type="text" class="form-control datetimepicker" value="{{$from_date}}" name="from_date"/>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>To Date</label>
                    <input type="text" class="form-control datetimepicker" value="{{$to_date}}" name="to_date" />
                </div>
            </div>


            <div class="col-md-3">
                <div class="form-group mt-2">
                    <br>
                    <button type="submit" value="submit" class="btn waves-effect waves-light btn-primary"><i
                        class="icon-base ti tabler-search me-1"></i> @lang('Search')</button>
                    <a href="{{ route('partner.report.export_excel_record', ['from_date' => $from_date, 'to_date' => $to_date , 'order' => request('order') === 'asc' ? 'asc' : 'desc']) }}" class="btn waves-effect waves-light btn-success">
                        <i class="fas fa-file-export"></i> @lang('Export')
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
                <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
                <div class="table-responsive">

                    <table class="categories-show-table table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">
                                    Transaction Date
                                </th>
                                <th scope="col">
                                    Completed Date
                                </th>
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
                                <th scope="col">Transaction Type</th>
                            </tr>
                        </thead>
                        <tbody>

                            @if(isset($filter_data))

                            @forelse($filter_data as $key => $item)
                            <tr>
                                <td>{{ $item['txn_created_at'] }}</td>
                                <td>{{ $item['txn_updated_at'] }}</td>
                                <td>{{ $item['transection_id'] }}</td>
                                <td>{{ $item['partner_transection_id'] }}</td>
                                <td>{{ $item['sender'] }}</td>
                                <td>{{ $item['e_wallet_name'] }}</td>
                                <td>{{ $item['e_wallet_type'] }}</td>
                                <td>{{ $item['e_wallet_phone_number'] }}</td>
                                <td>{{ $item['amount'] }}</td>
                                <td>{{ $item['charge'] }}</td>
                                <td>{{ number_format($item['final_amount'], 2) }}</td>
                                <td>{{ number_format($item['balance'], 2) }}</td>
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
