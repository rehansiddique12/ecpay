<x-admin-layout :title="$pageTitle">
    @push('styles')
    <script src="{{ asset('public/assets/css/select2.min.css')}}"></script>
    <style>
        tr th{
          color: white !important
        }
    </style>
    @endpush

<div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
    <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
    <form action="{{ route('admin.settlements.search') }}" method="get">
        <div class="row justify-content-between align-items-center">


            <div class="col-md-4">
                <div class="form-group">
                    <label>From Date</label>
                    <input type="date" class="form-control" value="{{@request()->from_date}}" name="from_date" id="datepicker" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>To Date</label>
                    <input type="date" class="form-control" value="{{@request()->to_date}}" name="to_date" id="datepicker" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Partner</label>
                    <select name="partner" class="form-control">
                        <option value="">All</option>
                        @foreach($partners as $partner)
                        <option value="{{ $partner->id }}" @if(@request()->partner == $partner->id) selected @endif>{{ $partner->website }}</option>
                        @endforeach
                    </select>
                </div>
            </div>



            <div class="col-md-4  mt-4">
                <div class="form-group">
                    <label>E-Wallet</label>
                    <select name="gateway" class="form-control">
                        <option value="">All</option>
                        @foreach($gateways as $gateway)
                        <option value="{{ $gateway->source_name }}" @if(@request()->gateway == $gateway->source_name) selected @endif>{{ $gateway->source_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-4  mt-4">
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="">@lang('All')</option>
                        <option value="1" @if(@request()->status == '1') selected @endif>@lang('Approved')</option>
                        <option value="0" @if(@request()->status == '0') selected @endif>@lang('Pending')</option>
                        <option value="2" @if(@request()->status == '2') selected @endif>@lang('Rejected')</option>
                    </select>
                </div>
            </div>








            <div class="col-md-4 mt-3">
                <div class="form-group">
                    <br>
                    <button type="submit" class="btn waves-effect waves-light btn-primary"><i class="fas fa-search" style="margin-right: 10px;"></i> @lang('Search')</button>
                </div>
            </div>

        </div>
    </form>

</div>



<div class="row">
    <div class="col-md-12">
        <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
            <div class="card-body">



                <button type="button" class="btn btn-primary mb-4 hover:drop-shadow-xl" data-bs-toggle="modal" data-bs-target="#newModal">
                    Add New Settlement
                </button>

                <div class="table-responsive">
                    <table class="categories-show-table table table-hover table-striped table-bordered">
                        <thead class="thead-dark text-warning" style="background: var(--bs-menu-active-bg); color:#ffffff;">
                            <tr>

                                <th scope="col">@lang('Source')</th>
                                <th scope="col">@lang('Source Name')</th>
                                <th scope="col">@lang('Account No.')</th>
                                <th scope="col">@lang('Amount')</th>
                                <th scope="col">@lang('Charges')</th>
                                <th scope="col">@lang('Net Amount')</th>
                                <th scope="col">@lang('Status')</th>
                                <th scope="col">@lang('Partner')</th>
                                <th scope="col">Created At</th>
                                <th>Action</th>
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
                                        <i class="fa fa-circle text-danger danger font-12"></i> @lang('Rejected') </span>
                                    @elseif($item->status == 1)
                                    <span class="badge badge-light">
                                        <i class="fa fa-circle text-success success font-12"></i> @lang('Approved')</span>
                                    @else
                                    <span class="badge badge-light">
                                        <i class="fa fa-circle text-warning success font-12"></i> @lang('Pending')</span>
                                    @endif
                                </td>
                                <td>{{ $item->api->website }}</td>
                                <td>{{ $item->created_at }}</td>
                                <td data-label="@lang('Action')">
                                    <div class="dropdown show ">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="icon-base ti tabler-dots-vertical"></i>
                                    </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                            @if(adminAccessRoute(config('role.settlements.access.edit')))
                                            <form action="{{ route('admin.settlements.approve', $item['id']) }}" method="GET">
                                                <button type="submit" class="btn btn-sm btn-icon edit_button"><i class="fa fa-check"></i> Approve</button>
                                            </form>
                                            <form action="{{ route('admin.settlements.reject', $item['id']) }}" method="GET">
                                                <button type="submit" class="btn btn-sm btn-icon edit_button"><i class="fa fa-times"></i> Reject</button>
                                            </form>
                                            @endif
                                        </div>
                                    </div>
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
                </div>
                <div class="card-footer">
                    {{ $records->appends($_GET)->links('partials.pagination') }}
                </div>
            </div>
        </div>
    </div>

</div>



<div class="modal modal-top fade" id="newModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTopTitle">@lang('Add New')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.settlements.add') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row justify-content-between align-items-center">

            <div class="col-md-12">
                <div class="form-group">
                    <label>Partner</label>
                    <select name="partner" class="form-control" required>
                        @foreach($partners as $partner)
                        <option value="{{ $partner->id }}" @if(@request()->partner == $partner->id) selected @endif>{{ $partner->website }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label class="pr-3">Source</label>
                        <select class="form-control" name="source" required>
                            <option value="Bank">Bank</option>
                            <option value="EWallet">EWallet</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
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
                            <input type="number" step="0.01" class="form-control" name="amount" required/>
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
<script src="{{ asset('public/assets/js/select2.min.js')}}"></script>
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

    
</script>

@endpush
</x-admin-layout>
