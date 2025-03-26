<x-admin-layout :title="$pageTitle">

    <div class="row">
        @if(adminAccessRoute(config('role.ewallet_transfer_balance.access.add')))
        <div class="col-md-12">
            <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
                <h3 style="color: #7367f0">Add Transfer Record</h3>
                <form action="{{ route('admin.transfer.balance.add') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row justify-content-between align-items-center">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="pr-3">Select Category</label>
                                <select class="form-control" name="category" id="category" required>
                                    <option value="E-wallet to E-wallet">E-wallet to E-wallet</option>
                                    <option value="Bank to E-wallet">Bank to E-wallet</option>
                                    <option value="E-wallet to Bank">E-wallet to Bank</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group" id="fromtransfer1">
                                <label class="pr-3">Transfer From</label>
                                <select class="form-control" name="transfer_from1">
                                    @foreach ($e_wallet_accounts as $e_wallet_account)

                                    <option value="{{ $e_wallet_account->id }}">{{ $e_wallet_account->account_no." (".$e_wallet_account->e_wallet_name.") " }}</option>

                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" id="fromtransfer2" style="display:none">
                                <label class="pr-3">Transfer From</label>
                                <input type="text" class="form-control" name="transfer_from2" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group" id="totransfer1">
                                <label class="pr-3">Transfer To</label>
                                <select class="form-control" name="transfer_to1">
                                    @foreach ($e_wallet_accounts as $e_wallet_account)

                                    <option value="{{ $e_wallet_account->id }}">{{ $e_wallet_account->account_no." (".$e_wallet_account->e_wallet_name.") " }}</option>

                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" id="totransfer2" style="display:none">
                                <label class="pr-3">Transfer To</label>
                                <input type="text" class="form-control" name="transfer_to2" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="pr-3">Transection No.</label>
                                <input type="text" class="form-control" name="txn_id" required>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="pr-3">Amount</label>
                                <input type="number" class="form-control" name="amount" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="pr-3">Charges</label>
                                <input type="number" class="form-control" name="charges" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="pr-3">Commission</label>
                                <input type="number" class="form-control" name="comission" required />
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="pr-3">Transfer Date Time</label>
                                <input type="datetime-local" class="form-control" value="<?php echo date('Y-m-d H:i:s');?>" name="transaction_date_time" id="datepicker" />
                            </div>
                        </div>



                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">Reciept</label>
                                <input type="file" class="form-control" name="image">
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" class="btn waves-effect waves-light btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
        @endif
    </div>

    @if(adminAccessRoute(config('role.ewallet_transfer_balance.access.view')))
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('admin.transfer.balance') }}" method="get">
            <div class="row justify-content-between align-items-center">


                <div class="col-md-10">
                    <div class="form-group">
                        <input type="date" class="form-control" name="from_date" value="{{$from_date}}" id="datepicker" />
                    </div>
                </div>



                <div class="col-md-2">
                    <div class="form-group">
                        <button type="submit" class="btn waves-effect waves-light btn-primary">
                            <i class="fas fa-search" style="margin-right: 10px;"></i> @lang('Search')
                        </button>
                    </div>
                </div>
            </div>
        </form>

    </div>

    <br>
    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <h4 style="color: #7367f0">Transfer Logs</h4>
            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">@lang('Category')</th>
                            <th scope="col">@lang('E-Wallet')</th>
                            <th scope="col">@lang('From Account')</th>
                            <th scope="col">@lang('To Account')</th>
                            <th scope="col">@lang('Amount')</th>
                            <th scope="col">@lang('Charges')</th>
                            <th scope="col">@lang('Commission')</th>
                            <th scope="col">@lang('Txn Id')</th>
                            <th scope="col">@lang('Date-Time')</th>
                            <th scope="col">@lang('Receipt')</th>
                            <th scope="col">@lang('Created At')</th>
                            <th scope="col">@lang('Updated At')</th>
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
                                @if(!empty($item->image))
                                <a data-fancybox="images" href="{{ getFile(config('location.receipts.path').$item->image) }}">
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
                                <p class="text-dark">@lang('No Data Found')</p>
                            </td>
                        </tr>

                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $e_wallet_accounts->appends($_GET)->links('partials.pagination') }}
            </div>
        </div>
    </div>
    @endif


    @push('js')
    <script>
    $(document).ready(function () {
        $('#category').change(function () {
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
    });

        </script>
    @endpush
    </x-admin-layout>
