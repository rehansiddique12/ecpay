<x-admin-layout :title="$pageTitle">
<div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
    <form action="{{ route('admin.reports.cal') }}" method="get">
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
                    <select name="website" class="form-select select2" data-allow-clear="true" data-placeholder="Select Domain">
                            <option></option>
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
                                <th scope="col">Transection Type</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $differance = 0;
                                ?>
                            @if(isset($filter_data))
                            @forelse($filter_data as $key => $item)
                            <tr>
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
                                <?php

                                $differance += $item['final_amount'];
                                $balance_to_show = number_format($differance, 2);
                                echo '<td>'.$balance_to_show.'</td>';

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

@push('js')
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script>
    $(document).ready(function () {
        $('form').on('submit', function () {
            const $form = $(this);
            const $submitButton = $form.find('button[type="submit"]');

            // Disable button and change text (optional)
            $submitButton.prop('disabled', true);
            $submitButton.html('<i class="fa fa-spinner fa-spin me-1"></i> @lang("Processing...")');

            // Allow form to proceed
            return true;
        });
       let $select = $('.select2').select2({
                // placeholder: "Select Partner",
                allowClear: true,
                selectOnClose: true,
            });

            // Prevent dropdown from opening on clear
            $select.on('select2:unselecting', function (e) {
                $(this).data('unselecting', true);
            });

            $select.on('select2:opening', function (e) {
                if ($(this).data('unselecting')) {
                    $(this).removeData('unselecting');
                    e.preventDefault();
                }
            });
    });
</script>
    @endpush
    @push('styles')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}">
    @endpush
</x-admin-layout>
