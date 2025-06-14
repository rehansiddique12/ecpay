<x-admin-layout :title="$pageTitle">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css"
        rel="stylesheet">
    <style>
        th a {
            color: white !important;
            background: none !important;
        }
    </style>
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('admin.reports.logs') }}" method="get">
            <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
            <div class="row justify-content-between align-items-center">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('reports.from_date') }}</label>
                        <input type="date" class="form-control" value="{{ $from_date }}" name="from_date"
                            id="datepicker" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('reports.to_date') }}</label>
                        <input type="date" class="form-control" value="{{ $to_date }}" name="to_date"
                            id="datepicker" />
                    </div>
                </div>


                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('reports.source') }}</label>
                        <select name="website" class="form-select select2" data-allow-clear="true"
                            data-placeholder="{{ __('reports.select_domain') }}">
                            <option></option>
                            <option value="">{{ __('reports.all_source') }}</option>
                            @foreach ($domains as $partner)
                                <option value="{{ $partner->id }}" @if (@request()->website == $partner->id) selected @endif>
                                    {{ $partner->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <div class="col-md-3">
                    <div class="form-group mt-2">
                        <br>
                        <button type="submit" value="submit" class="btn waves-effect waves-light btn-primary"><i
                                class="icon-base ti tabler-search me-1"></i>{{ __('reports.search') }}</button>
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

                                    <th scope="col">{{ __('reports.id') }}</th>
                                    <th scope="col">{{ __('reports.partner') }}</th>
                                    <th scope="col">{{ __('reports.transection_id') }}</th>
                                    <th scope="col">{{ __('reports.transection_date') }}</th>
                                    <th scope="col">{{ __('reports.txn_no') }}</th>
                                    <th scope="col">{{ __('reports.partner_txn_no') }}</th>
                                    <th scope="col">{{ __('reports.account_no') }}</th>
                                    <th scope="col">{{ __('reports.source') }}</th>
                                    <th scope="col">{{ __('reports.type') }}</th>
                                    <th scope="col">{{ __('reports.ewallet_account_no') }}</th>
                                    <th scope="col">{{ __('reports.amount') }}</th>
                                    <th scope="col">{{ __('reports.charges') }}</th>
                                    <th scope="col">{{ __('reports.final_amount') }}</th>
                                    <th scope="col">{{ __('reports.balance') }}</th>
                                    <th scope="col">{{ __('reports.difference') }}</th>
                                    <th scope="col">{{ __('reports.transection_type') }}</th>
                                    <th scope="col">{{ __('reports.source') }}</th>
                                    <th scope="col">{{ __('reports.created_at') }}</th>
                                </tr>
                            </thead>
                            <tbody>

                                @if (isset($filter_data))

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
                                            if (isset($filter_data[$key + 1]['balance'])) {
                                                $differance = $filter_data[$key + 1]['balance'] + $item['final_amount'] - $item['balance'];
                                            }
                                            $differance = number_format($differance, 2);
                                            
                                            if (@request()->website && !empty(@request()->website)) {
                                                if ($differance == 0) {
                                                    echo '<td>' . $differance . '</td>';
                                                } else {
                                                    echo '<td style="background-color: red;color:white">' . $differance . '</td>';
                                                }
                                            } else {
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
                                            if ($item['transection_type'] == 1) {
                                                echo 'Deposit';
                                            } elseif ($item['transection_type'] == 2) {
                                                echo 'Withdrawal';
                                            } elseif ($item['transection_type'] == 3) {
                                                echo 'Adjustment';
                                            } elseif ($item['transection_type'] == 4) {
                                                echo 'Settlement';
                                            } elseif ($item['transection_type'] == 5) {
                                                echo 'Commission';
                                            } elseif ($item['transection_type'] == 7) {
                                                echo 'Withdrawal Refunded';
                                            } else {
                                                echo $item['transection_type'];
                                            }
                                            ?></td>
                                            <td>{{ $item['source'] }}</td>
                                            <td>{{ $item['created_at'] }}</td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%">
                                                <p class="text-dark">{{ __('reports.no_data_found') }}</p>
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
        <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
        <script>
            $(document).ready(function() {
                $('form').on('submit', function() {
                    const $form = $(this);
                    const $submitButton = $form.find('button[type="submit"]');

                    // Disable button and change text (optional)
                    $submitButton.prop('disabled', true);
                    $submitButton.html(
                        "<i class='fa fa-spinner fa-spin me-1'></i> {{ __('reports.processing') }}");

                    // Allow form to proceed
                    return true;
                });
                let $select = $('.select2').select2({
                    // placeholder: "Select Partner",
                    allowClear: true,
                    selectOnClose: true,
                });

                // Prevent dropdown from opening on clear
                $select.on('select2:unselecting', function(e) {
                    $(this).data('unselecting', true);
                });

                $select.on('select2:opening', function(e) {
                    if ($(this).data('unselecting')) {
                        $(this).removeData('unselecting');
                        e.preventDefault();
                    }
                });
            });
        </script>
    @endpush
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
    @endpush
</x-admin-layout>
