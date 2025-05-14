<x-partner-layout :title="$pageTitle">
    <h1 class="text-center">
        <span class="badge badge-primary">Settlementable Amount: <b>{{ $settlementable_amount }} TK</b></span>
    </h1>

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('partner.settlements.search') }}" method="get">
            <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
            <div class="row justify-content-between align-items-center">

                <div class="col-md-5">
                    <div class="form-group">
                        <label>From Date</label>
                        <input type="date" class="form-control" value="{{ @request()->from_date }}" name="from_date"
                            id="datepicker" />
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label>To Date</label>
                        <input type="date" class="form-control" value="{{ @request()->to_date }}" name="to_date"
                            id="datepicker" />
                    </div>
                </div>
                <div class="col-md-2"></div>

                <!--<div class="col-md-4">-->
                <!--    <div class="form-group">-->
                <!--        <label>User Account No</label>-->
                <!--        <input type="text" class="form-control" value="{{ @request()->account_no }}" name="account_no"/>-->
                <!--    </div>-->
                <!--</div>-->

                <div class="col-md-5">
                    <div class="form-group">
                        <label>E-Wallet</label>
                        <select name="gateway" class="form-control">
                            <option value="">All</option>
                            @foreach ($gateways as $gateway)
                                <option value="{{ $gateway->source_name }}"
                                    @if (@request()->gateway == $gateway->source_name) selected @endif>{{ $gateway->source_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="all">@lang('All')</option>
                            <option value="1" @if (@request()->status == '1') selected @endif>@lang('Approved')
                            </option>
                            <option value="0" @if (@request()->status == '0') selected @endif>@lang('Pending')
                            </option>
                            <option value="2" @if (@request()->status == '2') selected @endif>@lang('Rejected')
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <br>
                        <button type="submit" class="btn waves-effect waves-light btn-primary"><i class="icon-base ti tabler-search me-1"></i> @lang('Search')</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                <div class="card-body">
                    <a href="javascript:void(0)" class="btn btn-sm btn-primary mr-2 mb-3 " data-target="#newModal"
                        data-toggle="modal">
                        <span>@lang('Add New')</span>
                    </a>
                    <div class="table-responsive">
                        <table class="categories-show-table table table-hover table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>

                                    <th scope="col">@lang('Source')</th>
                                    <th scope="col">@lang('Source Name')</th>
                                    <th scope="col">@lang('Account No.')</th>
                                    <th scope="col">@lang('Amount')</th>
                                    <th scope="col">@lang('Charges')</th>
                                    <th scope="col">@lang('Net Amount')</th>
                                    <th scope="col">@lang('Status')</th>
                                    <th scope="col">Created At</th>
                                    <!--<th>Action</th>-->
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $key => $item)
                                    <tr>
                                        <td>{{ $item->source }}</td>
                                        <td>{{ $item->source_name }}</td>
                                        <td>{{ $item->account_no }}</td>
                                        <td>{{ $item->amount }}</td>
                                        <td>{{ $item->charges }}</td>
                                        <td>{{ $item->net_amount }}</td>
                                        <td data-label="@lang('Status')" class="text-lg-center text-right">
                                            @if ($item->status == 2)
                                                <span class="badge badge-light">
                                                    <i class="fa fa-circle text-danger danger font-12"></i>
                                                    @lang('Rejected') </span>
                                            @elseif($item->status == 1)
                                                <span class="badge badge-light">
                                                    <i class="fa fa-circle text-success success font-12"></i>
                                                    @lang('Approved')</span>
                                            @else
                                                <span class="badge badge-light">
                                                    <i class="fa fa-circle text-warning success font-12"></i>
                                                    @lang('Pending')</span>
                                            @endif
                                        </td>
                                        <td>{{ convertToUserTimezone($item->created_at) }}</td>
                                        <!--<td data-label="@lang('Action')">-->
                                        <!--    <div class="dropdown show ">-->
                                        <!--        <a class="dropdown-toggle p-3" href="#" id="dropdownMenuLink" data-toggle="dropdown"-->
                                        <!--           aria-haspopup="true" aria-expanded="false">-->
                                        <!--            <i class="fa fa-ellipsis-v" aria-hidden="true"></i>-->
                                        <!--        </a>-->
                                        <!--        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">-->
                                        <!--            <form action="{{ route('admin.apis.delete', $item['id']) }}" method="POST">-->
                                        <!--                @csrf-->
                                        <!--                @method('DELETE')-->
                                        <!--                <button type="submit" class="btn btn-sm btn-icon edit_button"><i class="fa fa-trash"></i> Delete</button>-->
                                        <!--            </form>-->
                                        <!--            <button type="button" class="btn btn-sm btn-icon edit_button" data-toggle="modal" data-target="#editModal{{ $item['id'] }}">-->
                                        <!--                <i class="fa fa-edit"></i> Edit-->
                                        <!--            </button><br>-->
                                        <!--            <button type="button" class="btn btn-sm btn-icon edit_button" data-toggle="modal" data-target="#newModalb" onclick="setBalanceItem({{ $item['id'] }})">-->
                                        <!--                <i class="fa fa-money-bill"></i> Add Balance-->
                                        <!--            </button>-->
                                        <!--            <form action="{{ route('admin.apis.reset', $item['id']) }}" method="GET">-->
                                        <!--                <button type="submit" class="btn btn-sm btn-icon edit_button"><i class="fa fa-key"></i> Reset QR Code</button>-->
                                        <!--            </form>-->
                                        <!--            <form action="{{ route('admin.apis.commission', $item['id']) }}" method="GET">-->
                                        <!--                <button type="submit" class="btn btn-sm btn-icon edit_button"><i class="fa fa-calculator"></i> Commission %</button>-->
                                        <!--            </form>-->
                                        <!--        </div>-->
                                        <!--    </div>-->
                                        <!--</td>-->







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
                </div>
            </div>
        </div>

    </div>





    {{-- New MODAL --}}
    <div id="newModal" class="modal fade show" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header modal-colored-header bg-primary">
                    <h5 class="modal-title">@lang('Add New')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('partner.settlements.add') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row justify-content-between align-items-center">



                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Source</label>
                                    <select class="form-control" name="source" required>
                                        <option value="Bank">Bank</option>
                                        <option value="EWallet">EWallet</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Source Name</label>
                                    <input type="text" class="form-control" name="source_name" required />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Account No.</label>
                                    <input type="text" class="form-control" name="account_no" required />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Amount</label>
                                    <input type="number" step="0.01" class="form-control" name="amount"
                                        required />
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">@lang('Save')</button>
                        <button type="button" class="btn btn-dark" data-dismiss="modal">@lang('Close')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    @push('js')
        <script>
            "use strict";
            $(document).ready(function(e) {


                $('#image').change(function() {
                    let reader = new FileReader();
                    reader.onload = (e) => {
                        $('#image_preview_container').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(this.files[0]);
                });


            });

            $(document).ready(function() {
                $('select').select2({
                    selectOnClose: true
                });
            });
        </script>
    @endpush
</x-partner-layout>
