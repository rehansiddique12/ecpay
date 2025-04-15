<x-admin-layout :title="$pageTitle">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
    th a {
        color:white !important;
        background: none !important;
    }
</style>
<div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
    <form action="{{ route('admin.reports.logs') }}" method="get">
        <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
        <div class="row justify-content-between align-items-center">
            <div class="col-md-3">
                <div class="form-group">
                    <label>From Date</label>
                    <input type="date" class="form-control" value="{{$from_date}}" name="from_date" id="datepicker" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>To Date</label>
                    <input type="date" class="form-control" value="{{$to_date}}" name="to_date" id="datepicker" />
                </div>
            </div>


            <div class="col-md-3">
                <div class="form-group">
                    <label>Source</label>
                    <select name="website" class="form-control">
                        <option value="">All Source</option>
                        @foreach($domains as $partner)
                        <option value="{{ $partner->id }}" @if(@request()->website == $partner->id) selected @endif>{{ $partner->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>


            <div class="col-md-3">
                <div class="form-group mt-2">
                    <br>
                    <button type="submit" value="submit" class="btn waves-effect waves-light btn-primary"><i class="icon-base ti tabler-search me-1"></i> @lang('Search')</button>
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
                    <table class="categories-show-table table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>

                                <th scope="col">id</th>
                                <th scope="col">Partner</th>
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

                                {{-- <th scope="col">
                                    <a href="{{ route('admin.reports.logs', array_merge(request()->all(), ['sort_by' => 'created_at', 'order' => request('order') === 'asc' ? 'desc' : 'asc'])) }}">
                                        Created At
                                        @if (request('sort_by') === 'created_at')
                                            @if (request('order') === 'asc')
                                                <i class="bi bi-caret-up-fill"></i>
                                            @else
                                                <i class="bi bi-caret-down-fill"></i>
                                            @endif
                                        @else
                                            <!-- Default state (no sorting) -->
                                            <i class="bi bi-caret-down-fill text-muted"></i> <!-- You can show a muted default arrow or no arrow -->
                                        @endif
                                    </a>
                                </th> --}}

                            </tr>
                        </thead>
                        <tbody>

                            @if(isset($filter_data))

                            @forelse($filter_data as $key => $item)
                            <tr>
                                <td>{{ $item['id'] }}</td>
                                <td>{{ $item['partner'] }}</td>
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
                                // if($orderval == "desc")
                                // {
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
                                // }

                                // if($orderval == "asc")
                                // {

                                //     $differance = 0;
                                //     if( $key > 0 && isset($filter_data[$key+1]['balance'])){
                                //         $f_amount = (float)$filter_data[$key+1]['final_amount'];
                                //         $differance = $filter_data[$key]['balance'] + $f_amount - $filter_data[$key + 1]['balance'];
                                //     }

                                //     $differance = number_format($differance, 2);

                                //     if (@request()->website && !empty(@request()->website)) {
                                //         if($differance==0){
                                //             echo '<td>'.$differance.'</td>';
                                //         }else{
                                //             echo '<td style="background-color: red;color:white">'.$differance.'</td>';
                                //         }
                                //     }else{
                                //         echo '<td></td>';
                                //     }
                                // }

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

</div>
@push('js')
@endpush
</x-admin-layout>
