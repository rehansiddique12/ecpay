<x-admin-layout :title="$pageTitle">

    <div class="row">
        @if (adminAccessRoute(config('role.ewallet_transfer_balance.access.add')))
            <div class="col-md-12">
                <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
                    <h3 style="color: #7367f0">{{ __('partner.add_transfer_record') }}</h3>
                    <form action="{{ route('admin.transfer.balance.add') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row justify-content-between align-items-center">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('partner.select_category') }}</label>
                                    <select class="form-select" name="category" id="category" required>
                                        <option value="E-wallet to E-wallet">{{ __('partner.e_wallet_to_e_wallet') }}
                                        </option>
                                        <option value="Bank to E-wallet">{{ __('partner.bank_to_e_wallet') }}</option>
                                        <option value="E-wallet to Bank">{{ __('partner.e_wallet_to_bank') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" id="fromtransfer1">
                                    <label class="pr-3">{{ __('partner.transfer_from') }}</label>
                                    <select class="form-select select2" name="transfer_from1" data-allow-clear="true"
                                        data-placeholder="{{ __('partner.select_from_account') }}" required>
                                        <option></option>
                                        @foreach ($e_wallet_accounts as $e_wallet_account)
                                            <option value="{{ $e_wallet_account->id }}">
                                                {{ $e_wallet_account->account_no . ' (' . $e_wallet_account->e_wallet_name . ') ' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group" id="fromtransfer2" style="display:none">
                                    <label class="pr-3">{{ __('partner.transfer_from') }}</label>
                                    <input type="text" class="form-control" name="transfer_from2" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" id="totransfer1">
                                    <label class="pr-3">{{ __('partner.transfer_to') }}</label>
                                    <select class="form-select select2" name="transfer_to1" data-allow-clear="true"
                                        data-placeholder="{{ __('partner.select_to_account') }}">
                                        <option></option>
                                        @foreach ($e_wallet_accounts as $e_wallet_account)
                                            <option value="{{ $e_wallet_account->id }}">
                                                {{ $e_wallet_account->account_no . ' (' . $e_wallet_account->e_wallet_name . ') ' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group" id="totransfer2" style="display:none">
                                    <label class="pr-3">{{ __('partner.transfer_to') }}</label>
                                    <input type="text" class="form-control" name="transfer_to2" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('partner.transaction_no') }}</label>
                                    <input type="text" class="form-control" name="txn_id" required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('partner.amount') }}</label>
                                    <input type="number" class="form-control" name="amount" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('partner.charges') }}</label>
                                    <input type="number" class="form-control" name="charges" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('partner.commission') }}</label>
                                    <input type="number" class="form-control" name="comission" required />
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('partner.transfer_date_time') }}</label>
                                    <input type="datetime-local" class="form-control" value="<?php echo date('Y-m-d H:i:s'); ?>"
                                        name="transaction_date_time" id="datepicker" />
                                </div>
                            </div>



                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">{{ __('partner.receipt') }}</label>
                                    <input type="file" class="form-control" name="image">
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div class="form-group">
                                    <button id="submit-btn" type="submit"
                                        class="btn waves-effect waves-light btn-primary">{{ __('partner.submit') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        @endif
    </div>

    @if (adminAccessRoute(config('role.ewallet_transfer_balance.access.view')))
        <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
            <form action="{{ route('admin.transfer.balance') }}" method="get">
                <div class="row justify-content-between align-items-center">

                    <div class="col-md-10">
                        <div class="form-group">
                            <input type="date" class="form-control" name="from_date" value="{{ $from_date }}"
                                id="datepicker" />
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <button type="submit" class="btn waves-effect waves-light btn-primary">
                                <i class="icon-base ti tabler-search me-1"></i> {{ __('partner.search') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>

        <br>
        <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
            <div class="card-body">
                <h4 style="color: #7367f0">{{ __('partner.transfer_logs') }}</h4>
                <div class="table-responsive">
                    <table class="categories-show-table table table-hover table-striped table-bordered  table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">{{ __('partner.category') }}</th>
                                <th scope="col">{{ __('partner.e_wallet') }}</th>
                                <th scope="col">{{ __('partner.from_account') }}</th>
                                <th scope="col">{{ __('partner.to_account') }}</th>
                                <th scope="col">{{ __('partner.amount') }}</th>
                                <th scope="col">{{ __('partner.charges') }}</th>
                                <th scope="col">{{ __('partner.commission') }}</th>
                                <th scope="col">{{ __('partner.txn_id') }}</th>
                                <th scope="col">{{ __('partner.date_time') }}</th>
                                <th scope="col">{{ __('partner.receipt') }}</th>
                                <th scope="col">{{ __('partner.created_at') }}</th>
                                <th scope="col">{{ __('partner.updated_at') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($e_wallet_transections as $key => $item)
                                <tr>
                                    <td>{{ $item->category }}</td>
                                    <td>{{ $item->e_wallet }}</td>
                                    <td>{{ $item->from_account_no }}</td>
                                    <td>{{ $item->to_account_no }}</td>
                                    <td>{{ $item->amount }}</td>
                                    <td>{{ $item->charges }}</td>
                                    <td>{{ $item->comission }}</td>
                                    <td>{{ $item->txn_id }}</td>
                                    <td>{{ $item->transaction_date_time }}</td>
                                    <td>
                                        @if (!empty($item->image))
                                            <a data-fancybox="images"
                                                href="{{ getFile(config('location.receipts.path') . $item->image) }}">
                                                <h2><i class="fa fa-file"></i></h2>
                                            </a>
                                        @endif
                                    </td>
                                    <td>{{ $item->created_at }}</td>
                                    <td>{{ $item->updated_at }}</td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100%">
                                        <p class="text-dark">{{ __('partner.no_data_found') }} </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $e_wallet_transections->appends($_GET)->links('partials.pagination') }}
                </div>
            </div>
        </div>
    @endif
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
    @endpush

    @push('js')
        <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
        <script>
            $(document).ready(function() {
                $('#category').change(function() {
                    var selectedCategory = $(this).val();

                    if (selectedCategory === 'Bank to E-wallet') {
                        // Show fromtransfer2 and hide fromtransfer1
                        $('#fromtransfer2').show();
                        $('#fromtransfer1').hide();

                        // Show totransfer1 and hide totransfer2
                        $('#totransfer1').show();
                        $('#totransfer2').hide();
                    } else if (selectedCategory === 'E-wallet to Bank') {
                        // Show fromtransfer1 and hide fromtransfer2
                        $('#fromtransfer1').show();
                        $('#fromtransfer2').hide();

                        // Show totransfer2 and hide totransfer1
                        $('#totransfer2').show();
                        $('#totransfer1').hide();
                    } else if (selectedCategory === 'E-wallet to E-wallet') {
                        // Show fromtransfer1 and hide fromtransfer2
                        $('#fromtransfer1').show();
                        $('#fromtransfer2').hide();

                        // Show totransfer1 and hide totransfer2
                        $('#totransfer1').show();
                        $('#totransfer2').hide();
                    }
                });

                $('form').on('submit', function() {
                    const $btn = $(this).find('button[type="submit"]');
                    // Disable the button
                    $btn.prop('disabled', true);
                    // Optional: Change button text to show loading spinner
                    $btn.html('<i class="fa fa-spinner fa-spin me-1"></i> Submitting...');
                    return true; // allow form to submit
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
</x-admin-layout>
